# MarketNest

MarketNest is a small-scale PHP + MySQL e-commerce application designed for a university software development assignment. It includes customer shopping flows, authentication, order management, and an admin dashboard.

## Features

- User registration and login
- Customer profile and order history
- Product browsing, search, and category filters
- Shopping cart and checkout
- Order placement and status tracking
- Admin dashboard
- Product CRUD and category management
- User management and payment record visibility
- Responsive storefront UI

## Technology Stack

- PHP 8+
- MySQL
- Bootstrap 5
- Vanilla JavaScript
- Font Awesome
- PDO prepared statements

## Requirements

- XAMPP for Windows
- Apache + MySQL enabled
- PHP 8+

## XAMPP Setup

1. Install XAMPP.
2. Start Apache and MySQL.
3. Place the project in C:\xampp\htdocs\marketnest.
4. Open phpMyAdmin.
5. Import the SQL file from database/ecommerce.sql.
6. Open the application in a browser at:
   http://localhost/marketnest/public/

## Database Setup

Import the file below:

- database/ecommerce.sql

The database name is:

- ecommerce_db

## Default Login Accounts

Admin:
- Email: adminmarketnest123@gmail.com
- Password: Abi98068@

Customer:
- Email: customer@marketnest.com
- Password: customer123

## Project Structure

- config/
- includes/
- public/
- database/

## Security Notes

- Passwords are stored using password_hash
- SQL uses prepared statements
- CSRF tokens are used for important forms
- Session-based authentication is used
- Access checks protect admin areas
- Sensitive keys should be stored in environment variables or server config before production use

## Payment Gateway Setup

This project includes Khalti and eSewa placeholders in the payment configuration. For sandbox testing, update the values in config/payment.php or set environment variables before deploying.

## Testing

1. Register a new customer account.
2. Log in and browse products.
3. Add items to the cart and place an order.
4. Confirm order and payment status from the customer area.
5. Log in as admin and manage products, categories, users, and orders.

## Future Improvements

- More polished payment callback verification
- Better image upload handling and dashboard analytics
- Improved order filtering and reporting

## Screenshots

Add screenshots here once the app is running in your XAMPP environment.
