# API Inventaris Dokumen HKI

REST API untuk mengelola dokumen Hak Kekayaan Intelektual (HKI), kategori, autentikasi pengguna, aktivitas, dan statistik.

## Teknologi

- PHP 8.3+
- Laravel 13
- MySQL
- Laravel Sanctum
- L5 Swagger / OpenAPI

## Fitur

- Registrasi, login, logout, dan API token Sanctum.
- Pengelolaan dokumen: tambah, ubah, pencarian, filter status, unduh file, soft delete, restore, dan hapus permanen.
- Akses dokumen dibatasi dengan policy pemilik/admin untuk aksi yang mengubah atau menghapus data.
- CRUD kategori tersedia untuk `index` dan `store`, dengan middleware admin.
- Activity log, dashboard ringkasan/chart, dan statistik kategori.
- CSV export yang kompatibel dengan spreadsheet serta HTML export yang dapat dicetak ke PDF melalui browser.
- Swagger UI dan rate limiting API.

## Persyaratan

- PHP 8.3 atau lebih baru dengan ekstensi yang diperlukan Laravel.
- Composer.
- MySQL 8.0+.
- Node.js/npm hanya jika ingin membangun halaman welcome berbasis Vite.

## Instalasi Lokal

```bash
git clone https://github.com/ilhampram/api-hki.git
cd api-hki
composer install
copy .env.example .env
php artisan key:generate
```

Atur koneksi database dan nilai `APP_URL` di `.env`, lalu siapkan tabel dan storage:

```bash
php artisan migrate
php artisan storage:link
```

Jalankan server:

```bash
php artisan serve
```

API tersedia di `http://127.0.0.1:8000`; dokumentasi interaktif ada di `/api/documentation`.

## Konfigurasi Penting

Atur minimal variabel berikut di `.env`:

```dotenv
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=api_hki
DB_USERNAME=root
DB_PASSWORD=
SANCTUM_TOKEN_EXPIRATION=10080
```

Jangan commit `.env` atau rahasia ke repository. Login menghapus token lama milik pengguna sebelum membuat token baru. Jadwal harian membersihkan token yang telah kedaluwarsa; jalankan Laravel scheduler di server produksi.

## Endpoint API

Semua endpoint menggunakan awalan `/api`. `/api/test`, registrasi, login, dan endpoint baca dokumen/statistik bersifat publik. Operasi tulis dokumen, recycle bin, restore, ekspor, activity log, dashboard chart, logout, dan kategori memerlukan bearer token sesuai middleware route.

| Method | Endpoint | Akses | Keterangan |
| --- | --- | --- | --- |
| `GET` | `/api/test` | Publik | Health check |
| `POST` | `/api/register` | Publik, 5/menit | Registrasi |
| `POST` | `/api/login` | Publik, 5/menit | Login dan memperoleh token |
| `POST` | `/api/logout` | Auth | Hapus token aktif |
| `GET` | `/api/me` | Auth | Profil pengguna aktif |
| `GET` | `/api/documents` | Publik | Daftar dokumen, pagination `per_page` (1–100) |
| `GET` | `/api/documents/{id}` | Publik | Detail dokumen |
| `GET` | `/api/documents/search/{keyword}` | Publik | Cari dokumen |
| `GET` | `/api/documents/status/{status}` | Publik | Filter status |
| `GET` | `/api/documents/{id}/download` | Publik | Unduh file dokumen |
| `POST` | `/api/documents` | Auth | Buat dokumen |
| `PUT` | `/api/documents/{id}` | Auth + policy pemilik/admin | Ubah dokumen |
| `DELETE` | `/api/documents/{id}` | Auth + policy pemilik/admin | Soft delete |
| `GET` | `/api/documents/trash` | Auth | Daftar recycle bin |
| `PUT` | `/api/documents/{id}/restore` | Auth + policy pemilik/admin | Pulihkan dokumen |
| `DELETE` | `/api/documents/{id}/force-delete` | Auth + policy pemilik/admin | Hapus permanen |
| `GET` | `/api/documents/export/excel` | Auth | Ekspor CSV |
| `GET` | `/api/documents/export/pdf` | Auth | Ekspor HTML untuk dicetak ke PDF |
| `GET` | `/api/activity-log?limit=50` | Auth | Log dengan limit 1–100 |
| `GET` | `/api/dashboard` | Publik | Ringkasan statistik |
| `GET` | `/api/dashboard/chart` | Auth | Data chart dan recycle bin |
| `GET` | `/api/statistics/categories` | Publik | Statistik per kategori |
| `GET/POST` | `/api/categories` | Admin + auth | Daftar/buat kategori |

### Contoh Login

```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'
```

Gunakan token dari respons pada header berikut untuk route yang dilindungi:

```text
Authorization: Bearer <token>
```

Contoh membuat dokumen:

```bash
curl -X POST http://127.0.0.1:8000/api/documents \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{"nomor_hki":"HKI-001","judul":"Contoh karya","pemilik":"Nama pemilik","category_id":1,"tanggal_daftar":"2026-10-19","status":"Pending"}'
```

## Pengujian

```bash
php artisan test
```

## Deployment

Pastikan `.env` production telah disiapkan dengan database yang benar dan backup data tersedia. Skrip deployment Composer menjalankan instalasi production, migration, cache konfigurasi/route, serta storage link:

```bash
composer run deploy
```

Scheduler Laravel perlu dijalankan setiap menit oleh scheduler/cron di server agar pembersihan token harian berjalan. Jangan jalankan migration pada production tanpa backup dan pemeriksaan data duplikat sebelum unique index `documents.nomor_hki` diterapkan.

## Catatan Data

- `documents.nomor_hki` harus unik.
- Nilai status yang digunakan antara lain `Pending`, `Disetujui`, dan `Ditolak`.
- Ekspor spreadsheet dibuat sebagai CSV; ekspor “PDF” saat ini menghasilkan HTML yang dapat dibuka dan dicetak sebagai PDF dari browser.
- Endpoint baca dokumen dan file unduhan publik sesuai konfigurasi route saat ini; batasi akses bila dokumen bersifat rahasia.

Jalankan migration:

```bash
php artisan migrate
```

Buat storage link:

```bash
php artisan storage:link
```

Jalankan server:

```bash
php artisan serve
```

## Authentication

### Register

POST

```http
/api/register
```

### Login

POST

```http
/api/login
```

### Logout

POST

```http
/api/logout
```

## Endpoint Dokumen

### List Dokumen

GET

```http
/api/documents
```

### Detail Dokumen

GET

```http
/api/documents/{id}
```

### Tambah Dokumen

POST

```http
/api/documents
```

### Update Dokumen

PUT

```http
/api/documents/{id}
```

### Hapus Dokumen

DELETE

```http
/api/documents/{id}
```

### Download PDF

GET

```http
/api/documents/{id}/download
```

## Dashboard

GET

```http
/api/dashboard
```

## Author

Ilham Pramudia
Teknik Informatika - Universitas Pamulang
