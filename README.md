# Employee Directory App (Laravel)

This project is an application for managing an employee directory. It is built using **Laravel** and utilizes a sample MySQL database from [datacharmer/test_db](https://github.com/datacharmer/test_db), following the guidelines from the [MySQL Employees Sample Database Documentation](https://dev.mysql.com/doc/employee/en/employees-preface.html).

---

## Project Structure

- **backend/** – Laravel application (backend server).
- **docs/** – Project documentation.
- **database/** – SQL scripts and diagrams related to the sample database.

```
project
├── README.md
├── docs
├── database
├── backend
│   ├── app
│   ├── bootstrap
│   ├── config
│   ├── database
│   ├── public
│   ├── resources
│   ├── routes
│   │   ├── api.php
│   │   ├── channels.php
│   │   ├── console.php
│   │   └── web.php
│   ├── storage
│   ├── tests
│   ├── vendor
│   ├── .env
│   ├── artisan
│   ├── composer.json
│   ├── composer.lock
│   └── webpack.mix.js
```

---

## Features

- **Employee Listing**: Displays a list of employees including key details such as first name, last name, department, job title, and current salary.
- **Filtering Options**: Provides filters for selecting current/former employees, gender, salary range, and department.
- **Data Export**: Enables exporting employee data with comprehensive details—first name, last name, current department, job title, current salary, and the total sum of all salaries paid throughout their employment.

---

## Requirements

- **PHP >= 8.x** (with Composer)
- **MySQL** or another supported database for Laravel
- **Laravel** framework

---

## Quick Installation (Recommended) 🚀

**One-command setup** - automatycznie konfiguruje cały projekt:

```bash
git clone https://github.com/dawidolko/Employees-List-Project-Laravel
cd Employees-List-Project-Laravel
./setup.sh
```

Skrypt automatycznie:

- ✅ Sprawdzi wymagania systemowe (PHP >= 8.2, MySQL, Composer)
- ✅ Uruchomi MySQL jeśli nie działa
- ✅ Utworzy bazę danych `employees`
- ✅ Pobierze i zaimportuje dane testowe (~300,000 rekordów)
- ✅ Zainstaluje wszystkie zależności PHP i Node.js
- ✅ Skonfiguruje plik `.env`
- ✅ Wygeneruje klucz aplikacji
- ✅ Uruchomi serwer deweloperski

### Wymagania

- **PHP >= 8.2** (z rozszerzeniami: mbstring, xml, pdo_mysql, curl, zip)
- **Composer** (https://getcomposer.org)
- **MySQL 5.7+** lub **MariaDB 10.3+**
- **Git** (opcjonalnie, do pobrania danych testowych)

### Po instalacji

Aplikacja będzie dostępna pod adresem: **http://localhost:8000**

Aby uruchomić serwer ponownie:

```bash
cd backend
php artisan serve
```

---

## Manual Installation (Alternative)

1. **Clone the repository**:

   ```bash
   git clone https://github.com/dawidolko/Employees-List-Project-Laravel
   cd Employees-List-Project-Laravel
   ```

2. **Backend (Laravel) setup**:

   ```bash
   cd backend
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database Setup**:

   - Create database:

     ```bash
     mysql -u root -p -e "CREATE DATABASE employees CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
     ```

   - Download and import test data:

     ```bash
     git clone https://github.com/datacharmer/test_db.git
     cd test_db
     mysql -u root -p employees < employees.sql
     ```

   - Configure `.env` file with your database credentials

4. **Run the application**:

   ```bash
   php artisan serve
   ```

---

## Usage

- **Employee Directory**: View a comprehensive list of employees with details such as first name, last name, department, job title, and current salary.
- **Filtering**: Use filters to display only current or former employees, select by gender, specify a salary range, or filter by department.
- **Data Export**: Generate CSV files containing employee details along with the total sum of all salaries received during their employment.

---

## Configuration

1. **Database Connection**:
   - Update the `.env` file with your MySQL credentials.
2. **Importing the Sample Database**:
   - Follow the instructions from the [MySQL Employees Sample Database Documentation](https://dev.mysql.com/doc/employee/en/employees-preface.html) to properly import the data.
3. **Customizing Filters and Export Options**:
   - The filtering logic and export functionality can be adjusted within the controllers to meet further specific requirements.

---

## Author

Created by **Dawid Olko** as part of a practice project.  
Feel free to reach out with any questions or feedback.

---

## Project Status

**In Development** – Finished!
