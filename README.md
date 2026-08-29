# Homies Cars 🚗

**Car rental web application built with PHP and MySQL**, featuring user authentication, dynamic vehicle management, reservation handling, availability verification, and secure database operations.

## 🛠️ Technologies

* **Backend:** PHP
* **Database:** MySQL / MariaDB
* **Frontend:** HTML, CSS, JavaScript
* **Environment:** XAMPP
* **Database management:** phpMyAdmin

## ✨ Features

* User registration and authentication
* Secure password hashing with `password_hash()` and `password_verify()`
* Dynamic vehicle catalogue retrieved from the database
* Vehicle filtering by category
* Vehicle availability verification based on reservation dates
* Reservation creation and cancellation
* Automatic server-side price calculation
* User reservation history
* Contact form
* Session-based authentication
* Prepared SQL statements to prevent SQL injection
* Foreign keys, constraints and indexes for database integrity

## 🗄️ Database

The application uses a relational database composed of **6 tables**:

```text
categories
     │
     ▼
voitures ◄──────── reservations ────────► users
     │                    │
     │                    ▼
     └──────────────► agences

messages_contact
```

### Main entities

* `users` — registered users and authentication data
* `voitures` — vehicles, prices, categories and availability status
* `categories` — vehicle categories
* `agences` — vehicle pickup locations
* `reservations` — rental reservations and their status
* `messages_contact` — customer contact requests

The database includes foreign keys, unique constraints, validation rules and indexes designed to ensure data consistency and efficient reservation queries.

## 📂 Project Structure

| File                           | Description                           |
| ------------------------------ | ------------------------------------- |
| `homies_cars.php`              | Home page                             |
| `index.php`                    | Entry point / redirection             |
| `vehicules.php`                | Dynamic vehicle catalogue             |
| `login.html`                   | Login and registration interface      |
| `auth.php`                     | Authentication and session management |
| `reservation1.php`             | Reservation interface and processing  |
| `mes-reservations.php`         | User reservation history              |
| `contact.html` / `contact.php` | Contact form                          |
| `db.php`                       | Centralized database connection       |
| `schema.sql`                   | Database schema and reference data    |
| `*.css`                        | Application styles                    |
| `*.js`                         | Client-side interactions              |

## 🔄 Reservation Workflow

```text
Browse vehicles
      ↓
Select a vehicle
      ↓
Authentication
      ↓
Choose agency & rental dates
      ↓
Check vehicle availability
      ↓
Calculate total price server-side
      ↓
Create reservation
      ↓
Manage reservation
```

Reservations require an authenticated account. Availability is checked on the server before a reservation is created, and the total price is calculated from the database rather than from user-provided values.

## 🔐 Security

The project includes several security improvements:

* Prepared SQL statements using `bind_param()`
* Password hashing and verification
* Session-based authentication
* Server-side validation
* Server-side price calculation
* Protected reservation actions
* Generic error messages for users
* Server-side error logging
* Foreign keys and database constraints

## 🧪 Testing

The main application workflow was tested locally using **PHP + MariaDB**, including:

* User registration
* User login and logout
* Vehicle catalogue and category filtering
* Reservation creation
* Availability validation
* Prevention of overlapping reservations
* Reservation cancellation
* Reservation history
* Contact form submission

## 🚀 Installation

### 1. Clone the repository

```bash
git clone https://github.com/meryembouras28/Homies-Cars.git
```

### 2. Place the project in XAMPP

Copy the project into:

```text
C:\xampp\htdocs\homies-cars
```

### 3. Create the database

Import `schema.sql` using **phpMyAdmin** or MySQL:

```bash
mysql -u root -p < schema.sql
```

The script creates the `homies_cars` database, its tables and reference data.

### 4. Start XAMPP

Start:

* Apache
* MySQL

### 5. Open the application

```text
http://localhost/homies-cars/
```

The database connection is configured centrally in `db.php`.

## 🔮 Future Improvements

* Email verification during registration
* Admin dashboard for vehicle and reservation management
* Environment variables for production configuration
* Online deployment
* Enhanced reservation management and notifications

