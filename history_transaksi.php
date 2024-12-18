<?php
// Mulai session
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Arahkan ke halaman login jika belum login
    exit;
}

// Koneksi ke database
include 'koneksi.php';

// Query untuk menampilkan daftar transaksi dengan menggabungkan data produk untuk mendapatkan harga beli
$sql = "
    SELECT transaksi.id, transaksi.nama_produk, transaksi.harga, transaksi.jumlah_terjual, transaksi.total_harga, transaksi.tanggal, produk.harga_beli
    FROM transaksi
    INNER JOIN produk ON transaksi.nama_produk = produk.nama
    ORDER BY transaksi.id DESC
";
$result = $conn->query($sql);

// Query untuk menghitung total pendapatan dari semua transaksi
$sql_total_pendapatan = "SELECT SUM(total_harga) AS total_pendapatan FROM transaksi";
$result_pendapatan = $conn->query($sql_total_pendapatan);
$row_pendapatan = $result_pendapatan->fetch_assoc();
$total_pendapatan = $row_pendapatan['total_pendapatan'];

// Menghitung total keuntungan
$total_keuntungan = 0;
while ($row = $result->fetch_assoc()) {
    // Hitung keuntungan per transaksi
    $keuntungan = ($row['harga'] - $row['harga_beli']) * $row['jumlah_terjual'];
    $total_keuntungan += $keuntungan; // Menambahkan keuntungan per transaksi ke total keuntungan
}

// Reset pointer hasil query untuk tabel transaksi
$result->data_seek(0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Transaksi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        <h1>History Transaksi</h1>

        <!-- Tabel History Transaksi -->
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nama Produk</th>
                        <th>Harga Jual</th>
                        <th>Harga Beli</th>
                        <th>Jumlah Terjual</th>
                        <th>Total Harga</th>
                        <th>Keuntungan</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php
                            // Hitung keuntungan
                            $keuntungan = ($row['harga'] - $row['harga_beli']) * $row['jumlah_terjual'];
                        ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['nama_produk']; ?></td>
                            <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                            <td>Rp <?php echo number_format($row['harga_beli'], 0, ',', '.'); ?></td>
                            <td><?php echo $row['jumlah_terjual']; ?></td>
                            <td>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                            <td>Rp <?php echo number_format($keuntungan, 0, ',', '.'); ?></td>
                            <td><?php echo $row['tanggal']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Menampilkan Total Pendapatan dan Total Keuntungan -->
        <div class="total-pendapatan">
            <h1></h1>
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Total Pendapatan</th>
                        <th>Total Keuntungan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></td>
                        <td>Rp <?php echo number_format($total_keuntungan, 0, ',', '.'); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>


        <a href="index.php" class="back-button">Kembali ke Dashboard</a>
    </div>

</body>
</html>
