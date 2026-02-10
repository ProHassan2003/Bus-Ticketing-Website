# GreenBus 🚌

GreenBus is a simple bus travel booking web application built using PHP. The system allows users to search for routes, log in to their account, and manage bookings through a dashboard interface. This project was developed as part of coursework to practice backend PHP development, session handling, and secure form processing.

The homepage provides a journey search form where users can select departure city, destination, date, and number of passengers.

---

## Features

Journey search form (From / To / Date / Passengers)
User login and registration system
Dashboard for logged-in users
Booking management page
Session-based authentication
CSRF token protection for forms
Navigation bar with account management
Simple responsive UI

---

## Tech Stack

Frontend:
HTML
CSS

Backend:
PHP

Concepts Used:
Sessions
Form handling (POST requests)
Authentication system
CSRF protection
Reusable PHP includes

Tools:
VS Code
XAMPP / MAMP
Git & GitHub

---

## Project Structure

GreenBus
│
├── index.php
├── login.php
├── register.php
├── logout.php
├── dashboard.php
├── notes.php
│
├── config.php
├── library.php
│
├── assets/
│   └── style.css
│
└── README.md

---

## How It Works

The homepage displays a travel search form where users enter:

* departure city
* destination city
* travel date
* number of passengers

If the user is not logged in, they can still search but must log in to save a booking.

Sessions are used to track logged-in users, and CSRF tokens are used to protect form submissions.

After login, users can manage bookings from the dashboard.

---

## Installation

Clone the repository:

git clone [https://github.com/YOUR_USERNAME/greenbus.git](https://github.com/YOUR_USERNAME/greenbus.git)

Move the project into:

htdocs/

Start Apache from XAMPP or MAMP.

Open in browser:

[http://localhost/greenbus](http://localhost/greenbus)

---

## Learning Outcomes

This project helped me understand:

* Building multi-page PHP applications
* Implementing login systems with sessions
* Using CSRF tokens for security
* Structuring PHP projects using includes
* Handling forms and POST requests
* Creating a simple booking workflow

---

## Future Improvements

Database-based booking storage
Bus schedule system
Seat selection feature
Admin dashboard
Payment simulation
API-based route search

---

## Author

Hassan Jehangir
Programming & Internet Technologies Student
Vilnius Business College
