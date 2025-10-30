# 📦 DARIBONTANG-CRUD: Aplikasi Manajemen Data Khas Bontang (DALAM PENGEMBANGAN)

Aplikasi ini merupakan implementasi sistem **CRUD (Create, Read, Update, Delete)** berbasis **PHP Native** dan **MySQL/MariaDB** yang aman menggunakan **PDO Prepared Statements**.  

Login hanya bisa menggunakan akun admin dengan password admin 123.
Yang dalam tahap pengembangan : **Akun, Profile, Beranda didashboard, Pengaturan, dan yang lainnya**.
Digunakan untuk mengelola data **produk, wisata, dan makanan khas** dari Kota Bontang.

---

## 🌟 Fitur Utama

| Modul | Fitur | Keterangan |
|--------|--------|-------------|
| **Sistem** | Otentikasi (Login/Logout) | Akses Dashboard terbatas hanya untuk admin (`admin` / `admin123`). |
| **Sistem** | Halaman Publik (`index.php`) | Menampilkan informasi wisata dan produk dengan tampilan menarik (termasuk background video). |
| **Produk** | CRUD Lengkap | Tambah, lihat detail, edit, dan hapus data produk (kerajinan, oleh-oleh). |
| **Makanan** | CRUD Lengkap | Tambah, lihat detail, edit, dan hapus data makanan khas. |
| **Wisata** | CRUD Lengkap | Tambah, lihat detail, edit, dan hapus data destinasi wisata. |
| **Data** | Pagination | Menampilkan data per halaman (default 5 data). |
| **Data** | Pencarian | Fitur pencarian berdasarkan nama dan kategori/lokasi. |
| **Keamanan** | Validasi & Sanitasi | Mencegah SQL Injection (PDO) dan XSS (`htmlspecialchars`). |

---

## ⚙️ Kebutuhan Sistem

Aplikasi ini dapat dijalankan di lingkungan server lokal dengan konfigurasi berikut:

- **Server Web:** Apache (melalui XAMPP atau Laragon)  
- **Bahasa Pemrograman:** PHP Native (≥ 7.4)  
- **Database:** MySQL / MariaDB  
- **Ekstensi PHP Wajib:** `pdo_mysql` *(aktif secara default)*  

---

## 📂 Struktur Folder Proyek

```

DARIBONTANG-CRUD/
├── config/
│   └── database.php        # Koneksi PDO ke database
├── foto/
│   └── back.mp4            # File video background
├── public/
│   ├── food/               # CRUD untuk Makanan Khas
│   ├── product/            # CRUD untuk Produk
│   ├── tourism/            # CRUD untuk Wisata
│   ├── dashboard.php       # Dashboard admin (CRUD, Pagination, Search)
│   ├── login.php           # Halaman Login
│   └── logout.php          # Logika Logout
├── index.php               # Halaman utama / beranda publik
├── script.js               # Dark Mode, Search, dll.
└── style.css               # Desain utama website

````
---

## 🧾 Contoh Environment Config

Jika ingin menggunakan file `.env` sebagai pengganti `config/database.php`, buat file baru bernama `.env` di folder utama:

```env
# Server configuration
APP_NAME=DariBontang
APP_ENV=local
APP_PORT=8080

# Database configuration
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=db_daribontang
```

Kemudian ubah file PHP untuk membaca konfigurasi `.env` dengan bantuan `parse_ini_file()`:

```php
$env = parse_ini_file('.env');
$connect = mysqli_connect(
  $env['DB_HOST'],
  $env['DB_USER'],
  $env['DB_PASS'],
  $env['DB_NAME']
);
```

---

## 🛠️ Panduan Instalasi dan Konfigurasi

Ikuti langkah-langkah berikut untuk menjalankan aplikasi di server lokal (**XAMPP / Laragon**):

### 🧩 Langkah 1: Kloning dan Penempatan File
1. Letakkan folder `DARIBONTANG-CRUD` ke dalam direktori server:
   - `htdocs` → jika menggunakan XAMPP  
   - `www` → jika menggunakan Laragon  
2. Jalankan **Apache** dan **MySQL**.

---

### 🧩 Langkah 2: Konfigurasi Database

1. Buat database baru:
   ```sql
   CREATE DATABASE db_daribontang;
   ```

2. Buka file `config/database.php`, sesuaikan konfigurasi:

   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', ''); // Isi jika MySQL Anda memiliki password
   define('DB_NAME', 'db_daribontang');
   ```

