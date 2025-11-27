# 🎓 LMS/ Course Online Backend API (Laravel 12)

Repository ini berisi source code Backend untuk aplikasi Learning Management System. Dibangun menggunakan **Laravel 12**, menyediakan RESTful API untuk manajemen kursus, kuis, progres siswa, hingga penerbitan sertifikat.

## 🛠️ Teknologi

-   **Framework:** Laravel 12
-   **Database:** MySQL
-   **Authentication:** Laravel Sanctum
-   **Storage:** Local Storage (Symlink)

---

## 🚀 Cara Install & Menjalankan (Local)

Untuk menjalankan backend ini di komputer lokal, ikuti langkah berikut:

1.  **Clone Repository**

    ```bash
    git clone https://github.com/ax71/Project-class-V2.git
    cd Project-class-V2
    ```

2.  **Install Dependencies**

    ```bash
    composer install
    ```

3.  **Setup Environment**

    -   Copy file `.env.example` menjadi `.env`.
    -   Sesuaikan setting database di file `.env`:

    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=nama_database_anda
    DB_USERNAME=root
    DB_PASSWORD=
    ```

4.  **Generate Key & Migrate**

    ```bash
    php artisan key:generate
    php artisan migrate
    ```

5.  **Setup Storage (Wajib untuk File Materi & Sertifikat)**
    Agar file yang diupload bisa diakses publik (frontend):

    ```bash
    php artisan storage:link
    ```

6.  **Jalankan Server**
    ```bash
    php artisan serve
    ```
    API akan berjalan di: `http://127.0.0.1:8000/api`

---

## 🔐 Aturan Request API

1.  **Headers Wajib:**
    Setiap request harus menyertakan header ini agar return berupa JSON (bukan HTML):

    ```http
    Accept: application/json
    ```

2.  **Authentication:**
    Untuk endpoint yang diproteksi (Protected Routes), tambahkan Token di Header:
    ```http
    Authorization: Bearer <access_token>
    ```

---

## 📚 Dokumentasi Endpoint

### 1. Authentication

| Method | Endpoint    | Deskripsi              | Body (JSON)                                          |
| :----- | :---------- | :--------------------- | :--------------------------------------------------- |
| `POST` | `/register` | Daftar User Baru       | `name`, `email`, `password`, `role` ('admin'/'user') |
| `POST` | `/login`    | Masuk & Dapat Token    | `email`, `password`                                  |
| `POST` | `/logout`   | Hapus Token (Auth)     | -                                                    |
| `GET`  | `/user`     | Cek Profil Saya (Auth) | -                                                    |

### 2. Courses (Kursus)

| Method   | Endpoint        | Deskripsi           | Body                   |
| :------- | :-------------- | :------------------ | :--------------------- |
| `GET`    | `/courses`      | List Semua Kursus   | -                      |
| `POST`   | `/courses`      | Buat Kursus (Admin) | `title`, `description` |
| `GET`    | `/courses/{id}` | Detail Kursus       | -                      |
| `PUT`    | `/courses/{id}` | Edit Kursus         | `title`, `description` |
| `DELETE` | `/courses/{id}` | Hapus Kursus        | -                      |

### 3. Materials (Materi Belajar)

_Catatan: Gunakan `form-data` untuk upload file._

| Method   | Endpoint          | Deskripsi              | Body (Form-Data)                                         |
| :------- | :---------------- | :--------------------- | :------------------------------------------------------- |
| `GET`    | `/materials`      | List Materi per Course | Query Param: `?course_id=1`                              |
| `POST`   | `/materials`      | Upload Materi          | `course_id`, `title`, `content_type` (pdf/video), `file` |
| `DELETE` | `/materials/{id}` | Hapus Materi           | -                                                        |

### 4. Quiz System (Ujian)

| Method | Endpoint        | Deskripsi             | Body (JSON)                         |
| :----- | :-------------- | :-------------------- | :---------------------------------- |
| `GET`  | `/quizzes`      | List Kuis per Course  | Query Param: `?course_id=1`         |
| `POST` | `/quizzes`      | Buat Judul Kuis       | `course_id`, `title`, `description` |
| `GET`  | `/quizzes/{id}` | Detail Soal & Jawaban | -                                   |
| `POST` | `/questions`    | Tambah Soal + Jawaban | _Lihat contoh JSON di bawah_        |

**Contoh JSON Tambah Soal (`POST /questions`):**

```json
{
    "quiz_id": 1,
    "question_text": "Apa nama ibukota Indonesia?",
    "answers": [
        { "answer_text": "Jakarta", "is_correct": true },
        { "answer_text": "Bandung", "is_correct": false }
    ]
}
```
