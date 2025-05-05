# EXPO Technopreneurship

EXPO Technopreneurship adalah platform showcasing UMKM yang dibangun untuk membantu pertumbuhan UMKM melalui inovasi digital.

## Daftar Isi

- [EXPO Technopreneurship](#expo-technopreneurship)
  - [Daftar Isi](#daftar-isi)
  - [Teknologi yang Digunakan](#teknologi-yang-digunakan)
  - [Persyaratan](#persyaratan)
  - [Instalasi dan Setup](#instalasi-dan-setup)
  - [Struktur Proyek](#struktur-proyek)
  - [Build untuk Production](#build-untuk-production)
  - [Deployment](#deployment)
    - [Server Requirements](#server-requirements)
  - [Author](#author)

## Teknologi yang Digunakan

- **Backend**: Laravel 12.x
- **Frontend**: Vue.js 3.x dengan TypeScript
- **CSS Framework**: Tailwind CSS
- **Komponen UI**: shadcn/ui
- **Bundler**: Vite
- **State Management**: Vue 3 Reactivity
- **HTTP Client**: Axios
- **Form Handling**: Inertia.js

## Persyaratan

- PHP >= 8.3
- Node.js >= 16.x
- Composer
- MySQL atau PostgreSQL

## Instalasi dan Setup

1. **Clone Repository**

   ```bash
   git clone https://github.com/FadhilHere/expo_techno.git
   cd expo-technopreneurship
   ```

2. **Instalasi Dependency Backend**

   ```bash
   composer install
   ```

3. **Konfigurasi Environment**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi Database**

   Edit file `.env` dan sesuaikan konfigurasi database:

   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=expo_technopreneurship
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Membuat Symlink Storage**

   ```bash
   php artisan storage:link
   ```

6. **Migrasi dan Seeding Database**

   ```bash
   php artisan migrate
   php artisan db:seed
   ```

7. **Instalasi Dependency Frontend**

   ```bash
   npm install
   ```

8. **Menjalankan Development Server**

   Dalam terminal terpisah, jalankan server Laravel:
   ```bash
   php artisan serve
   ```

   Dalam terminal terpisah lainnya, jalankan Vite untuk frontend:
   ```bash
   npm run dev
   ```

## Struktur Proyek

```
expo-technopreneurship/
├── app/                 # Direktori utama Laravel
│   ├── Http/            # Controllers, Middleware, dan Requests
│   │   ├── Controllers/ # Controller
│   │   └── Resources/   # API Resources
│   │   
│   └── Models/          # Eloquent Models
├── database/            # Migrations dan Seeders
├── public/              # Assets yang dapat diakses publik
│   └── assets/          # Gambar dan file statis
├── resources/           # Frontend resources
│   ├── js/
│   │   ├── components/  # Komponen Vue reusable
│   │   │   └── ui/      # shadcn/ui components
│   │   ├── layouts/     # Template layout
│   │   └── pages/       # Halaman Vue (auth, user, admin)
│   └── views/           # Laravel Blade templates
├── routes/              # Definisi rute Laravel 
├── storage/             # File yang diupload, logs, dan cache
└── tests/               # Unit dan Feature tests
```

## Build untuk Production

1. **Kompilasi Assets Frontend**

   ```bash
   npm run build
   ```

2. **Optimisasi Laravel untuk Production**

   ```bash
   php artisan optimize
   php artisan route:cache
   php artisan config:cache
   php artisan view:cache
   ```

## Deployment

### Server Requirements

- PHP >= 8.3
- Composer
- Node.js >= 16.x (hanya dibutuhkan saat build)
- Nginx atau Apache
- MySQL atau PostgreSQL
- SSL Certificate (disarankan)

## Author

**Fadhil Parmata**

---

Dikembangkan dengan ❤️ untuk UMKM Indonesia
