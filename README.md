# Timeline

Pembuat garis waktu interaktif untuk menyusun peristiwa menjadi cerita yang mudah dipahami.

## Fitur

- Buat, edit, hapus, dan buka kembali timeline serta peristiwa.
- Timeline vertikal dengan navigasi antarkejadian.
- Urutan kronologis berdasarkan tanggal atau manual dengan drag-and-drop; posisi manual tetap tersimpan saat berganti mode.
- Tombol naik/turun sebagai alternatif untuk layar sentuh dan keyboard.
- Tautan unik untuk berbagi dalam mode hanya lihat.
- Penyimpanan SQLite dan antarmuka responsif berbahasa Indonesia.

## Teknologi

Laravel 13, Blade, SQLite, HTML5, CSS3, Vanilla JavaScript, dan Vite.

## Menjalankan

Gunakan PHP 8.5, Composer, dan Node.js 22.12+.

```sh
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
npm ci
npm run build
php artisan serve --host=127.0.0.1
```

Buka http://127.0.0.1:8000. Untuk mengembangkan aset, jalankan `npm run dev` di terminal lain.
Pengelolaan tanpa login dibatasi ke koneksi lokal. Jangan teruskan aplikasi melalui tunnel atau proxy publik tanpa autentikasi. Tautan `/s/{token}` hanya untuk membaca; penerima memerlukan akses jaringan ke server.

Pengujian: `php artisan test` dan `npm run build`.
