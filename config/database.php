<?php
// Konfigurasi Database (Wajib)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // GANTI DENGAN PASSWORD ANDA
define('DB_NAME', 'db_daribontang'); // GANTI DENGAN NAMA DB ANDA

/**
 * Fungsi untuk membuat koneksi PDO yang aman.
 * @return PDO
 */
function connectDB() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Wajib: Tampilkan error PDO sebagai Exception
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,     // Wajib: Fetch sebagai array asosiatif
            PDO::ATTR_EMULATE_PREPARES   => false,                // Wajib: Keamanan Prepared Statements
        ];
        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        // Pesan error informatif, tidak menampilkan stack trace (Sesuai ketentuan)
        error_log("PDO Connection Error: " . $e->getMessage()); // Catat error ke log server
        die("❌ Koneksi Database Gagal. Silakan cek file config/database.php."); 
    }
}

$pdo = connectDB();
