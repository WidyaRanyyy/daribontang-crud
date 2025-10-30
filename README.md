# 📦 DARIBONTANG-CRUD: Aplikasi Manajemen Data Khas Bontang

Aplikasi ini adalah implementasi sistem **CRUD (Create, Read, Update, Delete)** berbasis **PHP Native** dan **MySQL/MariaDB** yang aman (menggunakan **PDO Prepared Statements**) untuk mengelola data **produk, wisata, dan makanan khas** dari Kota Bontang.

---

## 🌟 Fitur Utama yang Tersedia

| Modul | Fitur | Keterangan |
|--------|--------|-------------|
| **Sistem** | Otentikasi (Login/Logout) | Akses Dashboard terbatas hanya untuk admin (Hardcoded: `admin` / `admin123`). |
| **Sistem** | Halaman Publik (`index.php`) | Menampilkan informasi wisata dan produk dengan desain menarik (termasuk background video). |
| **Produk** | CRUD Lengkap | Tambah, Lihat Detail, Edit, dan Hapus data produk (kerajinan, oleh-oleh). |
| **Makanan** | CRUD Lengkap | Tambah, Lihat Detail, Edit, dan Hapus data makanan khas. |
| **Wisata** | CRUD Lengkap | Tambah, Lihat Detail, Edit, dan Hapus data destinasi wisata. |
| **Data** | Pagination | Pembatasan tampilan data per halaman (default 5 data per halaman). |
| **Data** | Pencarian | Pencarian keyword pada kolom nama dan kategori/lokasi. |
| **Keamanan** | Validasi & Sanitasi | Mencegah SQL Injection (dengan PDO) dan XSS (dengan `htmlspecialchars`). |

---

## ⚙️ Kebutuhan Sistem

Aplikasi ini dirancang untuk berjalan di lingkungan server lokal:

- **Server Web:** Apache (melalui XAMPP atau Laragon)  
- **Bahasa Pemrograman:** PHP Native (minimal PHP 7.4)  
- **Database:** MySQL atau MariaDB  
- **Ekstensi PHP Wajib:** `pdo_mysql` *(biasanya aktif secara default)*  

---

## 📂 Struktur Folder Proyek

Struktur folder mencerminkan pemisahan antara file publik (yang dapat diakses langsung oleh browser) dan file konfigurasi:

```

DARIBONTANG-CRUD/
├── config/
│   └── database.php      # Koneksi PDO ke Database
├── foto/
│   └── back.mp4          # File video background
├── public/
│   ├── food/             # File CRUD untuk Makanan Khas (add, detail, edit, delete)
│   ├── product/          # File CRUD untuk Produk (add, detail, edit, delete)
│   ├── tourism/          # File CRUD untuk Wisata (add, detail, edit, delete)
│   ├── dashboard.php     # Halaman utama admin (CRUD, Pagination, Search)
│   ├── login.php         # Halaman Login Admin
│   └── logout.php        # Logika Logout
├── index.php             # Halaman utama / Beranda publik
├── script.js             # Logika Dark Mode, Search (Client-Side), dll.
└── style.css             # Desain Utama Website

````

---

## 🛠️ Panduan Instalasi dan Konfigurasi

Ikuti langkah-langkah di bawah ini untuk menjalankan aplikasi di server lokal Anda (**XAMPP / Laragon**):

### 🧩 Langkah 1: Kloning dan Penempatan File
1. Tempatkan folder `DARIBONTANG-CRUD` ke dalam direktori server Anda:
   - `htdocs` untuk XAMPP  
   - `www` untuk Laragon  
2. Pastikan **Apache** dan **MySQL** sudah berjalan.

---

### 🧩 Langkah 2: Konfigurasi Database (MySQL/MariaDB)

1. Buat database baru bernama:
   ```sql
   CREATE DATABASE db_daribontang;
````

