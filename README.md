# 🏝️ Aplikasi Destinasi Wisata

Aplikasi web CRUD (Create, Read, Update, Delete) untuk mengelola data destinasi wisata. Dibangun dengan PHP Native dan MySQL, aplikasi ini memiliki antarmuka modern dan responsif untuk memudahkan pengelolaan informasi destinasi wisata favorit Anda.

## ✨ Fitur Utama

✅ **Semua fitur wajib terpenuhi:**

- ➕ **Create** - Form tambah destinasi dengan validasi server-side dan pesan sukses/gagal
- 📋 **Read** - Tabel daftar destinasi, diurutkan berdasarkan `created_at DESC`
- 👁️ **Read Detail** - Halaman detail lengkap untuk setiap destinasi
- ✏️ **Update** - Form edit dengan data yang sudah terisi (prefill)
- 🗑️ **Delete** - Tombol hapus dengan konfirmasi JavaScript
- 🔍 **Pencarian** - Cari berdasarkan nama atau lokasi destinasi
- 📄 **Pagination** - Navigasi data dengan 5 item per halaman
- 🔒 **Validasi & Sanitasi** - Perlindungan dari SQL Injection dan XSS
- ⚠️ **Error Handling** - Pesan error informatif tanpa stack trace
- 🎨 **UI Modern & Responsif** - Antarmuka yang elegan dan mobile-friendly

## 💻 Kebutuhan Sistem

- **PHP** versi 8.0 atau lebih tinggi
- **MySQL** versi 5.7 atau lebih tinggi / **MariaDB** 10.2+
- **Web Server** (Apache/Nginx) - Bisa menggunakan XAMPP atau Laragon
- **PDO Extension** (biasanya sudah terinstall di PHP)
- Browser modern (Chrome, Firefox, Safari, Edge)

## 📦 Cara Instalasi

### 1. Clone atau Download Repository

```bash
git clone (https://github.com/WidyaRanyyy/travel-crud)
cd TRAVEL-CRUD
```

### 2. Setup Database

Buat database baru di MySQL:

```sql
CREATE DATABASE destinasi_wisata;
USE destinasi_wisata;

CREATE TABLE destinations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    ticket_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 3. Konfigurasi Database

Buat file `config/database.php` dengan konten berikut:

```php
<?php
$host = 'localhost';
$db   = 'destinasi_wisata';
$user = 'root';
$pass = '';
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
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
```

**Sesuaikan kredensial database:**
- `$host` - Host database (default: localhost)
- `$db` - Nama database
- `$user` - Username database
- `$pass` - Password database

### 4. Struktur Folder

```
TRAVEL-CRUD/
│
├── config/
│   ├── .env                  # File environment (opsional)
│   └── database.php          # Konfigurasi koneksi database
│
├── public/                   # Folder utama aplikasi
│   ├── assets/
│   │   └── style.css         # File CSS untuk styling
│   │
│   ├── about.php             # Halaman tentang aplikasi
│   ├── create.php            # Halaman tambah destinasi
│   ├── delete.php            # Proses hapus destinasi
│   ├── detail.php            # Halaman detail destinasi
│   ├── edit.php              # Halaman edit destinasi
│   └── index.php             # Halaman utama (list destinasi)
│
├── database.sql              # File SQL untuk setup database
└── README.md                 # Dokumentasi
```

### 5. Jalankan Aplikasi

Jika menggunakan XAMPP/WAMP:
1. Letakkan folder project di `htdocs` (XAMPP) atau `www` (WAMP)
2. Akses melalui browser: `http://localhost/TRAVEL-CRUD/public/`

Jika menggunakan PHP Built-in Server:
```bash
cd public
php -S localhost:8000
```
Akses: `http://localhost:8000`

## 🎯 Contoh Environment Config

Buat file `.env` atau `config/database.php` dengan template:

```php
<?php
// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'destinasi_wisata');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Pengaturan Aplikasi
define('BASE_URL', 'http://localhost/TRAVEL-CRUD/public/');
define('APP_NAME', 'Destinasi Wisata');
define('ITEMS_PER_PAGE', 5);

// Koneksi Database
$dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET;
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (\PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
?>
```

