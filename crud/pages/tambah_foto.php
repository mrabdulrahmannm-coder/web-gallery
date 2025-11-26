<?php
include('../koneksi.php'); 
include('header.php');

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    echo "<script>
            alert('Anda harus login terlebih dahulu.');
            window.location.href = '../index.php'; 
          </script>";
    exit;    
}

$user_id = $_SESSION['user_id']; // Ambil user_id dari session
$album_list = [];

// Cek level user untuk mengetahui apakah dia Admin atau bukan
$level = $_SESSION['level']; // Ambil level dari session (misal: Admin atau User)

// Jika user adalah admin, ambil semua album
if ($level === 'Admin') {
    $query = "SELECT album_id, namaalbum FROM album";
} else {
    // Jika user bukan admin, ambil hanya album milik user tersebut
    $query = "SELECT album_id, namaalbum FROM album WHERE user_id = ?";
}

$stmt = $connection->prepare($query);

if ($level !== 'Admin') {
    $stmt->bind_param("i", $user_id); // Hanya bind_param jika user bukan admin
}

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $album_list[] = $row; // Simpan data album
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
    $judul = $_POST['judulfoto'];
    $description = $_POST['deskripsifoto'];
    $album_id = $_POST['album_id']; // Menggunakan album_id dari dropdown
    $tanggalunggah = date('Y-m-d');

    $photo_url = basename($_FILES['photo']['name']);
    $target_file = '../uploads/' . $photo_url;
    $file_extension = strtolower(pathinfo($photo_url, PATHINFO_EXTENSION));
    $allowed_types = ['jpg', 'jpeg', 'png'];

    // Cek apakah judul foto sudah ada
    $checkJudulQuery = "SELECT COUNT(*) FROM foto WHERE judulfoto = ?";
    $stmtCheckJudul = $connection->prepare($checkJudulQuery);
    $stmtCheckJudul->bind_param("s", $judul);
    $stmtCheckJudul->execute();
    $stmtCheckJudul->bind_result($countJudul);
    $stmtCheckJudul->fetch();
    $stmtCheckJudul->close();

    // Cek apakah foto dengan nama yang sama sudah ada
    $checkFotoQuery = "SELECT COUNT(*) FROM foto WHERE lokasifile = ?";
    $stmtCheckFoto = $connection->prepare($checkFotoQuery);
    $stmtCheckFoto->bind_param("s", $photo_url);
    $stmtCheckFoto->execute();
    $stmtCheckFoto->bind_result($countFoto);
    $stmtCheckFoto->fetch();
    $stmtCheckFoto->close();

    if ($countJudul > 0) {
        echo "<script>
                alert('Judul foto sudah ada, silakan gunakan judul yang berbeda.');
                window.location.href = 'index.php'; 
              </script>";
        exit;
    } elseif ($countFoto > 0) {
        echo "<script>
                alert('Foto dengan nama yang sama sudah ada, silakan ganti nama file.');
                window.location.href = 'index.php'; 
              </script>";
        exit;
    }

    if (in_array($file_extension, $allowed_types) && getimagesize($_FILES['photo']['tmp_name'])) {
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
            $stmt = $connection->prepare("INSERT INTO foto (user_id, album_id, lokasifile, deskripsifoto, judulfoto, tanggalunggah) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iissss", $user_id, $album_id, $photo_url, $description, $judul, $tanggalunggah);
            $stmt->execute();
            // Pop-up pemberitahuan dan redirect ke halaman index.php
            echo "<script>
                    alert('Foto berhasil ditambahkan!');
                    window.location.href = 'index.php';
                  </script>";
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Foto</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="http://localhost/crud/css/tmb_foto.css">
</head>
<style>
   body {
    background-color: #f0f0f0; /* Latar belakang terang */
    color: #333; /* Teks berwarna gelap */
    overflow: hidden; /* Mencegah scrollbar muncul saat animasi */
}

.container {
    background-color: rgba(255, 255, 255, 0.9); /* Latar belakang transparan terang untuk kontainer */
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Shadow ringan */
}

h2 {
    color: #333; /* Teks judul berwarna gelap */
}

.form-control, .form-control-file, .btn-success {
    transition: 0.3s ease-in-out;
    background-color: #f9f9f9; /* Warna latar belakang input lebih terang */
    color: #333; /* Warna teks input lebih gelap */
}

.form-control:focus, .form-control-file:focus {
    box-shadow: 0 0 8px rgba(0, 123, 255, 0.8);
    border-color: #007bff;
}

.btn-success {
    background-color: #28a745;
    border: none;
    color: white;
}

.btn-success:hover {
    background-color: #218838;
    transform: scale(1.05);
    box-shadow: 0 6px 15px rgba(40, 167, 69, 0.4);
}

.btn-success:active {
    background-color: #1e7e34;
    transform: scale(1);
    box-shadow: none;
}
</style>
<body>
    <div class="container mt-4">
        <h2 class="mb-4">Tambah Foto</h2>
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Unggah Foto</label>
                <input type="file" class="form-control-file" name="photo" required>
            </div>
            <div class="form-group">
                <label>Judul Foto</label>
                <input type="text" class="form-control" name="judulfoto" required>
            </div>
            <div class="form-group">
                <label>Deskripsi Foto</label>
                <input type="text" class="form-control" name="deskripsifoto" required>
            </div>
            <div class="form-group">
                <label>Nama Album</label>
                <select class="form-control" name="album_id" required>
                    <option value="">Pilih Album</option>
                    <?php foreach ($album_list as $album): ?>
                        <option value="<?= htmlspecialchars($album['album_id']) ?>"><?= htmlspecialchars($album['namaalbum']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-success"><i class="fas fa-upload"></i> Tambah Foto</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php include('../footer.php'); ?>
