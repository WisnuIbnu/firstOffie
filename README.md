# 🏢 Office Rent API – Keamanan Jaringan (Tugas Akhir)

Sistem API berbasis Laravel untuk penyewaan ruang kantor. Proyek ini menyediakan berbagai endpoint RESTful untuk pengelolaan gedung, ruang kantor, penyewa, pemesanan, dan pembayaran, serta admin panel menggunakan **Filament**.

## 🚀 Fitur Utama

- Autentikasi pengguna menggunakan Laravel Sanctum
- Panel Admin menggunakan [Filament](https://filamentphp.com/)
- CRUD untuk:
  - Gedung (Buildings)
  - Ruang Kantor (Offices)
  - Penyewa/Pelanggan (Users)
  - Pemesanan (Bookings)
  - Pembayaran (Payments)
- Filtering, sorting, dan pagination data
- Validasi request dan response JSON yang konsisten
- Middleware otorisasi akses
- Dokumentasi API dengan Swagger atau Postman Collection (opsional)

## 🧱 Teknologi

- Laravel ^10.x
- Laravel Sanctum
- Filament Admin Panel
- MySQL / PostgreSQL
- Eloquent ORM
- Laravel Resource & Form Request
- Swagger / Postman (untuk dokumentasi API)

## 📦 Instalasi

Langkah-langkah instalasi proyek secara lokal:

```bash
# 1. Clone repositori ini
git clone https://github.com/username/office-rent-api.git
cd office-rent-api

# 2. Install dependensi Laravel
composer install

# 3. Salin file .env dan generate key
cp .env.example .env
php artisan key:generate

# 4. Atur konfigurasi database di file .env

# 5. Jalankan migrasi dan seed
php artisan migrate --seed

# 6. Instal Filament (jika belum)
php artisan filament:install

# 7. Jalankan server
php artisan serve
