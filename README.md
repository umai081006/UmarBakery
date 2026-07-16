# Umar Bakery

![Umar Bakery Banner](https://via.placeholder.com/1200x400.png?text=Umar+Bakery+-+Artisan+Bread+%26+Pastries)

> **Umar Bakery** adalah platform e-commerce dan sistem manajemen pesanan internal (Private Repository).

## 🚀 Fitur Utama
*   **Customer Portal:** Penjelajahan produk, keranjang belanja, *wishlist*, dan sistem *checkout*.
*   **Order Management:** Pelacakan pesanan dan status pengiriman.
*   **Payments:** Integrasi Midtrans untuk pembayaran otomatis.
*   **Admin Dashboard:** Laporan penjualan harian, manajemen produk, dan log stok.

## 🛠️ Stack Teknologi
*   Laravel 11 (PHP 8.3+)
*   PostgreSQL
*   Tailwind CSS & Alpine.js
*   Midtrans & Cloudinary

## 📦 Panduan Setup Internal

1.  **Clone Repository**
    ```bash
    git clone https://github.com/umai081006/UmarBakery.git
    cd UmarBakery
    ```

2.  **Install Dependensi**
    ```bash
    composer install
    npm install
    ```

3.  **Konfigurasi Environment**
    Salin `.env.example` ke `.env` dan konfigurasikan akses ke PostgreSQL, Midtrans, dan Cloudinary.

4.  **Database & Aset**
    ```bash
    php artisan key:generate
    php artisan migrate
    npm run build
    ```

## 🔒 Catatan Keamanan & Deployment
*   Repositori ini bersifat **Private** dan berisi kode hak cipta bisnis Umar Bakery.
*   Untuk deployment server, gunakan konfigurasi `.env.production` (pastikan `APP_DEBUG=false`).
*   Panduan konfigurasi Nginx, Cron, dan Backup tersedia di folder `docs/`.

---
© 2026 Umar Bakery. Hak Cipta Dilindungi Undang-Undang.
