# Smart Data Analysis Dashboard for UMKM

A comprehensive business intelligence dashboard built with Laravel, TailwindCSS, and Alpine.js for small and medium enterprises (UMKM) to visualize and manage their business data.

## 🚀 Features

- **📊 Dashboard Analytics**: Total sales overview, profit & loss summary, sales trends
- **🏆 Top Products**: Real-time tracking of best-selling products
- **📈 Interactive Charts**: Sales trend visualization using Chart.js
- **👥 Customer Management**: Customer insights with demographics and purchase patterns
- **💰 Sales Management**: Complete sales transaction recording and tracking
- **💸 Expense Management**: Operational expense tracking with categorization
- **🔍 Audit Logs**: Complete audit trail for data accountability
- **🌙 Dark Theme**: Modern dark UI with lightning green (#00FF9C) accents

## 🛠️ Tech Stack

- **Backend**: Laravel 12.x (latest)
- **Frontend**: Blade Templates + TailwindCSS (CDN) + Alpine.js
- **Database**: PostgreSQL
- **Charts**: Chart.js
- **Icons**: Heroicons
- **Authentication**: Laravel Breeze
- **Typography**: Inter/Poppins

## 📋 Prerequisites

Before running this application, make sure you have:

- PHP 8.2 or higher
- Composer
- PostgreSQL 13+ 
- Node.js & NPM (for asset compilation)

## 🔧 Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd sda-umkm
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   ```
   
   Update your `.env` file with PostgreSQL credentials:
   ```env
   APP_NAME="SDA UMKM Dashboard"
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=sda_umkm
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

4. **Generate application key**
   ```bash
   php artisan key:generate
   ```

5. **Create PostgreSQL database**
   ```sql
   CREATE DATABASE sda_umkm;
   ```

6. **Run migrations and seeders**
   ```bash
   php artisan migrate --seed
   ```

7. **Install Laravel Breeze (Authentication)**
   ```bash
   composer require laravel/breeze --dev
   php artisan breeze:install blade
   npm install && npm run build
   ```

8. **Start the development server**
   ```bash
   php artisan serve
   ```

## 📊 Database Schema

The application uses the following main entities:

- **Users**: Authentication and user management
- **Products**: Product catalog with images and pricing
- **Customers**: Customer information and demographics
- **Sales**: Sales transactions with detailed line items
- **Expenses**: Operational expense tracking
- **Profit Loss**: Automated profit/loss calculations
- **Audit Logs**: Complete audit trail

All tables use UUID as primary keys and include audit fields (created_by, created_date, updated_by, updated_date).

## 🎨 UI/UX Design

- **Theme**: Dark mode with #111827 background
- **Accent**: Lightning green (#00FF9C) for highlights and CTAs
- **Cards**: Glassmorphism design with subtle shadows
- **Typography**: Inter/Poppins with high contrast
- **Responsive**: Mobile-first design approach
- **Animations**: Smooth transitions and hover effects

## 🔐 Authentication

The application includes role-based authentication:
- **Admin**: Full access to all features and audit logs
- **Staff**: Limited access to daily operations
- **Owner**: Business analytics and reporting focus

## 📱 Responsive Design

Fully responsive design optimized for:
- Desktop (1920px+)
- Tablet (768px - 1024px)
- Mobile (320px - 767px)

## 🚀 Performance Features

- Optimized database queries with proper indexing
- Lazy loading for charts and heavy components
- Efficient caching strategies
- Compressed assets and images

## 📈 Future Enhancements

- Real-time notifications with Laravel Echo
- Advanced reporting with PDF export
- Multi-language support
- API integration for external services
- Advanced analytics with machine learning insights

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.
