# Kuza Kizazi Finance

A robust and modern finance management web application built for [Kuza Kizazi](https://kuzakizazi.com).

## 👨‍💻 Developer
This project was specially developed by **Ian Adams Kelly**, Founder of Kuza Kizazi. 
- **Website:** [kuzakizazi.com](https://kuzakizazi.com)

## 🛠️ Technologies Used
- **Backend Framework:** Laravel 12 (PHP 8.2+)
- **Frontend Stack:** Alpine.js & Tailwind CSS
- **Data Visualization:** Chart.js
- **Roles & Permissions:** Spatie Laravel Permission
- **PDF Generation:** Barryvdh Laravel DOMPDF

## 🚀 Getting Started

Follow these instructions to set up the project on your local machine.

### Prerequisites
- PHP >= 8.2
- Composer
- Node.js & NPM
- Database (MySQL, SQLite, or PostgreSQL)

### Installation Steps

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd kuzakizazi-finance
   ```

2. **Install PHP and Node dependencies:**
   ```bash
   composer install
   npm install
   ```

3. **Environment Setup:**
   Copy the `.env.example` file and rename it to `.env`:
   ```bash
   cp .env.example .env
   ```
   Then generate the application key:
   ```bash
   php artisan key:generate
   ```

4. **Database Configuration:**
   Make sure you have created your database, then update the `.env` file with your specific database credentials (`DB_DATABASE`, `DB_USERNAME`, etc.).

5. **Run Migrations and Seed:**
   This will create your database tables and populate them with initial data.
   ```bash
   php artisan migrate --seed
   ```

6. **Compile Frontend Assets:**
   ```bash
   npm run build
   ```
   *(Or use `npm run dev` while you are actively developing)*

7. **Serve the Application LocallY:**
   ```bash
   php artisan serve
   ```
   The application will now be running and accessible at `http://localhost:8000`.

## 📜 License

This project is proprietary software belonging to Kuza Kizazi. All rights reserved.
