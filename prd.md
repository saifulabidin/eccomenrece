Product Requirements Document (PRD): Katalog Online WhatsApp Checkout

1. Pendahuluan

1.1 Visi & Tujuan Proyek

Membuat sebuah platform e-commerce (katalog online) yang modern, cepat, dan interaktif. Tujuan utamanya adalah untuk memamerkan produk secara profesional, sekaligus menyederhanakan proses pemesanan dengan mengarahkan semua transaksi checkout langsung ke nomor WhatsApp Admin yang telah ditentukan.

1.2 Target Pengguna (Persona)

Admin (Penjual)

Pemilik bisnis yang membutuhkan interface admin yang powerful, cepat, dan mudah untuk mengelola inventaris produk, kategori, dan konfigurasi toko (seperti nomor WA) tanpa perlu coding.

Pelanggan (Pembeli)

Pengguna website yang ingin pengalaman browsing produk yang cepat dan interaktif (tanpa full page reload), serta proses checkout yang familier dan instan via WhatsApp.

2. Arsitektur & Tumpukan Teknologi (Tech Stack)

Development Environment: Laravel Sail (Manajemen kontainer Docker).

Database: PostgreSQL (Dijalankan via Sail).

Backend Framework: Laravel (versi terbaru).

ORM: Eloquent (Bawaan Laravel).

Frontend - Interaktivitas: Livewire (versi terbaru). Seluruh komponen dinamis di sisi publik (katalog, pencarian, keranjang) akan dibangun sebagai komponen Livewire.

Frontend - Styling (Publik): Bootstrap 5. Layout utama dan styling website publik akan menggunakan Bootstrap.

Admin Panel - Framework: Filament (versi terbaru). Seluruh interface admin (login, dashboard, CRUD) akan dibangun dan dikelola sepenuhnya oleh Filament.

Admin Panel - Styling (Admin): Tailwind CSS (Bawaan standar Filament, terisolasi dari frontend publik).

3. Fitur Fungsional - Website Publik (Frontend via Livewire)

Ini adalah fitur yang dilihat dan digunakan oleh Pelanggan. Seluruh interaksi dinamis (filter, tambah ke keranjang, dll.) harus menggunakan Livewire untuk menghindari full page reload.

3.1 Halaman Utama (Homepage)

Menampilkan banner promosi (jika ada).

Menampilkan daftar kategori produk.

Menampilkan produk unggulan (Featured Products).

3.2 Katalog Produk

Menampilkan semua produk dengan layout grid.

Implementasi lazy loading atau paginasi (via Livewire).

Setiap produk menampilkan: Gambar, Nama, Harga.

3.3 Filter & Pencarian Produk

Pencarian: Input pencarian real-time (component Livewire) untuk mencari produk berdasarkan nama.

Filter: Filter produk berdasarkan Kategori (component Livewire).

3.4 Halaman Detail Produk

Galeri gambar produk.

Nama produk, harga, dan deskripsi lengkap.

Tombol "Tambah ke Keranjang" dengan opsi memilih kuantitas (component Livewire).

3.5 Keranjang Belanja (Shopping Cart)

Dapat diakses sebagai modal atau sidebar (component Livewire).

Menampilkan daftar produk di keranjang.

Memungkinkan pengguna mengubah kuantitas atau menghapus item dari keranjang (interaksi Livewire).

Menampilkan subtotal dan total harga.

3.6 Proses Checkout

Tombol "Checkout via WhatsApp" di dalam keranjang belanja.

Saat diklik, sistem akan mengumpulkan semua item di keranjang (Nama Produk, Kuantitas, Harga) dan memformatnya menjadi satu pesan teks.

Pengguna akan di-redirect ke api.whatsapp.com/send?phone=[NomorAdmin]&text=[PesanOtomatis]

Pesan Otomatis berisi: "Halo, saya ingin memesan:\n\n1. [Produk A] (x[Qty]) - [Harga]\n2. [Produk B] (x[Qty]) - [Harga]\n\nTotal: [Total Harga]\n\n[Data Pembeli - Opsional, bisa ditambahkan form singkat sebelum checkout]"

4. Fitur Fungsional - Panel Admin (Backend via Filament)

Ini adalah backend interface yang digunakan oleh Admin (Penjual) untuk mengelola toko.

4.1 Autentikasi

Halaman login standar Filament untuk Admin.

Proteksi route admin.

4.2 Dashboard

Halaman utama setelah login.

Menampilkan statistik sederhana (misal: Jumlah Produk, Jumlah Kategori).

4.3 Manajemen Kategori (CRUD)

Membuat, Membaca, Mengedit, dan Menghapus Kategori.

Field: Nama Kategori, Slug (otomatis), Deskripsi (opsional).

4.4 Manajemen Produk (CRUD)

Membuat, Membaca, Mengedit, dan Menghapus Produk.

Field:

Nama Produk

Slug (otomatis)

Deskripsi (Rich Text Editor)

Kategori Produk User Manual Input

Variasi/Attribute (misal: Warna, Ukuran - opsional) User Manual Input

Harga

Harga Diskon (opsional)

Harga Varian (opsional, untuk produk dengan variasi)

Stok (opsional)

Stok Varian (opsional, untuk produk dengan variasi)

Relasi ke Kategori (Dropdown/Select)

Upload Gambar (Tunggal/Ganda)

Status (Draft/Published)

4.5 Konfigurasi Toko

Resource/Halaman khusus untuk pengaturan global toko.

Field yang paling penting: Nomor WhatsApp Admin (digunakan untuk tujuan checkout).

Field lain: Nama Toko, Alamat (opsional), Deskripsi Toko (opsional).