2. Buka file `config/database.php` dan sesuaikan kredensial koneksi:

   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', ''); // Ganti jika MySQL Anda memiliki password
   define('DB_NAME', 'db_daribontang'); // Pastikan nama ini sesuai
   ```

3. Jalankan script SQL berikut untuk membuat tabel yang dibutuhkan:

   ```sql
   -- 1. Tabel Users (untuk Login)
   CREATE TABLE users (
     id INT PRIMARY KEY AUTO_INCREMENT,
     username VARCHAR(50) UNIQUE,
     password VARCHAR(255)
   );

   INSERT INTO users (username, password) VALUES ('admin', 'admin123');

   -- 2. Tabel Products
   CREATE TABLE products (
     id INT PRIMARY KEY AUTO_INCREMENT,
     name VARCHAR(255),
     category VARCHAR(100),
     description TEXT,
     stock INT,
     price DECIMAL(10,2),
     created_at DATETIME,
     updated_at DATETIME
   );

   -- 3. Tabel Tourism
   CREATE TABLE tourism (
     id INT PRIMARY KEY AUTO_INCREMENT,
     name VARCHAR(255),
     location VARCHAR(255),
     description TEXT,
     rating DECIMAL(2,1),
     price_range VARCHAR(50),
     created_at DATETIME,
     updated_at DATETIME
   );
   ```
  --4. Tabel Food
    CREATE TABLE IF NOT EXISTS foods (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        origin VARCHAR(100),
        description TEXT,
        price_range VARCHAR(50),
        best_place VARCHAR(150),
        rating DECIMAL(2,1) DEFAULT 4.0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
---

### 🧩 Langkah 3: Akses Aplikasi

| Akses                | URL                                                                                                      | Keterangan                              |
| -------------------- | -------------------------------------------------------------------------------------------------------- | --------------------------------------- |
| **Publik (Beranda)** | [http://localhost/DARIBONTANG-CRUD/index.php](http://localhost/DARIBONTANG-CRUD/index.php)               | Halaman depan untuk pengunjung          |
| **Admin (Login)**    | [http://localhost/DARIBONTANG-CRUD/public/login.php](http://localhost/DARIBONTANG-CRUD/public/login.php) | Masuk ke Dashboard untuk mengelola data |

---

## 📝 Panduan Penggunaan Web (Cara Memakai Aplikasi)

Berikut adalah langkah-langkah untuk mengakses dan mengelola konten di aplikasi:

### 1️⃣ Login ke Dashboard

* Akses halaman Login melalui URL:
  [http://localhost/DARIBONTANG-CRUD/public/login.php](http://localhost/DARIBONTANG-CRUD/public/login.php)
* Gunakan kredensial default:

  * **Username:** `admin`
  * **Password:** `admin123`
* Setelah berhasil login, Anda akan diarahkan ke **Dashboard Admin (`dashboard.php`)**.

---

### 2️⃣ Mengelola Data Produk, Makanan dan Wisata (CRUD)

Setelah login, Anda dapat beralih antara menu **Produk**, **Makanan** dan **Wisata** menggunakan navigasi di sidebar.

#### 🔍 A. Fitur Read (Tampil Data)

* Akses: Klik menu **Produk** (`dashboard.php?page=produk`) atau **Wisata** (`dashboard.php?page=wisata`) atau **Makanan Khas** (`dashboard.php?page=food`).
* **Pencarian:** Gunakan kolom input “Cari berdasarkan nama/kategori…” untuk memfilter data.
* **Pagination:** Navigasi di bawah tabel menampilkan maksimal 5 data per halaman.
* **Detail:** Klik tombol **Detail** untuk melihat informasi lengkap setiap item.

#### ➕ B. Fitur Create (Tambah Data)

1. Klik tombol **+ Tambah Produk** atau **+ Tambah Wisata** atau **+ Tambah Makanan**.
2. Isi semua kolom form yang diperlukan.
3. Klik **Simpan Produk/Wisata/Makanan**.
4. Jika berhasil, sistem akan menampilkan pesan sukses dan menampilkan data baru di tabel.

#### ✏️ C. Fitur Update

1. Klik tombol **Edit** pada data yang ingin diubah.
2. Lakukan perubahan pada form yang muncul.
3. Klik **Simpan Perubahan** untuk memperbarui data.

#### ❌ D. Fitur Delete

1. Klik tombol **Hapus** pada data yang ingin dihapus.
2. Akan muncul konfirmasi penghapusan.
3. Klik **OK** untuk menghapus data secara permanen.

---

### 🚪 3️⃣ Logout

* Klik menu **Logout** pada pojok atas Dashboard untuk keluar dari sistem.
* Anda akan diarahkan kembali ke halaman **Login**.

---

## 🖼️ Tampilan Aplikasi

### Beranda:

<img width="960" height="449" alt="image" src="https://github.com/user-attachments/assets/ea434d4e-cfdd-4bb5-aa34-66485dbfe3a5" />

### Login:

<img width="960" height="445" alt="image" src="https://github.com/user-attachments/assets/abfac8f5-be07-437f-b00c-fbe3ec33eeab" />

### Dashboard Admin (Produk):

<img width="960" height="444" alt="image" src="https://github.com/user-attachments/assets/3824259e-cc35-4811-b32a-7932d4f45e8d" />

---

## 🔒 Akses Admin

| Username | Password   |
| -------- | ---------- |
| `admin`  | `admin123` |

---

## 📜 Lisensi

Aplikasi ini dikembangkan untuk tujuan **pembelajaran dan tugas akademik**.
Bebas dimodifikasi dan dikembangkan sesuai kebutuhan.

---

```

--- 
