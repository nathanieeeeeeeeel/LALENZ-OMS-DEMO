🍽️ Lalenz Order Management System

A modern and responsive restaurant order management system built to make managing orders, sales, and daily operations simple.






📌 About

Lalenz Order Management System is a restaurant-focused web application designed to provide administrators with a centralized dashboard for monitoring orders, sales, and daily restaurant activity.

The system combines a responsive user interface with dynamic dashboard data, interactive charts, authentication, database integration, and configurable system settings.

It was built as a practical project to explore full-stack web development and the integration of frontend interfaces with backend services and databases.

✨ Features
📊 Admin Dashboard

Get an overview of your restaurant's current activity at a glance.

💰 Today's sales
🛒 Orders today
⏳ Pending orders
✅ Completed orders
📈 Sales trends
📊 Order statistics
🕐 Timezone-aware data
📦 Order Management

Monitor recent orders directly from the dashboard.

Customer information
Order ID
Number of items
Order total
Order status
Order date and time
Automatic status styling
📈 Performance Analytics

Interactive charts powered by Chart.js.

Available time ranges:

Last 24 Hours
Last 7 Days
Last 15 Days
Last 30 Days
Last 60 Days
Last 12 Months

The dashboard separates orders and sales into individual datasets, making it easier to understand restaurant performance over time.

🌙 Dark Mode

The interface supports both light and dark themes.

The selected theme is stored in the browser using localStorage, allowing the user's preference to persist between visits.

🔐 Authentication

Administrative areas are protected using authentication mechanisms including:

JWT
Admin authentication
Protected dashboard access
💱 Currency Support

The system supports configurable currencies and dynamically displays the appropriate currency symbol throughout the dashboard.

🌎 Timezone Support

Order dates and dashboard statistics are calculated using the configured system timezone.

This helps ensure that:

"Today" is calculated correctly
Yesterday's data is accurate
Charts use the correct dates
Order timestamps are displayed according to the configured timezone
🛠️ Tech Stack
Frontend
Technology	Usage
JavaScript	Application logic
jQuery	AJAX and DOM manipulation
Tailwind CSS	UI styling
Bootstrap	Additional UI development
Chart.js	Data visualization
Font Awesome	Icons
Toastr.js	Notifications
Backend
Technology	Usage
PHP	Server-side application
Node.js	JavaScript runtime / tooling
Express.js	REST API development
REST APIs	Data communication
JWT	Authentication
Database
Database	Usage
MongoDB	NoSQL data storage
SQLite	Lightweight relational storage
🖥️ Dashboard

The main dashboard provides a quick snapshot of restaurant operations.

┌─────────────────────────────────────────────────────────────┐
│                       OVERVIEW                              │
│           Real-time overview of today's operations          │
├────────────────┬────────────────┬────────────────┬─────────┤
│  TODAY SALES   │ ORDERS TODAY   │    PENDING     │COMPLETED│
│                │                │                │         │
│   ₱12,450.00   │      42        │       8        │   34    │
├────────────────┴────────────────┴────────────────┴─────────┤
│                                                             │
│                 PERFORMANCE OVERVIEW                        │
│                                                             │
│       ╱╲       Sales                                       │
│      ╱  ╲   ╱╲                                             │
│  ╱╲ ╱    ╲_╱  ╲                                            │
│ ╱  ╲             ╲                                        │
│                                                             │
├─────────────────────────────────────────────────────────────┤
│                     RECENT ORDERS                           │
│                                                             │
│  Customer       Order #       Total          Status        │
│  ───────────────────────────────────────────────────────── │
│  Customer 1      #1024        ₱850.00        Completed     │
│  Customer 2      #1023        ₱420.00        Preparing     │
│  Customer 3      #1022        ₱650.00        Pending       │
└─────────────────────────────────────────────────────────────┘

📈 Analytics

The dashboard dynamically processes order information and generates performance statistics.

24-Hour View

Orders are grouped by hour.

Daily Views

The following ranges group orders by day:

7 Days
15 Days
30 Days
60 Days

Yearly View

The 12-month view groups orders by month.

Jan → Feb → Mar → Apr → May → Jun
 ↓      ↓      ↓      ↓      ↓      ↓
Sales  Sales  Sales  Sales  Sales  Sales

📋 Order Statuses

