<?php
include('../koneksi.php'); // Memanggil koneksi.php
include('header.php');

// Cek apakah pengguna sudah login dan merupakan admin
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'Admin') {
    header("Location: ../index.php");
    exit;
}

// Ambil semua pengguna untuk dropdown
$query_all_users = "SELECT user_id, namalengkap FROM user";
$stmt_all_users = $connection->prepare($query_all_users);
$stmt_all_users->execute();
$all_users_result = $stmt_all_users->get_result();

// Mendapatkan nilai pencarian dari formulir jika ada
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

// Ambil data pengguna dengan pencarian jika ada
if ($searchTerm) {
    $query = "SELECT * FROM user WHERE user_id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $searchTerm);
    $stmt->execute();
    $users_result = $stmt->get_result();
} else {
    // Jika tidak ada pencarian, ambil semua pengguna
    $query = "SELECT * FROM user";
    $stmt = $connection->prepare($query);
    $stmt->execute();
    $users_result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pengguna</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<style>
    .input-group input {
        border-radius: 20px 0 0 20px;
        /* Mengatur border-radius untuk input */
    }

    .input-group .btn {
        border-radius: 0 20px 20px 0;
        /* Mengatur border-radius untuk tombol */
    }

    /* Styling untuk tombol */
    .btn-warning:hover {
        background-color: #e0a800;
        transform: scale(1.05);
    }

    .btn-danger:hover {
        background-color: #c82333;
        transform: scale(1.05);
    }

    .btn-primary:hover {
        background-color: #0056b3;
        transform: scale(1.05);
    }
</style>

<body>
    <div class="container mt-5">
        <h2 class="mb-4"><i class="fas fa-users"></i> Daftar Pengguna</h2>

        <!-- Form Pencarian Dropdown -->
        <form method="GET" class="mb-4">
            <div class="input-group">
                <select name="search" class="form-control" required>
                    <option value="">Pilih Pengguna...</option>
                    <?php while ($user = $all_users_result->fetch_assoc()): ?>
                        <option value="<?= $user['user_id'] ?>" <?= ($user['user_id'] == $searchTerm) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($user['namalengkap']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary"> <i class="fas fa-search"></i> Cari</button>
                </div>
            </div>
        </form>

        <table class="table table-striped">
            <thead class="thead-light">
                <tr>
                    <th>Nama Lengkap</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Alamat</th>
                    <th>Level</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = mysqli_fetch_object($users_result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($user->namalengkap) ?></td>
                        <td><?= ucwords(strtolower($user->username)) ?></td>
                        <td><?= htmlspecialchars($user->email) ?></td>
                        <td><?= htmlspecialchars($user->alamat) ?></td>
                        <td><?= htmlspecialchars($user->level) ?></td>
                        <td>
                            <a href="edit_user.php?id=<?= $user->user_id ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form method="POST" action="delete_user.php" style="display:inline-block;">
                                <input type="hidden" name="id" value="<?= $user->user_id ?>">
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <a href="manage_users.php" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Tambah Pengguna
        </a>
    </div>
</body>

</html>