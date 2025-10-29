# 🏝️ Web CRUD Destinasi Wisata dengan PHP Native

Aplikasi CRUD sederhana (Create, Read, Update, Delete) untuk mengelola data destinasi wisata menggunakan **PHP Native** dan koneksi database **PDO**.

## 🚀 Fitur Utama

Aplikasi ini mengimplementasikan fitur-fitur dasar CRUD sesuai dengan spesifikasi tugas:

  * **Create (Tambah Data)**: Form untuk menambah data destinasi baru dengan validasi sisi server (`create.php`).
  * **Read (Daftar Data)**: Halaman utama menampilkan daftar destinasi dalam bentuk tabel, diurutkan berdasarkan `created_at` secara *descending* (`index.php`).
      * **Pagination**: Daftar data dibatasi **5 data per halaman** (`index.php`).
  * **Read Detail**: Halaman untuk melihat informasi lengkap setiap destinasi (`detail.php`).
  * **Update (Edit Data)**: Form untuk mengubah data destinasi dengan *prefill* data lama (`edit.php`).
  * **Delete (Hapus Data)**: Tombol hapus dengan konfirmasi *prompt* (`delete.php`).
  * **Validasi & Sanitasi**: Implementasi sanitasi data menggunakan *Prepared Statements* (PDO) untuk menghindari SQL Injection, dan penggunaan `htmlspecialchars()` untuk mencegah XSS.
  * **Pesan Informatif**: Menampilkan pesan sukses atau error yang informatif setelah operasi CRUD (`index.php`, `create.php`, `edit.php`).

## 🛠️ Kebutuhan Sistem

  * **Bahasa**: PHP Native (minimal versi 8.0)
  * **Database**: MySQL atau MariaDB
  * **Web Server**: Server lokal (misalnya XAMPP, Laragon, atau sejenisnya)
  * **Koneksi Database**: Wajib menggunakan PDO.

## ⚙️ Cara Instalasi dan Konfigurasi

1.  **Clone Repository**:

    ```bash
    git clone (https://github.com/WidyaRanyyy/travel-crud)
    ```

2.  **Siapkan Database**:

      * Buat database baru (misalnya: `db_wisata`).
      * Buat tabel `destinations` dengan struktur kurang lebih seperti berikut:

    <!-- end list -->

    ```sql
    CREATE TABLE destinations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        location VARCHAR(255) NOT NULL,
        description TEXT,
        ticket_price DECIMAL(10, 2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    ```

3.  **Konfigurasi Koneksi Database**:

      * Buat file koneksi database (misalnya: `config/database.php`).
      * Isi dengan konfigurasi PDO Anda.

    **Contoh `config/database.php`:**

    ```php
    <?php
    $host = 'localhost';
    $db   = 'db_wisata';
    $user = 'root'; // Ganti dengan user database Anda
    $pass = ''; // Ganti dengan password database Anda
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        // Pesan error informatif, tanpa menampilkan stack trace
        die("Koneksi database gagal: " . $e->getMessage()); 
    }
    ```

4.  **Akses Aplikasi**:

      * Jalankan server lokal Anda.
      * Akses aplikasi melalui *browser* ke direktori proyek (`http://localhost/[FOLDER_PROYEK]/destinations/index.php`).

## 📁 Struktur Folder

Struktur folder utama yang digunakan dalam proyek ini:

```
.
├── config/
│   └── database.php  # Konfigurasi koneksi PDO
├── destinations/
│   ├── assets/
│   │   └── style.css # File CSS (Diasumsikan ada)
│   ├── create.php    # Logika dan tampilan tambah data
│   ├── detail.php    # Logika dan tampilan detail data
│   ├── delete.php    # Logika hapus data
│   ├── edit.php      # Logika dan tampilan edit data
│   └── index.php     # Logika dan tampilan daftar data (Read)
└── README.md         # File ini
```

## 📸 Screenshot Aplikasi
1. Tampilan Awal
<img width="960" height="416" alt="image" src="https://github.com/user-attachments/assets/4e78fe6d-eda1-4e85-b3cb-8722f2ceb384" />

2. Tambah Destinasi
<img width="715" height="429" alt="image" src="https://github.com/user-attachments/assets/20135c87-4b51-4963-aecf-58ec747213ca" />

3. Edit Destinasi
<img width="697" height="432" alt="image" src="https://github.com/user-attachments/assets/9c00c88b-aaa2-43f5-8c5c-d0266c547fef" />

4. Detail Destinasi
<img width="713" height="318" alt="image" src="https://github.com/user-attachments/assets/ed489038-06d0-4376-ab05-a24023de5c63" />

5. Tentang
<img width="740" height="408" alt="image" src="https://github.com/user-attachments/assets/86b8af9c-571d-45dd-a9c3-f3b3c6c8bd3d" />

