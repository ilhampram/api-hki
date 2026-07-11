# 📚 API HKI - Dokumentasi Fitur Baru

## ✨ Fitur Baru yang Ditambahkan

### 1. ♻️ Restore Recycle Bin

**Endpoint:** `PUT /api/documents/{id}/restore`

- Memulihkan dokumen yang telah dihapus dari recycle bin
- Memerlukan autentikasi (Sanctum Bearer Token)
- Parameter: `id` (integer - Document ID)
- Response: Success message dengan status dokumen yang dipulihkan

**Contoh Request:**

```bash
curl -X PUT http://127.0.0.1:8000/api/documents/1/restore \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"
```

---

### 2. 📊 Export Excel

**Endpoint:** `GET /api/documents/export/excel`

- Export semua dokumen ke file CSV (kompatibel dengan Excel)
- Memerlukan autentikasi
- Format file: CSV dengan nama `documents_YYYY-MM-DD_HH-i-s.csv`
- Columns: ID, Nomor HKI, Judul, Pemilik, Kategori, User, Tanggal Daftar, Status, Created At

**Contoh Request:**

```bash
curl -X GET http://127.0.0.1:8000/api/documents/export/excel \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -o documents.csv
```

---

### 3. 📄 Export PDF

**Endpoint:** `GET /api/documents/export/pdf`

- Export semua dokumen ke file HTML (dapat diprint sebagai PDF)
- Memerlukan autentikasi
- Format file: HTML dengan styling profesional
- Filename: `documents_YYYY-MM-DD_HH-i-s.html`
- Fitur: Tabel terformatkan dengan header hijau dan alternating row colors

**Contoh Request:**

```bash
curl -X GET http://127.0.0.1:8000/api/documents/export/pdf \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -o documents.html
```

---

### 4. 📋 Activity Log

**Endpoint:** `GET /api/activity-log`

- Melihat riwayat semua aktivitas dalam sistem
- Memerlukan autentikasi
- Query Parameter: `limit` (default: 50)
- Menampilkan: User, Action, Description, Document, IP Address, Timestamp

**Actions yang dicatat:**

- `create` - Dokumen ditambahkan
- `update` - Dokumen diperbarui
- `delete` - Dokumen dihapus ke recycle bin
- `restore` - Dokumen dipulihkan dari recycle bin
- `export_excel` - Export dokumen ke Excel
- `export_pdf` - Export dokumen ke PDF

**Contoh Request:**

```bash
curl -X GET "http://127.0.0.1:8000/api/activity-log?limit=50" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Response Example:**

```json
{
    "success": true,
    "message": "Activity Log",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "user_id": 1,
                "action": "create",
                "description": "Dokumen \"Sistem Inventaris HKI\" ditambahkan",
                "document_id": 1,
                "ip_address": "127.0.0.1",
                "created_at": "2026-07-12T10:30:00.000000Z",
                "user": {
                    "id": 1,
                    "name": "Admin",
                    "email": "admin@example.com"
                },
                "document": {
                    "id": 1,
                    "judul": "Sistem Inventaris HKI"
                }
            }
        ],
        "total": 10
    }
}
```

---

### 5. 📈 Dashboard Chart

**Endpoint:** `GET /api/dashboard/chart`

- Mendapatkan data statistik untuk dashboard dengan chart/grafik
- Memerlukan autentikasi
- Menampilkan:
    - Statistik per kategori
    - Statistik per status
    - Statistik bulanan
    - Jumlah dokumen di recycle bin
    - Summary ringkas

**Contoh Request:**

```bash
curl -X GET http://127.0.0.1:8000/api/dashboard/chart \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Response Example:**

```json
{
    "success": true,
    "message": "Data Chart Dashboard",
    "data": {
        "category_statistics": [
            {
                "nama": "Patent",
                "total": 5
            },
            {
                "nama": "Trademark",
                "total": 3
            }
        ],
        "status_statistics": [
            {
                "status": "Pending",
                "total": 4
            },
            {
                "status": "Disetujui",
                "total": 2
            },
            {
                "status": "Ditolak",
                "total": 2
            }
        ],
        "monthly_statistics": [
            {
                "bulan": "2026-07",
                "total": 8
            }
        ],
        "recycle_bin_count": 2,
        "summary": {
            "total_documents": 8,
            "pending": 4,
            "approved": 2,
            "rejected": 2
        }
    }
}
```

---

## 🗂️ Database Changes

### New Table: `activity_logs`

- Menyimpan riwayat aktivitas semua pengguna
- Kolom:
    - `id` - Primary key
    - `user_id` - Foreign key ke users table
    - `action` - Jenis aksi (create, update, delete, restore, export_excel, export_pdf)
    - `description` - Deskripsi detail aktivitas
    - `document_id` - Foreign key ke documents table (nullable)
    - `ip_address` - IP address pengguna
    - `created_at` - Timestamp aktivitas
    - `updated_at` - Timestamp update

**Migration Command:**

```bash
php artisan migrate
```

---

## 🔄 Activity Logging

Sistem secara otomatis mencatat aktivitas pada method:

- **Store (Tambah):** Mencatat dokumen yang ditambahkan
- **Update (Edit):** Mencatat perubahan dokumen
- **Delete (Hapus):** Mencatat dokumen yang dihapus
- **Restore (Pulihkan):** Mencatat dokumen yang dipulihkan
- **Export Excel:** Mencatat jumlah dokumen yang diexport
- **Export PDF:** Mencatat jumlah dokumen yang diexport

Setiap log menyimpan:

- User ID yang melakukan aksi
- Deskripsi aksi
- Document ID (jika relevan)
- IP Address
- Timestamp

---

## 🚀 Cara Menggunakan

### 1. Jalankan Migration

```bash
php artisan migrate
```

### 2. Login untuk mendapatkan Token

```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password"
  }'
```

### 3. Gunakan Token untuk mengakses fitur baru

```bash
# Contoh: Export Excel
curl -X GET http://127.0.0.1:8000/api/documents/export/excel \
  -H "Authorization: Bearer YOUR_TOKEN"

# Contoh: Lihat Activity Log
curl -X GET http://127.0.0.1:8000/api/activity-log \
  -H "Authorization: Bearer YOUR_TOKEN"

# Contoh: Lihat Dashboard Chart
curl -X GET http://127.0.0.1:8000/api/dashboard/chart \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## 📖 Swagger Documentation

Semua endpoint telah didokumentasikan dengan OpenAPI/Swagger Attributes:

- ✅ Restore Recycle Bin (PUT /api/documents/{id}/restore)
- ✅ Export Excel (GET /api/documents/export/excel)
- ✅ Export PDF (GET /api/documents/export/pdf)
- ✅ Activity Log (GET /api/activity-log)
- ✅ Dashboard Chart (GET /api/dashboard/chart)

**Untuk melihat dokumentasi Swagger**, akses URL yang sudah dikonfigurasi di project Anda.

---

## 🔐 Keamanan

- Semua fitur baru memerlukan autentikasi
- Menggunakan Laravel Sanctum untuk API authentication
- IP Address dicatat untuk audit trail
- Activity Log tidak bisa dimodifikasi langsung (read-only untuk user biasa)

---

## 📝 Notes

1. File export disimpan dalam memori dan langsung didownload (streaming)
2. Export PDF menghasilkan format HTML yang dapat diprint ke PDF menggunakan browser
3. Activity Log menyimpan hingga 50 records per halaman
4. Semua timestamps menggunakan timezone sesuai config Laravel Anda

---

**Last Updated:** 2026-07-12
**Version:** 1.1.0 (dengan fitur baru)
