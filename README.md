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

## Installation

1. **Clone the repository**:

   ```bash
   git clone https://gitlab.ideo.pl/m.koszyk/pracownicy
   cd pracownicy
   ```

2. **Backend (Laravel) setup**:

   - Install dependencies:
     ```bash
     composer install
     ```
   - Copy the `.env.example` to `.env` and configure your database connection settings:
     ```bash
     cp .env.example .env
     ```
   - Generate an application key:
     ```bash
     php artisan key:generate
     ```

3. **Database Setup**:

   - Download the sample MySQL database from [datacharmer/test_db](https://github.com/datacharmer/test_db).
   - Create a new database (e.g. `employees_db`) and import the SQL scripts:
     ```bash
     mysql -u root -p employees_db < employees.sql
     ```

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
