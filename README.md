# Vaccination Management System (VMS)

A comprehensive web-based platform designed to streamline the process of child vaccination by connecting parents, hospitals, and administrators. VMS provides a centralized system for managing vaccination records, scheduling appointments, and monitoring immunization coverage.

## ✨ Key Features

*   **Role-Based Access Control:** Separate, secure dashboards for Parents, Hospitals, and Administrators.
*   **Child Profile Management:** Parents can add and manage detailed profiles for each of their children.
*   **Appointment Booking:** Parents can search for registered hospitals and book vaccination appointments.
*   **Vaccination Scheduling:** Admins approve appointment requests, which automatically creates a vaccination schedule.
*   **Record Tracking:** Hospitals can update the status of appointments (e.g., 'Vaccinated', 'Missed').
*   **Digital Records:** Centralized and accessible vaccination history for every child.
*   **Inventory Management:** Hospitals can manage their vaccine inventory and availability.
*   **Reporting & Analytics:** Dynamic dashboards with charts and statistics for all user roles.
*   **Data Export:** Export reports to CSV and Excel formats.
*   **Notifications:** A system for alerts and reminders (e.g., upcoming vaccinations, appointment confirmations).

## 👥 User Roles

The system is designed for three main user roles:

1.  **👨‍💼 Administrator:**
    *   Oversees the entire system.
    *   Manages hospital and parent accounts.
    *   Approves/rejects appointment requests.
    *   Monitors system-wide statistics and generates reports.
    *   Updates vaccine availability.

2.  **👨‍👩‍👧‍👦 Parent:**
    *   Registers and manages their children's profiles.
    *   Searches for hospitals and books vaccination appointments.
    *   Views upcoming vaccination schedules and historical records.
    *   Receives reminders for due dates.
    *   Downloads vaccination reports and certificates.

3.  **🏥 Hospital:**
    *   Manages incoming vaccination appointments.
    *   Updates the status of vaccinations after they are administered.
    *   Manages their local vaccine inventory and availability.
    *   Views appointment history and statistics for their facility.

## 🛠️ Technology Stack

*   **Backend:** PHP
*   **Database:** MySQL / MariaDB
*   **Frontend:** HTML, CSS, JavaScript
*   **Frameworks/Libraries:**
    *   [Bootstrap 5](https://getbootstrap.com/) for responsive UI components.
    *   [Chart.js](https://www.chartjs.org/) for data visualization and charts.
    *   [Font Awesome](https://fontawesome.com/) for icons.

## 🚀 Getting Started

Follow these instructions to get a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

*   A local web server environment such as [XAMPP](https://www.apachefriends.org/index.html), WAMP, or MAMP.
*   PHP (v7.4 or higher recommended)
*   MySQL or MariaDB

### Installation

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/muhammadasim2009/vaccination-management-system.git
    ```
    Or download the ZIP and extract it.

2.  **Place the project folder** into your web server's root directory (e.g., `C:/xampp/htdocs/`). The final path should be `htdocs/vaccination_management_system`.

3.  **Database Setup:**
    *   Open your database management tool (like phpMyAdmin).
    *   Create a new database named `vaccination_management_system`.
    *   Import the `database/vaccination_management_system.sql` file (you'll need to create this SQL dump from your database) into the newly created database.

4.  **Configure Database Connection:**
    *   Open the file `config/db.php`.
    *   Update the database credentials if they are different from the default:
        ```php
        $host = 'localhost';
        $user = 'root';
        $password = '';
        $database = 'vaccination_management_system';
        ```

5.  **Run the Application:**
    *   Open your web browser and navigate to `http://localhost/vaccination_management_system/`.

## 📂 File Structure

The project follows a modular structure based on user roles:

```
/vaccination_management_system
├── admin/                # Admin panel files
│   ├── bookings/
│   ├── children/
│   ├── export/
│   ├── hospitals/
│   ├── includes/         # Header, sidebar, auth check for admin
│   ├── notifications/
│   ├── profile/
│   ├── requests/
│   ├── vaccination/
│   ├── vaccines/
│   └── dashboard.php
├── assets/               # Global CSS, JS, and image files
│   ├── css/
│   └── js/
├── auth/                 # Login, register, logout files
├── config/               # Database configuration & functions
│   ├── db.php
│   └── functions.php
├── hospital/             # Hospital panel files
│   ├── includes/
│   ├── notifications/
│   ├── profile/
│   ├── vaccination/
│   ├── vaccines/
│   └── dashboard.php
├── parent/               # Parent panel files
│   ├── booking/
│   ├── child/
│   ├── includes/
│   ├── notifications/
│   ├── profile/
│   ├── vaccination/
│   └── dashboard.php
└── index.php             # Main landing page
```

## 🔐 Usage & Credentials

After setting up the project, you can use the following default credentials for testing (assuming they are present in your SQL dump):

*   **Admin:**
    *   Email: `admin@vms.com`
    *   Password: `password`
*   **Hospital:**
    *   Email: `hospital@vms.com`
    *   Password: `password`
*   **Parent:**
    *   Email: `parent@vms.com`
    *   Password: `password`

You can also register new parent and hospital accounts through the registration pages.

## 🤝 Contributing

Contributions are what make the open-source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

1.  Fork the Project
2.  Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3.  Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4.  Push to the Branch (`git push origin feature/AmazingFeature`)
5.  Open a Pull Request

## 📜 License

Distributed under the MIT License. See `LICENSE` for more information.