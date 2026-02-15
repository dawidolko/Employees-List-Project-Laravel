# Employees-List-Project-Laravel 

> 🚀 **Enterprise Employee Directory Management** - Build comprehensive HR systems with Laravel, advanced filtering, and data export capabilities

## 📋 Description

Welcome to the **Employee Directory App** repository! This Laravel-based application provides a robust solution for managing and analyzing employee data using the MySQL Employees Sample Database. The system features comprehensive employee listings, advanced filtering options, salary analysis, and powerful data export capabilities.

Built with Laravel's elegant architecture and utilizing a realistic dataset from [datacharmer/test_db](https://github.com/datacharmer/test_db), this project demonstrates best practices in database management, query optimization, and enterprise-grade HR system development. Perfect for learning complex database operations, reporting systems, and data-driven web applications.

## 📁 Repository Structure

```

Employees-List-Project-Laravel/
├── 📁 backend/ # Laravel backend application
│ ├── 📁 app/
│ │ ├── 🎮 Http/
│ │ │ └── Controllers/ # Application controllers
│ │ ├── 📦 Models/ # Eloquent ORM models
│ │ │ ├── Employee.php
│ │ │ ├── Department.php
│ │ │ ├── Salary.php
│ │ │ └── Title.php
│ │ └── 🔧 Services/ # Business logic services
│ ├── 📁 config/ # Configuration files
│ ├── 📁 database/
│ │ ├── 🌱 seeders/ # Database seeders
│ │ └── 🔄 migrations/ # Database migrations
│ ├── 📁 routes/
│ │ ├── 🌐 web.php # Web routes
│ │ ├── 🔌 api.php # API endpoints
│ │ ├── 📡 channels.php # Broadcasting channels
│ │ └── ⚙️ console.php # Console commands
│ ├── 📁 resources/
│ │ ├── 📄 views/ # Blade templates
│ │ └── 🎨 css/ # Stylesheets
│ ├── 📁 public/ # Public assets
│ ├── 📁 storage/ # Application storage
│ ├── 📁 tests/ # Unit and feature tests
│ ├── ⚙️ .env.example # Environment template
│ ├── 🎯 artisan # Laravel CLI
│ ├── 📦 composer.json # PHP dependencies
│ ├── 🔧 webpack.mix.js # Asset compilation
│ └── 📖 README.md # Backend documentation
├── 📁 database/ # SQL scripts and diagrams
│ ├── 📊 employees.sql # Sample database
│ ├── 🗂️ schema-diagram.pdf # Database schema
│ └── 📖 README.md # Database documentation
├── 📁 docs/ # Project documentation
│ ├── 📚 user-guide.md
│ └── 🔧 api-reference.md
└── 📖 README.md # Main documentation

```

## 🚀 Getting Started

### 🐳 Quick Start with Docker (Recommended)

The easiest and fastest way to run this project is using Docker. Everything is pre-configured and automated!

**Docker Project Name**: `employeeslist-project`  
**MySQL Port**: 3307 (to avoid conflicts with local MySQL)

```bash
# 1. Clone the repository
git clone https://github.com/dawidolko/Employees-List-Project-Laravel.git
cd Employees-List-Project-Laravel/.tools/docker

# 2. Start the application (first run takes 5-10 minutes)
docker-compose up -d --build

# 3. Access the application
# Web App: http://localhost:8000
# PHPMyAdmin: http://localhost:8080
# MySQL: localhost:3307
```

**That's it!** The Docker setup includes:

- ✅ Laravel application with PHP 8.2
- ✅ MySQL 8.0 database with employee data (~300k employees) on port 3307
- ✅ Nginx web server
- ✅ PHPMyAdmin for database management
- ✅ Automatic configuration and database import
- ✅ Named containers: `employeeslist-mysql`, `employeeslist-app`, `employeeslist-phpmyadmin`

📚 **Detailed Docker Documentation**:

- [Complete Installation Guide](.tools/docker/README.md) - Full manual setup instructions
- [Quick Start Guide](.tools/docker/QUICKSTART.md) - Fast reference
- [FAQ](.tools/docker/FAQ.md) - Common questions and troubleshooting
- [Architecture](.tools/docker/ARCHITECTURE.md) - System architecture diagrams

**Management Commands**:

```bash
cd .tools/docker
./manage.sh start    # Start containers
./manage.sh stop     # Stop containers
./manage.sh status   # View status
./manage.sh logs     # View logs
./manage.sh help     # Show all commands
```

---

### 💻 Manual Installation (Without Docker)

If you prefer to install without Docker, follow these steps:

#### 1. Clone the Repository

```bash
git clone https://github.com/dawidolko/Employees-List-Project-Laravel.git
cd Employees-List-Project-Laravel
```

#### 2. Backend Setup (Laravel)

```bash
cd backend

# Install PHP dependencies
composer install

# Create environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

#### 3. Database Configuration

**Configure Database Connection**

Edit the `.env` file with your MySQL credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=employees_db
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

#### Import Sample Database

Download and import the MySQL Employees Sample Database:

```bash
# Download from https://github.com/datacharmer/test_db
# Create database
mysql -u root -p -e "CREATE DATABASE employees_db;"