3. Jalankan query SQL berikut untuk membuat tabel:

   ```sql
   -- Tabel Users
   CREATE TABLE users (
     id INT AUTO_INCREMENT PRIMARY KEY,
     username VARCHAR(50) UNIQUE,
     password VARCHAR(255)
   );

   INSERT INTO users (username, password) VALUES ('admin', 'admin123');

   -- Tabel Products
   CREATE TABLE products (
     id INT AUTO_INCREMENT PRIMARY KEY,
     name VARCHAR(255),
     category VARCHAR(100),
     description TEXT,
     stock INT,
     price DECIMAL(10,2),
     created_at DATETIME,
     updated_at DATETIME
   );

   -- Tabel Tourism
   CREATE TABLE tourism (
     id INT AUTO_INCREMENT PRIMARY KEY,
     name VARCHAR(255),
     location VARCHAR(255),
     description TEXT,
     rating DECIMAL(2,1),
     price_range VARCHAR(50),
     created_at DATETIME,
     updated_at DATETIME
   );

   -- Tabel Food
   CREATE TABLE foods (
     id INT AUTO_INCREMENT PRIMARY KEY,
     name VARCHAR(100) NOT NULL,
     origin VARCHAR(100),
     description TEXT,
     price_range VARCHAR(50),
     best_place VARCHAR(150),
     rating DECIMAL(2,1) DEFAULT 4.0,
     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );
   ```

---

### 🧩 Langkah 3: Akses Aplikasi

| Jenis Akses          | URL                                                                                                      | Keterangan                     |
| -------------------- | -------------------------------------------------------------------------------------------------------- | ------------------------------ |
| **Publik (Beranda)** | [http://localhost/DARIBONTANG-CRUD/index.php](http://localhost/DARIBONTANG-CRUD/index.php)               | Halaman depan untuk pengunjung |
| **Admin (Login)**    | [http://localhost/DARIBONTANG-CRUD/public/login.php](http://localhost/DARIBONTANG-CRUD/public/login.php) | Masuk ke dashboard admin       |

---

## 📝 Panduan Penggunaan Aplikasi

### 1️⃣ Login ke Dashboard

* Buka [http://localhost/DARIBONTANG-CRUD/public/login.php](http://localhost/DARIBONTANG-CRUD/public/login.php)
* Gunakan akun:

  * **Username:** `admin`
  * **Password:** `admin123`
* Setelah login, pengguna diarahkan ke **Dashboard Admin**.

---

### 2️⃣ Mengelola Data (CRUD)

#### 🔍 A. Tampil Data (Read)

* Pilih menu **Produk**, **Wisata**, atau **Makanan Khas**.
* Gunakan kolom pencarian untuk memfilter data.
* Navigasi halaman dengan **Pagination**.
* Klik **Detail** untuk melihat info lengkap.

#### ➕ B. Tambah Data (Create)

1. Klik tombol **+ Tambah** di masing-masing modul.
2. Isi form dengan lengkap.
3. Klik **Simpan**.

#### ✏️ C. Edit Data (Update)

1. Klik tombol **Edit**.
2. Ubah data sesuai kebutuhan.
3. Klik **Simpan Perubahan**.

#### ❌ D. Hapus Data (Delete)

1. Klik tombol **Hapus**.
2. Konfirmasi tindakan.
3. Data akan dihapus permanen.

---

### 🚪 3️⃣ Logout

* Klik menu **Logout** di pojok atas dashboard untuk keluar dari sistem.
* Anda akan diarahkan ke halaman **Login**.

---

## 🖼️ Screenshot Aplikasi

### 🏠 Beranda

![Beranda](https://github.com/user-attachments/assets/ea434d4e-cfdd-4bb5-aa34-66485dbfe3a5)

### 🔑 Login

![Login](https://github.com/user-attachments/assets/abfac8f5-be07-437f-b00c-fbe3ec33eeab)

### 📊 Dashboard Admin

![Dashboard](https://github.com/user-attachments/assets/3824259e-cc35-4811-b32a-7932d4f45e8d)

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

✍️ **Dibuat oleh:** Widya Ayu Anggraini 2409106011
📅 **Tahun:** 2025

```

---

