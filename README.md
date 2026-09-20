# Portfolio V1 — PHP + MySQL

## 1. Database
Import `database.sql` ke phpMyAdmin InfinityFree.

## 2. Database connection
Buat `config/database.php` dari template yang tersedia dan isi:
- YOUR_MYSQL_HOST
- YOUR_DATABASE_NAME
- YOUR_DATABASE_USER
- YOUR_DATABASE_PASSWORD

## 3. Admin
1. Edit `generate_password.php`.
2. Ganti `GANTI_PASSWORD_ANDA`.
3. Buka file itu sekali di browser.
4. Salin hash ke:
   INSERT INTO users (username,password) VALUES ('admin','HASH_DI_SINI');
5. Hapus `generate_password.php` dari server dan GitHub.

## 4. Upload
Upload seluruh isi project ke `htdocs` InfinityFree.
Pastikan folder `uploads` writable.

## 5. GitHub
Jangan commit `config/database.php` yang berisi password asli.
