# 📋 API Inventaris HKI (Hak Kekayaan Intelektual)

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-00758F?style=flat-square&logo=mysql)](https://www.mysql.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg?style=flat-square)](LICENSE)

RESTful API profesional untuk manajemen dokumen Hak Kekayaan Intelektual dengan fitur lengkap termasuk autentikasi, dokumentasi Swagger interaktif, export multi-format, activity logging, dan dashboard statistik real-time.

---

## 🎯 Fitur Utama

### 📄 Manajemen Dokumen

- ✅ CRUD dokumen lengkap dengan validasi
- ✅ Upload & download file PDF
- ✅ Soft delete dengan recycle bin
- ✅ Permanent delete (Force Delete) dengan pembersihan file
- ✅ Restore dokumen dari recycle bin
- ✅ Pencarian by judul atau nomor HKI
- ✅ Filter berdasarkan status (Pending, Disetujui, Ditolak)
- ✅ Kategori dokumen (Parent-Child relationships)

### 📊 Export & Reporting

- ✅ **Export Excel** - Download semua dokumen dalam format CSV
- ✅ **Export PDF** - Download dokumen dengan formatting profesional
- ✅ Activity Log - Audit trail lengkap semua operasi
- ✅ Dashboard Chart - Statistik real-time dengan visualisasi data

### 🔐 Autentikasi & Keamanan

- ✅ Bearer Token Authentication (Laravel Sanctum)
- ✅ Protected endpoints dengan middleware auth:sanctum
- ✅ IP Address tracking untuk setiap aktivitas
- ✅ Role-based access control (user role field tersedia)

### 📈 Dashboard & Analytics

- ✅ Ringkasan statistik dokumen
- ✅ Breakdown per kategori
- ✅ Status statistics (Pending, Disetujui, Ditolak)
- ✅ Monthly statistics trend
- ✅ Recycle bin count

### 📋 Activity Logging

- ✅ Tracking semua CRUD operations
- ✅ Pencatatan create, update, delete, restore, force_delete
- ✅ Export activities (excel & pdf)
- ✅ User & document relationships
- ✅ IP address & timestamp untuk setiap aksi

### 📚 Dokumentasi

- ✅ Interactive Swagger/OpenAPI UI
- ✅ Comprehensive API documentation
- ✅ Request/response examples
- ✅ Security scheme definitions

---

## 🛠️ Tech Stack

| Component          | Version         | Purpose                      |
| ------------------ | --------------- | ---------------------------- |
| **Framework**      | Laravel 12      | Backend framework PHP modern |
| **Language**       | PHP             | 8.2+                         |
| **Database**       | MySQL           | 8.0+                         |
| **Authentication** | Laravel Sanctum | API token authentication     |
| **ORM**            | Eloquent        | Database abstraction layer   |
| **API Docs**       | Swagger/OpenAPI | L5-Swagger                   |
| **File Upload**    | Laravel Storage | File management system       |
| **Testing**        | PHPUnit         | Unit & feature testing       |

---

## ⚙️ Instalasi & Setup

### Prerequisites

```bash
# Pastikan terinstall:
- PHP 8.2 atau lebih tinggi
- Composer
- MySQL 8.0 atau lebih tinggi
- Node.js & npm (opsional, untuk frontend)
```

### 1️⃣ Clone Repository

```bash
git clone <repository-url>
cd api-hki
```

### 2️⃣ Install Dependencies

```bash
composer install
```

### 3️⃣ Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate
```

### 4️⃣ Database Setup

```bash
# Edit .env dengan credentials database Anda
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_hki_api
DB_USERNAME=root
DB_PASSWORD=

# Run migrations
php artisan migrate

# (Optional) Seed data
php artisan db:seed
```

### 5️⃣ Generate Swagger Documentation

```bash
php artisan l5-swagger:generate
```

### 6️⃣ Start Development Server

```bash
php artisan serve
```

Server akan berjalan di: `http://127.0.0.1:8000`

---

## 🚀 Penggunaan API

### Authentication

Semua endpoint yang dilindungi memerlukan Bearer Token. Dapatkan token melalui login endpoint, kemudian sertakan di header:

```bash
Authorization: Bearer <your-token>
```

### 📌 Contoh Request

#### 1. Login

```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password123"
  }'
```

#### 2. Lihat Semua Dokumen

```bash
curl -X GET http://127.0.0.1:8000/api/documents \
  -H "Authorization: Bearer YOUR_TOKEN"
```

#### 3. Tambah Dokumen Baru

```bash
curl -X POST http://127.0.0.1:8000/api/documents \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "judul": "Patent XYZ",
    "nomor_hki": "P00123456",
    "category_id": 1,
    "status": "Pending",
    "deskripsi": "Deskripsi produk"
  }'
```

#### 4. Export ke Excel

```bash
curl -X GET http://127.0.0.1:8000/api/documents/export/excel \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -o documents.csv
```

#### 5. Export ke PDF

```bash
curl -X GET http://127.0.0.1:8000/api/documents/export/pdf \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -o documents.pdf
```

#### 6. Lihat Recycle Bin

```bash
curl -X GET http://127.0.0.1:8000/api/documents/trash \
  -H "Authorization: Bearer YOUR_TOKEN"
```

#### 7. Restore Dokumen

```bash
curl -X PUT http://127.0.0.1:8000/api/documents/{id}/restore \
  -H "Authorization: Bearer YOUR_TOKEN"
```

#### 8. Force Delete (Permanent)

```bash
curl -X DELETE http://127.0.0.1:8000/api/documents/{id}/force-delete \
  -H "Authorization: Bearer YOUR_TOKEN"
```

#### 9. Lihat Activity Log

```bash
curl -X GET http://127.0.0.1:8000/api/activity-log?limit=50 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

#### 10. Dashboard Chart Data

```bash
curl -X GET http://127.0.0.1:8000/api/dashboard/chart \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## 📡 API Endpoints

### Dokumentasi Lengkap

Akses Swagger UI interaktif:

```
http://127.0.0.1:8000/api/documentation
```

### Public Endpoints

| Method | Endpoint                          | Description          |
| ------ | --------------------------------- | -------------------- |
| GET    | `/api/documents`                  | Daftar semua dokumen |
| GET    | `/api/documents/{id}`             | Detail dokumen       |
| GET    | `/api/documents/search/{keyword}` | Cari dokumen         |
| GET    | `/api/documents/status/{status}`  | Filter by status     |
| GET    | `/api/documents/{id}/download`    | Download file        |

### Protected Endpoints (Requires Auth)

| Method | Endpoint                           | Description       |
| ------ | ---------------------------------- | ----------------- |
| POST   | `/api/documents`                   | Buat dokumen baru |
| PUT    | `/api/documents/{id}`              | Update dokumen    |
| DELETE | `/api/documents/{id}`              | Soft delete       |
| GET    | `/api/documents/trash`             | Lihat recycle bin |
| PUT    | `/api/documents/{id}/restore`      | Restore dokumen   |
| DELETE | `/api/documents/{id}/force-delete` | Permanent delete  |
| GET    | `/api/documents/export/excel`      | Export Excel      |
| GET    | `/api/documents/export/pdf`        | Export PDF        |
| GET    | `/api/activity-log`                | Activity log      |
| GET    | `/api/dashboard/chart`             | Dashboard data    |
| POST   | `/api/logout`                      | Logout user       |

---

## 📊 Database Schema

### Tables

#### `documents`

```
- id (Primary Key)
- category_id (Foreign Key to categories)
- user_id (Foreign Key to users)
- judul (string)
- nomor_hki (string)
- status (enum: Pending, Disetujui, Ditolak)
- deskripsi (text)
- file_dokumen (string - filename)
- deleted_at (timestamp - untuk soft delete)
- created_at, updated_at
```

#### `categories`

```
- id (Primary Key)
- nama (string)
- deskripsi (text)
- created_at, updated_at
```

#### `users`

```
- id (Primary Key)
- name (string)
- email (string - unique)
- password (hashed)
- role (string - user, admin)
- created_at, updated_at
```

#### `activity_logs`

```
- id (Primary Key)
- user_id (Foreign Key)
- action (string: create, update, delete, restore, export_excel, export_pdf, force_delete)
- description (text)
- document_id (Foreign Key - nullable)
- ip_address (string - nullable)
- created_at, updated_at
```

#### `personal_access_tokens` (Sanctum)

```
- id (Primary Key)
- tokenable_id, tokenable_type
- name (string)
- token (string - hashed)
- abilities (json)
- last_used_at, created_at, updated_at
```

---

## 🧪 Testing

### Run Unit Tests

```bash
php artisan test
```

### Run Specific Test

```bash
php artisan test tests/Feature/DocumentControllerTest.php
```

### Coverage Report

```bash
php artisan test --coverage
```

---

## 📁 Project Structure

```
api-hki/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── DocumentController.php
│   │   └── Middleware/
│   ├── Models/
│   │   ├── Document.php
│   │   ├── Category.php
│   │   ├── User.php
│   │   └── ActivityLog.php
│   ├── Providers/
│   │   └── AppServiceProvider.php
│   └── OpenApi/
│       └── OpenApiSpec.php
├── config/
│   ├── app.php
│   ├── database.php
│   ├── l5-swagger.php
│   └── ...
├── database/
│   ├── migrations/
│   ├── factories/
│   └── seeders/
├── routes/
│   └── api.php
├── storage/
│   ├── api-docs/
│   │   └── api-docs.json
│   └── app/
├── tests/
│   ├── Feature/
│   └── Unit/
├── .env.example
├── artisan
├── composer.json
├── phpunit.xml
└── README.md
```

---

## 🔐 Security Best Practices

1. **Environment Variables**
    - Jangan commit `.env` file ke repository
    - Gunakan `.env.example` untuk template

2. **Authentication**
    - Selalu gunakan HTTPS di production
    - Bearer tokens disimpan secara aman
    - Token expiration bisa dikonfigurasi di `config/sanctum.php`

3. **Input Validation**
    - Semua input divalidasi sebelum disimpan
    - SQL injection prevention via Eloquent ORM
    - XSS protection via HTML escaping

4. **CORS Configuration**
    - Configure di `config/cors.php`
    - Whitelist domain yang diizinkan

5. **Rate Limiting**
    - API routes dilindungi dengan middleware throttle
    - Dapat dikonfigurasi di `routes/api.php`

---

## 🚨 Error Handling

API mengembalikan error dengan format konsisten:

```json
{
    "success": false,
    "message": "Error message",
    "errors": {
        "field_name": ["Error detail"]
    }
}
```

### Status Codes

- `200` - OK
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Unprocessable Entity
- `500` - Internal Server Error

---

## 📝 Kontribusi

1. Fork repository
2. Buat feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📞 Support & Documentation

- **API Documentation**: [http://127.0.0.1:8000/api/documentation](http://127.0.0.1:8000/api/documentation)
- **Laravel Documentation**: [https://laravel.com/docs](https://laravel.com/docs)
- **Swagger/OpenAPI**: [https://swagger.io](https://swagger.io)

---

## 📄 License

Proyek ini dilisensikan di bawah MIT License - lihat file [LICENSE](LICENSE) untuk detail.

---

## 👨‍💻 Author

Dikembangkan sebagai API management system untuk dokumen Hak Kekayaan Intelektual (HKI).

**Last Updated**: Juli 2026

---

## 🔄 Changelog

### v1.0.0 (2026-07-12)

- ✨ Initial release
- 📄 Document CRUD operations
- 🗑️ Soft delete & recycle bin
- ♻️ Restore functionality
- 💥 Force delete with file cleanup
- 📊 Export to Excel & PDF
- 📋 Activity logging system
- 📈 Dashboard with statistics
- 📚 Swagger/OpenAPI documentation
- 🔐 Bearer token authentication

```bash
php artisan key:generate
```

Konfigurasi database pada file `.env`

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
