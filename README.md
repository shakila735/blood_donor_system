# 🩸 BloodNet - Role-Based Blood Donor Finder System

> A comprehensive, secure, and user-friendly web application designed to connect blood donors, requesters, and hospitals efficiently to save lives.

---

## 🌟 Overview

**BloodNet** is a centralized platform that facilitates the process of finding and requesting blood. By utilizing Role-Based Access Control (RBAC), BloodNet provides tailored dashboards and specific functionalities for different user types, including Donors, Requesters, Hospitals, and System Administrators.

## ✨ Key Features

- **Role-Based Access Control (RBAC):** Tailored dashboards and functionalities for `Admin`, `Donor`, `Requester`, and `Hospital` users.
- **Secure Authentication:** Robust login and registration system with secure password hashing and input validation.
- **Dual Login Methods:** Log in conveniently using either an Email address or Phone number.
- **Smart Validation:** Comprehensive client-side and server-side validation (including standard Bangladeshi phone number formatting).
- **Profile Management:** Easy view and complete management of user profile details.
- **Blood Request Management:** Formulate, publish, accept, and track blood requests dynamically.
- **Admin Analytics:** Oversee active users, manage blood requests, and safeguard system integrity.

## 🛠️ Tech Stack

- **Frontend:** HTML5, CSS3, JavaScript 
- **Backend:** PHP
- **Database:** MySQL
- **Environment:** XAMPP  (Apache Server)

## 🚀 Getting Started

Follow these instructions to set up the project locally for development and testing.

### Prerequisites

- [XAMPP](https://www.apachefriends.org/index.html) (or any local server providing PHP & MySQL)

### Setup Instructions

1. **Install the Application**
   Place the project directory inside your local server's web root directory. Ensure the folder name matches your desired path:
   - For XAMPP (Windows): `C:\xampp\htdocs\blood_donor_system`

2. **Database Configuration**
   - Open the **XAMPP Control Panel** and start both `Apache` and `MySQL` modules.
   - Wait for the modules to start on their respective ports.
   - Navigate to `http://localhost/phpmyadmin` in your web browser.
   - *Note:* You do not need to create the database manually. Use the import function immediately.
   - Go to the **Import** tab, browse your local files, and select the `database/schema.sql` file located within this project's directory.
   - Click **Import** at the bottom of the page. This script will automatically create the `bloodnet_db` database, generate the necessary tables, and inject the initial seed data.

3. **Access the Application**
   - Open your browser and navigate to the local server URL:
     ```text
     http://localhost/blood_donor_system
     ```
   - You will step into the BloodNet landing page and can start testing the system.

### 🔑 Default Administrator Credentials

To access the Admin dashboard and manage system data, use the default seeded credentials:

- **Phone:** `01700000000`
- **Password:** `adminpassword`

---

## 📂 Project Structure

```text
blood_donor_system/
├── assets/             # CSS, JS, Images, and other static assets
├── auth/               # Authentication scripts (Login, Register backend logic)
├── config/             # Configuration files (Database connection logic)
├── dashboards/         # Role-specific dashboard views (Admin, Donor, etc.)
├── database/           # Database schema and initial seed SQL files
├── includes/           # Reusable UI components (Navbars, sidebars, modals)
├── index.php           # Application entry point (Landing page routing)
└── README.md           # Core project documentation
```

---

<p align="center">
  <b>Designed to connect, built to save lives. ❤️</b>
</p>