## 📸 Screenshot Aplikasi

1. Beranda
<img width="960" height="439" alt="image" src="https://github.com/user-attachments/assets/d5730224-0b4e-48df-b495-fb65c0000010" />

2. Tambah Destinasi
<img width="435" height="382" alt="image" src="https://github.com/user-attachments/assets/189723db-cee4-433b-8475-3c05a754f16b" />

3. Edit Destinasi
<img width="258" height="364" alt="image" src="https://github.com/user-attachments/assets/a9ad4235-c1d1-4f88-aaf4-3e570e5643d8" />

4. Detail Destinasi
<img width="320" height="345" alt="image" src="https://github.com/user-attachments/assets/985ef29e-059a-4376-897d-bd5719f0938e" />

5. Tentang Aplikasi
<img width="787" height="383" alt="image" src="https://github.com/user-attachments/assets/90f70f4d-f86e-4d44-9bce-ca6459bfd87d" />



### Halaman Utama (List Destinasi)
```
┌─────────────────────────────────────────────────────┐
│  Destinasi Wisata    [Beranda] [Tambah] [Tentang]  │
├─────────────────────────────────────────────────────┤
│                                                     │
│  Destinasi Wisata                                   │
│  Kelola destinasi favorit Anda dengan mudah        │
│                                                     │
│  [+ Tambah Destinasi]                              │
│                                                     │
│  [Cari nama atau lokasi...] [Cari]                 │
│                                                     │
│  ┌───────────────────────────────────────────────┐ │
│  │ No │ Nama        │ Lokasi      │ Harga │ Aksi│ │
│  ├────┼─────────────┼─────────────┼───────┼─────┤ │
│  │ 1  │ Pantai Kuta │ Bali        │ 25000 │ ... │ │
│  │ 2  │ Borobudur   │ Yogyakarta  │ 50000 │ ... │ │
│  └───────────────────────────────────────────────┘ │
│                                                     │
│  [« Sebelumnya] [1] [2] [3] [Berikutnya »]        │
│                                                     │
└─────────────────────────────────────────────────────┘
```

### Halaman Tambah/Edit Destinasi
```
┌─────────────────────────────────────────────────────┐
│  Tambah Destinasi Wisata                            │
│  Isi semua kolom dengan benar                       │
│                                                     │
│  ← Kembali ke Daftar                               │
│                                                     │
│  Nama Destinasi: [________________]                 │
│  Lokasi:         [________________]                 │
│  Deskripsi:      [________________]                 │
│                  [________________]                 │
│  Harga Tiket:    [________] Rp                     │
│                                                     │
│  [Simpan Destinasi]                                │
└─────────────────────────────────────────────────────┘
```

## 🛠️ Teknologi yang Digunakan

- **Backend:** PHP 8.0+ Native (tanpa framework/ORM)
- **Database:** MySQL/MariaDB dengan PDO
- **Frontend:** HTML5, CSS3
- **Design:** Modern UI dengan CSS Grid & Flexbox
- **Security:** Prepared Statements (PDO), htmlspecialchars() untuk XSS protection

## 👨‍💻 Developer

**Widya Ayu Anggraini**  
NIM: 2409106011

Dikembangkan sebagai latihan dalam membangun aplikasi CRUD dinamis berbasis web dengan PHP Native.

## 📝 Lisensi

Aplikasi ini dibuat untuk keperluan edukasi dan pembelajaran.

## 🐛 Troubleshooting

### Error: Connection refused
- Pastikan MySQL service sudah berjalan
- Cek kredensial database di `config/database.php`

### Error: 404 Not Found
- Pastikan path URL sesuai dengan lokasi folder
- Gunakan `http://localhost/nama-folder/public/` bukan `http://localhost/nama-folder/`

### Error: Function not found
- Pastikan PHP PDO extension sudah aktif
- Cek `php.ini` dan uncomment `extension=pdo_mysql`

### Error: CSS tidak muncul
- Periksa path file `assets/style.css`
- Pastikan file CSS berada di folder `public/assets/`



**Selamat menggunakan Aplikasi Destinasi Wisata! 🎉**


