# BloodNet - Role-Based Blood Donor Finder System

## Setup Instructions for Phase 1

1. **Database Setup**
   - Open XAMPP and start the `Apache` and `MySQL` modules.
   - Go to `http://localhost/phpmyadmin`.
   - You don't need to create a database manually; simply go to the "Import" tab and select `database/schema.sql` located inside this project directory.
   - It will automatically create the `bloodnet_db` database and insert the necessary tables and initial seed data.

2. **Access the System**
   - Head to `http://localhost/blood_donor_system` in your browser.
   - You will see the landing page.

3. **Default Admin Login**
   - Phone: `01700000000`
   - Password: `adminpassword`

4. **Features Implemented (Phase 1)**
   - Registration (with role selection Donor, Requester, Hospital)
   - Login (Phone or Email)
   - Dashboard Routing (Role-Based Access Guard)
   - Profile Management
   - Server-Side and Client-Side validations (Bangladeshi Phone number format, required fields, duplicate phone checks)
   - Password Hashing and verification.
