# Support Ticket System

A simple raw PHP-based support ticket system.

## 📁 Installation

1. **Clone or download** this repository.
2. Place the project folder inside your `htdocs` directory (if using XAMPP).
3. Import the `ticket.sql` file into your MySQL database using **phpMyAdmin**.
4. Open the `db.php` file and update your database connection details.

```php
// db.php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'your_database_name';
