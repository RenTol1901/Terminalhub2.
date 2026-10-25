# TerminalHub Van & Bus Terminal Management System

A starter PHP/MySQL web application with role-based Admin, Staff, and Passenger dashboards.

## Requirements
- PHP 8.0+ with PDO MySQL enabled
- MySQL 5.7+ / MariaDB 10.4+
- Apache (XAMPP/WAMP/LAMP)

## Setup (XAMPP)
1. Extract `TerminalHub_Van_Bus.zip` into `htdocs/TerminalHub_Van_Bus`.
2. Create a MySQL database by importing `database/terminalhub.sql` in phpMyAdmin.
3. Edit `config.php` if your MySQL username/password differ from the defaults.
4. Open `http://localhost/TerminalHub_Van_Bus/` and sign in.

## Demo accounts
- Admin: `admin@terminalhub.local` / `password`
- Staff: `staff@terminalhub.local` / `password`
- Passenger: `passenger@terminalhub.local` / `password`

These demo passwords are for local testing only. Change them before any real deployment.
The SQL import contains password hashes for the demo password `password`.

## Included
- PDO database connection and prepared statements
- Session login/logout and role checks
- Admin dashboard: summary counts and user/route overview
- Staff dashboard: route and trip overview
- Passenger dashboard: published trip list and booking request form
- Responsive HTML/CSS interface and small JavaScript enhancements

## Important
This is a functional starter/demo, not a production-ready ticketing platform. Before public deployment, add HTTPS, CSRF protection to every state-changing form, rate limiting, email verification/password reset, audit logging, payment integration only through a compliant provider, and operational testing. Review privacy, retention, and local transport regulations.
