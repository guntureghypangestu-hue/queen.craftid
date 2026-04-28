<?php
// Konfigurasi Database
 $host = 'localhost';
 $dbname = 'toko_bunga';
 $username = 'root'; // Ganti dengan username DB Anda
 $password = '';     // Ganti dengan password DB Anda

// Membuat koneksi PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Set mode error PDO ke Exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Hentikan skrip dan tampilkan pesan error jika koneksi gagal
    die("Could not connect to the database $dbname :" . $e->getMessage());
}
?>