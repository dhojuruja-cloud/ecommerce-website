# MarketNest Project Report

## 1. Project Overview
MarketNest is a PHP + MySQL e-commerce web application created for an assignment/project. The system allows customers to browse products, search by category, add products to cart, log in, and place orders. The admin side provides user and product management features.

## 2. Objective
The main goal of the project was to build a working online shopping website with:
- customer-facing storefront
- login and registration
- product listing and details
- cart and checkout flow
- admin dashboard
- basic payment integration structure

## 3. Technology Stack
- PHP 8+
- MySQL / MariaDB
- Bootstrap 5
- JavaScript
- XAMPP
- PDO database connection

## 4. Project Structure
- config/ : app configuration and database connection
- includes/ : shared functions and layout partials
- public/ : customer-facing pages and admin pages
- database/ : SQL database file
- uploads/ : product images

## 5. Default Credentials
### Admin
- Email: admin@marketnest.com
- Password: admin123

### Customer
- Email: customer@marketnest.com
- Password: customer123

## 6. Functional Features
### Customer Features
- User registration and login
- View product catalog
- Search and filter products by category
- Add products to cart
- Quantity control
- View product details
- Account dashboard with user information
- Order tracking and order history

### Admin Features
- Admin login
- Product management
- Category management
- User management
- Order management
- Payment record visibility

## 7. Major Modules
### Authentication Module
Users can register, login, and remain authenticated using PHP sessions. Admin users are redirected to the admin dashboard after login.

### Product Module
Products are stored in the database and displayed through product cards and product detail pages. Images are stored in the uploads folder and the filename is saved in the database.

### Cart Module
The cart is controlled using session data and allows customers to manage quantities and continue buying.

### Order Module
Orders are stored with user and product details so customers can view order status and history.

### Admin Module
Admin users can manage products, categories, users, and orders from a dashboard interface.

## 8. Image Handling
The project stores product images in the uploads folder and keeps the image file name in the database.
This approach is commonly used in real-world e-commerce systems because it is efficient and easier to manage than storing raw binary image data inside the database.

## 9. Payment Integration
The project includes payment-related structures and placeholders for eSewa and Khalti. In a real production setup, the actual merchant credentials and callback verification logic must be implemented securely using the official payment gateway documentation.

## 10. Challenges Solved
- Product images not showing correctly
- Missing or hidden View Details buttons
- Guest users being redirected to login before adding to cart
- Missing account dashboard details
- Correct user flow from product list to login to account

## 11. Setup Instructions
1. Install XAMPP.
2. Start Apache and MySQL.
3. Place the project in the htdocs folder.
4. Import the database file from database/ecommerce.sql into MySQL.
5. Open the app in the browser:
   http://localhost/marketnest/public/
6. Login using the default admin or customer credentials.

## 12. Conclusion
MarketNest is a functional e-commerce assignment project that demonstrates core web development concepts such as PHP backend logic, session management, relational database design, product management, and user flow handling. It provides a strong foundation for future enhancement, including advanced payment integration, analytics, and order processing features.

## 13. Future Improvements
- Real eSewa callback verification
- More advanced checkout flow
- Better validation and error handling
- Improved admin analytics
- Better product image management and deletion
- Responsive mobile polish