The system supports multiple order statuses:

Status	Description
🟠 Pending	Order is waiting to be processed
🟡 Preparing	Order is currently being prepared
🟢 Ready	Order is ready
🔵 Out For Delivery	Order is being delivered
🟣 Scheduled	Order is scheduled
🟢 Completed	Order has been completed
🔴 Cancelled	Order has been cancelled
📁 Project Structure
LALENZ_ORDER_SYSTEM/
│
├── Pages/
│   │
│   ├── admin/
│   │   ├── login.php
│   │   └── dashboard.php
│   │
│   ├── Partials/
│   │   ├── navbar.html
│   │   └── footer.php
│   │
│   └── Script/
│       ├── init.php
│       ├── get_orders.php
│       │
│       └── Dashboard/
│           └── navbar.js
│
├── assets/
│   ├── images/
│   └── ...
│
├── index.php
│
└── README.md

🔄 How It Works
                  ┌─────────────────┐
                  │     CUSTOMER    │
                  └────────┬────────┘
                           │
                           ▼
                  ┌─────────────────┐
                  │      ORDER      │
                  │     SYSTEM      │
                  └────────┬────────┘
                           │
                           ▼
                  ┌─────────────────┐
                  │     DATABASE    │
                  │ MongoDB / SQLite│
                  └────────┬────────┘
                           │
                           ▼
                  ┌─────────────────┐
                  │   REST / PHP    │
                  │      API       │
                  └────────┬────────┘
                           │
                           ▼
                  ┌─────────────────┐
                  │ ADMIN DASHBOARD │
                  └─────────────────┘

🎨 UI

The dashboard was designed with a focus on a clean and modern interface.

UI Features
Responsive layout
Mobile-friendly design
Dark mode
Smooth transitions
Interactive cards
Status badges
Hover animations
Responsive charts
Toast notifications
Dynamic content loading
🔒 Security

The project includes several security-related components:

JWT authentication
Protected admin pages
Server-side authentication checks
HTML output escaping
Database-backed authentication
Configurable system settings

For production deployment, additional security measures should be considered, such as:

HTTPS
Secure cookie configuration
CSRF protection
Rate limiting
Environment variables for secrets
Strong password hashing
API request validation
Input sanitization
🚀 Getting Started
Requirements

Before running the project, make sure you have:

PHP
MySQL/MongoDB/SQLite depending on configuration
Apache, Nginx, XAMPP, Laragon, or PHP's built-in server
Node.js if using the Node/Express components
Installation
1. Clone the repository
git clone https://github.com/YOUR_USERNAME/lalenz-order-system.git

2. Enter the project
cd lalenz-order-system

3. Configure the application

Update your configuration files with your:

Database
System Name
Currency
Timezone
Authentication Secrets

4. Start the application

Using PHP's built-in server:

php -S localhost:8000


Then open:

http://localhost:8000

📸 Screenshots

Add your actual screenshots here.

Dashboard — Light Mode

Dashboard — Dark Mode

Login

🧠 What I Learned

This project gave me hands-on experience with:

Building a real-world dashboard
JavaScript DOM manipulation
jQuery AJAX requests
REST API integration
PHP backend development
JWT authentication
Database integration
MongoDB
SQLite
Chart.js
Tailwind CSS
Responsive web design
Dark mode implementation
Date and timezone handling
Dynamic data processing
UI/UX design
🔮 Future Improvements

Some features I'd like to add or improve:

 Real-time order notifications
 Customer management
 Product/menu management
 Inventory tracking
 Advanced sales reports
 Export reports to PDF/Excel
 More detailed analytics
 Role-based admin permissions
 Automated backups
 PWA/mobile support
 Improved API architecture
👨‍💻 Developer

Built with ☕, JavaScript, PHP, and probably too much debugging.

I'm a 22-year-old developer currently exploring full-stack development and building projects to improve my skills.

Technologies I'm familiar with
JavaScript
PHP
Node.js
Express.js
REST APIs
JWT
MongoDB
SQLite
Tailwind CSS
Bootstrap
jQuery


I'm still learning, experimenting, breaking things, fixing them, and trying to make each project better than the last one. 🚀

⭐ Support

If you find this project interesting, consider giving it a ⭐ on GitHub.

<p align="center"> Made with ❤️ and a lot of debugging </p>