# Import SQL scripts
mysql -u root -p employees_db < /path/to/employees.sql
```

#### 4. Start the Application

```bash
# Start Laravel development server
php artisan serve
```

- Access the application at [http://localhost:8000](http://localhost:8000)

---

## ⚙️ System Requirements

### **Essential Tools:**

- **PHP** (version 8.0 or higher)
- **Composer** for PHP dependency management
- **MySQL** (version 5.7 or higher)
- **Git** for version control

### **Development Environment:**

- **Laravel** (latest version)
- **Code Editor** (VS Code, PhpStorm, Sublime Text)
- **Database Management Tool** (phpMyAdmin, MySQL Workbench, DBeaver)
- **Postman** or **Insomnia** for API testing

### **Database Requirements:**

- **MySQL Employees Sample Database** from [datacharmer/test_db](https://github.com/datacharmer/test_db)
- Minimum 200MB storage for database
- Proper MySQL user privileges

### **Recommended Extensions:**

- **Laravel** and **PHP** IntelliSense
- **Laravel Blade Snippets**
- **PHP Debug** for debugging
- **Prettier** for code formatting
- **MySQL** syntax highlighting

### **Laravel Ecosystem:**

- **Eloquent ORM** for database operations
- **Blade Templating Engine** for views
- **Laravel Excel** for data export
- **Laravel Debugbar** for development

## ✨ Key Features

### **👥 Employee Directory**

- Comprehensive employee listing with key information
- Display first name, last name, birth date, and hire date
- Current department and job title information
- Real-time salary data display
- Employee number and gender information

### **🔍 Advanced Filtering System**

- **Employment Status**: Filter current vs. former employees
- **Gender Filter**: Male, Female, or All employees
- **Salary Range**: Minimum and maximum salary filters
- **Department Filter**: Filter by specific departments
- **Date Range**: Filter by hire date periods
- Combined multi-criteria filtering

### **💰 Salary Analysis**

- Display current employee salary
- Calculate total lifetime earnings per employee
- Salary history tracking and analysis
- Department salary statistics
- Salary range reports

### **📊 Data Export Capabilities**

- Export employee data to CSV format
- Comprehensive employee details in exports
- Include total salary sum calculations
- Custom export field selection
- Filtered data export support

### **🏢 Department Management**

- View department assignments
- Department employee count
- Department salary budgets
- Historical department changes

### **📈 Reporting Features**

- Employee statistics and analytics
- Department distribution reports
- Salary trend analysis
- Gender distribution statistics
- Employment duration reports

### **🔐 Data Integrity**

- Proper foreign key relationships
- Transaction-safe operations
- Data validation and constraints
- Historical data preservation

## 🛠️ Technologies Used

- **Laravel** - Robust PHP framework for web applications
- **MySQL** - Relational database management system
- **Eloquent ORM** - Elegant database abstraction layer
- **Blade** - Laravel's powerful templating engine
- **Laravel Excel** - Excel and CSV export functionality
- **Bootstrap** - Responsive frontend framework
- **jQuery** - JavaScript library for DOM manipulation
- **Chart.js** - Data visualization library
- **Composer** - PHP dependency management

## 📚 Database Schema

The application uses the MySQL Employees Sample Database with the following main tables:

- **employees** - Employee personal information
- **departments** - Department details
- **dept_emp** - Employee-department assignments
- **dept_manager** - Department managers
- **titles** - Employee job titles
- **salaries** - Employee salary history

For detailed schema information, refer to the [MySQL Employees Sample Database Documentation](https://dev.mysql.com/doc/employee/en/employees-preface.html).

## 📖 Usage Guide

### **1. View Employee Directory**

Navigate to the main page to see a comprehensive list of all employees with their current information including name, department, job title, and current salary.

### **2. Apply Filters**

Use the filtering panel to refine your search:

- Select employment status (current/former)
- Choose gender
- Set salary range (min/max)
- Select specific departments
- Apply date range filters

### **3. Export Data**

Click the "Export" button to generate a CSV file containing:

- Employee details (name, department, title)
- Current salary information
- Total lifetime earnings
- Custom filtered data

### **4. View Employee Details**

Click on any employee to view detailed information including:

- Complete employment history
- Salary progression
- Department changes
- Title history

## 🖼️ Preview

[<img src="docs/img/GUI1.png" width="80%" alt="Employee Directory Preview"/>](docs/img/GUI1.png)

## 📊 Project Status

✅ **Completed** - Fully functional employee directory management system!

## 🤝 Contributing

Contributions are highly welcomed! Here's how you can help:

- 🐛 **Report bugs** - Found an issue? Let us know!
- 💡 **Suggest improvements** - Have ideas for better features?
- 🔧 **Submit pull requests** - Share your enhancements and solutions
- 📖 **Improve documentation** - Help make the project clearer

Feel free to open issues or reach out through GitHub for any questions or suggestions.

## 👨‍💻 Author

Created by **Dawid Olko** - Part of the Laravel enterprise application series.

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

---
