# 🎫 Support Ticket System

A simple raw PHP-based support ticket system built without any frameworks. Useful for small-scale support operations or learning purposes.

---

## 📁 Installation

1. Clone or download this repository.
2. Place the project folder into your local server's `htdocs` directory (e.g. if using XAMPP).
3. Import the `ticket.sql` file into your MySQL database via **phpMyAdmin**.
4. Update database connection settings in the `db.php` file:

```php
// db.php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'your_database_name';
```

5. Open your browser and go to:

```
http://localhost/support-ticket-system/
```

---

## 👥 User Roles

### Admin

- Email: `admin@gmail.com`  
- Password: `12345678`

### User

- Email: `user@gmail.com`  
- Password: `12345678`

> Any newly registered user will automatically be assigned the `user` role.

---

## 🛠 Features

### Admin

- View all tickets  
- Reply to any ticket  
- Change ticket status (`Open`, `Pending`, `Close`)  

### User

- Register and log in  
- Create new support tickets  
- View all tickets they have submitted  

---

## 💡 Tech Stack

- PHP (Plain/Raw)  
- MySQL  
- HTML & Bootstrap (UI Styling)  

---

## 📝 Notes

- I haven't worked in raw PHP for a while, so I took some assistance from ChatGPT.
- This project does not use any frameworks.
- All features are implemented using plain PHP and MySQL.

---
