# CitiServe – Database Setup (citiserve_db)

This document explains the **MySQL database structure** for CitiServe and how to **import** it using phpMyAdmin so that every group member has the same tables and sample data.

---


## Local database configuration - IMPORTANT!!!!!!!! This is how you can see it when you clone this repo
PLEASE READ NUMBER 3 IN THE OVERVIEW TO IMPORT THE DATABASE FIRST THEN YOU CAN FOLLOW THIS STEP

1. **Create the database and import data**
   - Open phpMyAdmin (e.g., `http://localhost/phpmyadmin`).
   - Create a database named `citiserve_db`.
   - Select `citiserve_db` → **Import** tab.
   - Choose the file: `database/citiserve_latest.sql` from this project.
   - Click **Go**.

2. **Configure your local database connection**
   - Copy the example config:

     ```bash
     cp app/config/database.example.php app/config/database.php
     ```

   - Open `app/config/database.php` and set:
     - `database` → the name of your DB (usually `citiserve_db`)
     - `username` → your own MySQL/phpMyAdmin username  
       (for example, `root` or `phpmyadmin`)
     - `password` → your own MySQL/phpMyAdmin password

3. **Test the connection**
   - Open in your browser:

     `http://localhost/CitiServe/public/test_services.php`

   - You should see a “Connected to citiserve_db” message and a list of document services.






## 1. Overview

Database name (recommended): **`citiserve_db`**

CitiServe is a web-based Barangay Complaint Management and Document Request System.  
The database mainly covers:

- **User accounts** (residents, staff, admin)
- **Barangay document services** and **requests**
- **Complaint categories** and **complaint records**

You will find an SQL export file in this project:

- `database/citiserve_latest.sql`

Importing this file will automatically create all required tables and insert some initial records (e.g., document services, complaint categories).

---

## 2. Tables and their purpose

### 2.1 `users`

**Purpose:** Stores all user accounts for the system (residents, staff, admin).

**Important columns:**

- `id` – Primary key.
- `full_name` – Full name of the user.
- `email` – Email address (must be unique).
- `password_hash` – Encrypted password (hash).
- `role` – User role:
  - `resident`
  - `staff`
  - `admin`
- `address` – Address of the resident.
- `contact_number` – Contact number.
- `created_at`, `updated_at` – Timestamps.

This table is used for login, authentication, and role-based access (resident vs. staff/admin).

---

### 2.2 `document_services`

**Purpose:** Lists all barangay document services that residents can request.

**Example entries:**

- Barangay Clearance  
- Certificate of Residency  
- Barangay Indigency  

**Important columns:**

- `id` – Primary key.
- `name` – Name of the document service.
- `description` – Short description of the service.
- `price` – Official price for the document.
- `processing_time_days` – Estimated number of days to process.
- `is_active` – Whether the service is active (1) or not (0).
- `created_at`, `updated_at` – Timestamps.

This table supports the **Document Request** feature: showing available services, prices, and processing time.

---

### 2.3 `document_requests`

**Purpose:** Stores every document request submitted by residents.

**Relationships:**

- `user_id` → references `users.id`
- `document_service_id` → references `document_services.id`

**Important columns:**

- `id` – Primary key.
- `user_id` – The resident who made the request.
- `document_service_id` – The type of document requested.
- `purpose` – Purpose or reason for requesting the document.
- `status` – Current status of the request. Possible values:
  - `received` – Request has been received by the system.
  - `pending` – Staff is processing the request.
  - `claimable` – The document is ready to be claimed.
  - `rejected` – Request was rejected.
  - `released` – Document was successfully released to the resident.
- `payment_reference` – Reference number / notes for payment (e.g., GCash ref.).
- `payment_proof_path` – File path to the uploaded proof of payment.
- `claimed_at` – When the resident claimed the document (if applicable).
- `created_at`, `updated_at` – Timestamps.

This table is used in the **Resident Interface** (request history) and **Barangay Staff Interface** (review/approve/update status).

---

### 2.4 `complaint_categories`

**Purpose:** Categorizes complaints for easier filtering and reporting.

**Example entries:**

- Noise Disturbance  
- Sanitation  
- Peace and Order  
- Infrastructure  

**Important columns:**

- `id` – Primary key.
- `name` – Category name.
- `description` – Short description of the category.
- `is_active` – Whether the category is active (1) or not (0).

Residents select from these categories when submitting complaints.

---

### 2.5 `complaints`

**Purpose:** Stores complaints submitted by residents (or anonymous users).

**Relationships:**

- `user_id` → references `users.id` (can be `NULL` if anonymous)
- `category_id` → references `complaint_categories.id`

**Important columns:**

- `id` – Primary key.
- `user_id` – The resident who submitted the complaint (nullable for anonymous).
- `category_id` – Complaint category.
- `is_anonymous` – `1` if the complaint is anonymous, `0` otherwise.
- `title` – Short title/summary of the complaint.
- `description` – Detailed description of the issue.
- `location` – Location of the incident (e.g., street, area).
- `status` – Current status of the complaint. Example values:
  - `submitted` – Complaint has been submitted.
  - `under_review` – Staff is reviewing the complaint.
  - `in_progress` – Action is being taken.
  - `resolved` – Issue has been resolved.
  - `rejected` – Complaint was rejected.
- `created_at`, `updated_at` – Timestamps.

This table powers the **Complaint Management** part of the system, including complaint submission, history, and admin updates.

---

## 3. How to import the database (for group members)

Follow these steps to set up the CitiServe database on your own computer.

### 3.1 Requirements

- Local web server (XAMPP/WAMP/LAMP, etc.).
- MySQL running.
- phpMyAdmin accessible (usually at `http://localhost/phpmyadmin`).

### 3.2 Steps to import `citiserve_latest.sql`

1. **Open phpMyAdmin**

   - Go to `http://localhost/phpmyadmin` in your browser.
   - Log in with your MySQL username and password  
     (often `root` with no password on local setups, unless you changed it).

2. **Create a new database**

   - Click the **Databases** tab at the top.
   - In **Create database**, enter:  
     `citiserve_db`
   - Choose collation: `utf8mb4_unicode_ci` (recommended).
   - Click **Create**.

3. **Select the new database**

   - In the left sidebar, click **`citiserve_db`**.

4. **Import the SQL file**

   - With `citiserve_db` selected, click the **Import** tab at the top.
   - Click **Choose File** (or **Browse**).
   - Navigate to the project folder and select:

     `database/citiserve_latest.sql`

   - Make sure the format is **SQL**.
   - Click **Go**.

5. **Wait for completion**

   - If the import is successful, you will see a green success message.
   - In the left sidebar, under `citiserve_db`, you should now see tables like:
     - `users`
     - `document_services`
     - `document_requests`
     - `complaint_categories`
     - `complaints`

You now have the same database structure and sample data as the original developer.

---

## 4. Common issues & quick fixes

- **Error: Unknown database `citiserve_db`**

  - You forgot to create the database before importing.
  - Fix: Go to **Databases** → create `citiserve_db` → select it → then import.

- **Error: Access denied for user**

  - Your MySQL username/password are incorrect.
  - Fix: Use the same credentials you normally use to log in to phpMyAdmin.

- **After import, I see no tables**

  - You may have imported into the wrong database or import failed.
  - Fix: Make sure you select `citiserve_db` in the left sidebar **before** importing.
  - Check the message at the top of phpMyAdmin for any errors.

---

## 5. Next steps (for developers)

After successfully importing `citiserve_db`:

1. Configure your PHP project to connect to this database (e.g., in `app/config/database.php`).
2. Use the tables described above to:
   - Implement registration
