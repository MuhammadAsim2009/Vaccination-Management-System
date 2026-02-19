<?php
include 'config/db.php';

// Parent count
$stmt_parentCount = $conn->query("SELECT COUNT(id) AS parentCount FROM users WHERE role = 'parent'");
$parentCount = $stmt_parentCount->fetch_assoc()['parentCount'];

// Hospital count
$stmt_hospitalCount = $conn->query("SELECT COUNT(id) AS hospitalCount FROM users WHERE role = 'hospital'");
$hospitalCount = $stmt_hospitalCount->fetch_assoc()['hospitalCount'];

// Children count
$stmt_childrenCount = $conn->query("SELECT COUNT(id) AS childrenCount FROM children");
$childrenCount = $stmt_childrenCount->fetch_assoc()['childrenCount'] ?? 0;

// Vaccination count
$stmt_vaccineCount = $conn->query("SELECT COUNT(id) AS vaccineCount FROM vaccination_schedule WHERE status = 'vaccinated'");
$vaccineCount = $stmt_vaccineCount->fetch_assoc()['vaccineCount'] ?? 0;

// Appointment count
$stmt_appointmentCount = $conn->query("SELECT COUNT(id) AS appointmentCount FROM appointments");
$appointmentCount = $stmt_appointmentCount->fetch_assoc()['appointmentCount'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Vaccination Management System | VMS</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&amp;display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bs-primary: #0d0dfd;
            --bs-primary-rgb: 13, 13, 253;
            --bs-success: #22c55e;
            --bs-body-font-family: 'Inter', sans-serif;
            --heading-color: #101018;
            --text-muted-custom: #5e5e8d;
            --bg-light-custom: #f5f5f8;
        }

        body {
            font-family: var(--bs-body-font-family);
            color: var(--text-muted-custom);
            -webkit-font-smoothing: antialiased;
        }

        h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 {
            color: var(--heading-color);
            font-weight: 800;
        }

        .text-primary { color: var(--bs-primary) !important; }
        .bg-primary { background-color: var(--bs-primary) !important; }
        .text-success-custom { color: var(--bs-success) !important; }
        .bg-success-custom { background-color: var(--bs-success) !important; }
        
        .bg-light-custom { background-color: var(--bg-light-custom); }
        
        /* Navbar */
        .navbar {
            background-color: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #dadae7;
            height: 80px;
        }
        .nav-link {
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--text-muted-custom);
        }
        .nav-link:hover {
            color: var(--bs-primary);
        }

        /* Hero */
        .hero-gradient {
            background: radial-gradient(circle at 10% 20%, rgba(13, 13, 253, 0.03) 0%, rgba(255, 255, 255, 1) 90%);
        }
        .hero-title {
            font-size: 3rem;
            line-height: 1.1;
            letter-spacing: -0.025em;
        }
        @media (min-width: 992px) {
            .hero-title { font-size: 4.5rem; }
        }

        /* Utilities */
        .soft-shadow {
            box-shadow: 0 10px 30px -10px rgba(13, 13, 253, 0.1);
        }
        .hover-lift {
            transition: all 0.3s ease;
        }
        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: rgba(13, 13, 253, 0.3) !important;
        }
        
        .rounded-4 { border-radius: 1rem !important; }
        .rounded-5 { border-radius: 1.5rem !important; }
        .rounded-circle-custom { border-radius: 50%; }

        /* Scroll Reveal Animation */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 1s cubic-bezier(0.5, 0, 0, 1);
        }
        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* Custom Buttons */
        .btn-primary-custom {
            background-color: var(--bs-primary);
            color: white;
            font-weight: 700;
            padding: 0.625rem 1.5rem;
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(13, 13, 253, 0.2);
            border: none;
            transition: all 0.3s;
        }
        .btn-primary-custom:hover {
            background-color: #0b0bbd;
            color: white;
            transform: translateY(-1px);
        }

        /* Icon Boxes */
        .icon-box {
            width: 3rem;
            height: 3rem;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .bg-primary-soft { background-color: rgba(13, 13, 253, 0.1); color: var(--bs-primary); }
        .bg-success-soft { background-color: rgba(34, 197, 94, 0.1); color: var(--bs-success); }

        /* Process Steps */
        .step-number {
            width: 4rem;
            height: 4rem;
            background-color: var(--bs-primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 900;
            margin: 0 auto;
            box-shadow: 0 20px 25px -5px rgba(13, 13, 253, 0.3);
        }
        .process-line {
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: #f0f0f5;
            transform: translateY(-50%);
            z-index: 0;
        }

        /* Footer */
        footer a { color: var(--text-muted-custom); text-decoration: none; transition: color 0.2s; }
        footer a:hover { color: var(--bs-primary); }

        /* About Image Animation */
        .about-image {
            transform: rotate(3deg);
            transition: transform 0.5s ease;
        }
        .about-image:hover {
            transform: rotate(0deg) scale(1.02);
        }

        /* Contact Button */
        .Contact-btn {
            border-color: rgba(255,255,255,0.2) !important; 
            transition: all 0.3s ease;
        }

        .Contact-btn:hover {
          background: rgba(255,255,255,0.2) !important;
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <header class="sticky-top">
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                    <div class="bg-primary p-1 rounded-2 text-white d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                    <span class="fs-4 fw-bolder text-dark">VMS</span>
                </a>
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-4">
                        <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                        <li class="nav-item"><a class="nav-link" href="#process">Process</a></li>
                        <li class="nav-item"><a class="nav-link" href="#roles">Roles</a></li>
                        <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                    </ul>
                    <div class="d-flex align-items-center gap-3">
                        <a href="auth/login.php" class="text-decoration-none fw-bold text-primary d-none d-sm-block px-3">Log In</a>
                        <a href="auth/register_parent.php" class="btn btn-primary-custom rounded-pill">Get Started</a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <!-- HERO SECTION -->
        <section class="hero-gradient py-5 overflow-hidden reveal">
            <div class="container py-lg-5">
                <div class="row align-items-center gy-5">
                    <div class="col-lg-6">
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-primary-soft mb-4">
                            <i class="fa-solid fa-heart-pulse fs-6"></i>
                            <span class="text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.05em;">Trusted Healthcare Platform</span>
                        </div>
                        <h1 class="hero-title mb-4 text-dark">
                            Vaccination <span class="text-primary">Management</span> System
                        </h1>
                        <p class="lead mb-4" style="color: var(--text-muted-custom);">
                            Track, Schedule, and Manage Child Vaccinations Easily. A secure, centralized platform for parents and hospitals to ensure timely immunization.
                        </p>
                        <div class="d-flex flex-wrap gap-4 pt-2">
                            <div class="d-flex align-items-center gap-2 fw-medium text-dark">
                                <i class="fa-solid fa-circle-check text-success-custom"></i>
                                Fully HIPAA Compliant
                            </div>
                            <div class="d-flex align-items-center gap-2 fw-medium text-dark">
                                <i class="fa-solid fa-circle-check text-success-custom"></i>
                                24/7 Support Available
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 position-relative">
                        <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuCbMN3V0yjzeg2FB8GqfnEFJq7JxhjrRX3tm7dPHBI3iAyvnlLoDglgnFWMlP9GhHyoLJ_NHGslWPpErIk1hVsIDTY0cdT-nQQfsVjkMLdM8j9sW1-Zad7eawQ3MG7BYbnSlu3V6S3Jw4u8AHRNnx-AIQL2YVHeAD0iM9Viwx8aCcyjS4t99N2bt8sTv3sI4tbw0zSiauu4OhFwXTEy_j3_7BZiUYCdjkanQctmMo6CqeL2rsn8zySKwmOHjZ2CiULjGDz5J-4AaRk" 
                             alt="Medical professional" class="img-fluid rounded-5 shadow-lg w-100">
                    </div>
                </div>
            </div>
        </section>

        <!-- STATISTICS -->
        <section class="py-5 bg-white border-top border-bottom reveal">
            <div class="container">
                <div class="row g-4">
                    <div class="col-6 col-lg-3 text-center">
                        <p class="display-5 fw-bolder text-dark mb-0"><?= $childrenCount ?? '0' ?>+</p>
                        <p class="fw-medium text-muted">Children Tracked</p>
                    </div>
                    <div class="col-6 col-lg-3 text-center">
                        <p class="display-5 fw-bolder text-dark mb-0"><?= $vaccineCount ?? '0' ?>+</p>
                        <p class="fw-medium text-muted">Vaccines Given</p>
                    </div>
                    <div class="col-6 col-lg-3 text-center">
                        <p class="display-5 fw-bolder text-dark mb-0"><?= $hospitalCount ?? '0' ?>+</p>
                        <p class="fw-medium text-muted">Hospitals Linked</p>
                    </div>
                    <div class="col-6 col-lg-3 text-center">
                        <p class="display-5 fw-bolder text-dark mb-0"><?= $appointmentCount ?? '0' ?>+</p>
                        <p class="fw-medium text-muted">Appointments</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- FEATURES -->
        <section class="py-5 bg-light-custom reveal" id="features">
            <div class="container py-5">
                <div class="text-center mb-5">
                    <h2 class="display-6 fw-bolder mb-3">Comprehensive Healthcare Tools</h2>
                    <p class="lead text-muted mx-auto" style="max-width: 600px;">Everything you need to manage the immunization lifecycle with precision and ease.</p>
                </div>
                <div class="row g-4">
                    <!-- Feature 1 -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border soft-shadow rounded-4 p-4 hover-lift">
                            <div class="card-body">
                                <div class="icon-box bg-primary-soft mb-4">
                                    <i class="fa-solid fa-baby fs-4"></i>
                                </div>
                                <h3 class="h4 fw-bold mb-3">Child Tracking</h3>
                                <p class="text-muted mb-0">Complete digital health records for every child, accessible anywhere, anytime.</p>
                            </div>
                        </div>
                    </div>
                    <!-- Feature 2 -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border soft-shadow rounded-4 p-4 hover-lift">
                            <div class="card-body">
                                <div class="icon-box bg-success-soft mb-4">
                                    <i class="fa-solid fa-calendar-days fs-4"></i>
                                </div>
                                <h3 class="h4 fw-bold mb-3">Scheduling</h3>
                                <p class="text-muted mb-0">Automated vaccine slot allocation and seamless management for clinics.</p>
                            </div>
                        </div>
                    </div>
                    <!-- Feature 3 -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border soft-shadow rounded-4 p-4 hover-lift">
                            <div class="card-body">
                                <div class="icon-box bg-primary-soft mb-4">
                                    <i class="fa-solid fa-hospital fs-4"></i>
                                </div>
                                <h3 class="h4 fw-bold mb-3">Hospital Dashboard</h3>
                                <p class="text-muted mb-0">A centralized hub for medical staff to manage patients and inventory.</p>
                            </div>
                        </div>
                    </div>
                    <!-- Feature 4 -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border soft-shadow rounded-4 p-4 hover-lift">
                            <div class="card-body">
                                <div class="icon-box bg-primary-soft mb-4">
                                    <i class="fa-solid fa-user-group fs-4"></i>
                                </div>
                                <h3 class="h4 fw-bold mb-3">Parent Dashboard</h3>
                                <p class="text-muted mb-0">Intuitive interface for parents to view records, history, and certificates.</p>
                            </div>
                        </div>
                    </div>
                    <!-- Feature 5 -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border soft-shadow rounded-4 p-4 hover-lift">
                            <div class="card-body">
                                <div class="icon-box bg-success-soft mb-4">
                                    <i class="fa-solid fa-chart-line fs-4"></i>
                                </div>
                                <h3 class="h4 fw-bold mb-3">Detailed Reports</h3>
                                <p class="text-muted mb-0">In-depth analytics for hospitals and compliance reporting for regulators.</p>
                            </div>
                        </div>
                    </div>
                    <!-- Feature 6 -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border soft-shadow rounded-4 p-4 hover-lift">
                            <div class="card-body">
                                <div class="icon-box bg-primary-soft mb-4">
                                    <i class="fa-solid fa-bell fs-4"></i>
                                </div>
                                <h3 class="h4 fw-bold mb-3">Instant Alerts</h3>
                                <p class="text-muted mb-0">Automated SMS and email reminders for upcoming or overdue shots.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- HOW IT WORKS -->
        <section class="py-5 bg-white reveal" id="process">
            <div class="container py-5">
                <div class="text-center mb-5">
                    <h2 class="display-6 fw-bolder mb-3">How It Works</h2>
                    <p class="text-muted">Three simple steps to a healthier tomorrow</p>
                </div>
                <div class="row g-5 position-relative">
                    <div class="process-line d-none d-md-block"></div>
                    <!-- Step 1 -->
                    <div class="col-md-4 position-relative z-1">
                        <div class="card h-100 border soft-shadow rounded-4 p-4 text-center bg-white">
                            <div class="card-body">
                                <div class="step-number mb-4">1</div>
                                <h4 class="fw-bold mb-3">Parent Registers</h4>
                                <p class="text-muted">Create an account and set up profiles for your children in minutes.</p>
                            </div>
                        </div>
                    </div>
                    <!-- Step 2 -->
                    <div class="col-md-4 position-relative z-1">
                        <div class="card h-100 border soft-shadow rounded-4 p-4 text-center bg-white">
                            <div class="card-body">
                                <div class="step-number mb-4">2</div>
                                <h4 class="fw-bold mb-3">Hospital Schedules</h4>
                                <p class="text-muted">Hospitals manage vaccine availability and assign specific appointment slots.</p>
                            </div>
                        </div>
                    </div>
                    <!-- Step 3 -->
                    <div class="col-md-4 position-relative z-1">
                        <div class="card h-100 border soft-shadow rounded-4 p-4 text-center bg-white">
                            <div class="card-body">
                                <div class="step-number mb-4">3</div>
                                <h4 class="fw-bold mb-3">System Tracks</h4>
                                <p class="text-muted">VMS tracks progress, sends reminders, and generates digital certificates.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- USER ROLES -->
        <section class="py-5 bg-light-custom reveal" id="roles">
            <div class="container py-5">
                <div class="text-center mb-5">
                    <h2 class="display-6 fw-bolder">Tailored Panels for Every Role</h2>
                </div>
                <div class="row g-4">
                    <!-- Parent Panel -->
                    <div class="col-lg-4">
                        <div class="card h-100 border-0 shadow-lg rounded-4 p-4 border-bottom border-4 border-primary">
                            <div class="card-body">
                                <div class="text-primary mb-4">
                                    <i class="fa-solid fa-people-roof" style="font-size: 3rem;"></i>
                                </div>
                                <h3 class="h4 fw-bold mb-4">Parent Panel</h3>
                                <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
                                    <li class="d-flex align-items-center gap-3 text-muted fw-medium">
                                        <i class="fa-solid fa-circle-check text-success-custom fs-5"></i>
                                        Digital vaccination cards
                                    </li>
                                    <li class="d-flex align-items-center gap-3 text-muted fw-medium">
                                        <i class="fa-solid fa-circle-check text-success-custom fs-5"></i>
                                        Appointment booking
                                    </li>
                                    <li class="d-flex align-items-center gap-3 text-muted fw-medium">
                                        <i class="fa-solid fa-circle-check text-success-custom fs-5"></i>
                                        Growth tracking tools
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- Hospital Panel -->
                    <div class="col-lg-4">
                        <div class="card h-100 border-0 shadow-lg rounded-4 p-4 border-bottom border-4 border-primary">
                            <div class="card-body">
                                <div class="text-primary mb-4">
                                    <i class="fa-solid fa-briefcase-medical" style="font-size: 3rem;"></i>
                                </div>
                                <h3 class="h4 fw-bold mb-4">Hospital Panel</h3>
                                <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
                                    <li class="d-flex align-items-center gap-3 text-muted fw-medium">
                                        <i class="fa-solid fa-circle-check text-success-custom fs-5"></i>
                                        Patient queue management
                                    </li>
                                    <li class="d-flex align-items-center gap-3 text-muted fw-medium">
                                        <i class="fa-solid fa-circle-check text-success-custom fs-5"></i>
                                        Stock/Inventory control
                                    </li>
                                    <li class="d-flex align-items-center gap-3 text-muted fw-medium">
                                        <i class="fa-solid fa-circle-check text-success-custom fs-5"></i>
                                        Digital verification
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- Admin Panel -->
                    <div class="col-lg-4">
                        <div class="card h-100 border-0 shadow-lg rounded-4 p-4 border-bottom border-4 border-primary">
                            <div class="card-body">
                                <div class="text-primary mb-4">
                                    <i class="fa-solid fa-shield-halved" style="font-size: 3rem;"></i>
                                </div>
                                <h3 class="h4 fw-bold mb-4">Admin Panel</h3>
                                <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
                                    <li class="d-flex align-items-center gap-3 text-muted fw-medium">
                                        <i class="fa-solid fa-circle-check text-success-custom fs-5"></i>
                                        Region-wide statistics
                                    </li>
                                    <li class="d-flex align-items-center gap-3 text-muted fw-medium">
                                        <i class="fa-solid fa-circle-check text-success-custom fs-5"></i>
                                        Hospital onboarding
                                    </li>
                                    <li class="d-flex align-items-center gap-3 text-muted fw-medium">
                                        <i class="fa-solid fa-circle-check text-success-custom fs-5"></i>
                                        System configuration
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ABOUT -->
        <section class="py-5 bg-white overflow-hidden reveal" id="about">
            <div class="container py-5">
                <div class="row align-items-center gy-5">
                    <div class="col-lg-6">
                        <h2 class="display-6 fw-bolder mb-4">Our Mission for a Healthier Future</h2>
                        <p class="lead text-muted mb-4">
                            At VMS, we believe that every child deserves the best start in life. Our platform was built to bridge the gap between healthcare providers and families, ensuring that no essential vaccination is ever missed.
                        </p>
                        <p class="lead text-muted mb-4">
                            We combine cutting-edge technology with rigorous security standards to create a reliable ecosystem that empowers parents and equips hospitals with the tools they need to succeed in their mission of child health protection.
                        </p>
                        <div class="pt-2">
                            <div class="d-flex align-items-center gap-3">
                                <div class="icon-box bg-primary-soft rounded-circle">
                                    <i class="fa-solid fa-users fs-4"></i>
                                </div>
                                <div>
                                    <p class="fw-bold mb-0 text-dark">Community Driven</p>
                                    <p class="small text-muted mb-0">Supporting global health equity</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 position-relative">
                        <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuCz0SZnHsi9Qb7syDPRISV15kpKFP9o2bu4sxOqA721N-bbKhLpiIufjJmkgLCgRUn1EiXk0sJYAFcD9El5qjaXmahywLqBX_z2QhITKRajwByyeYcO4PknaVHawvAPfsy1xwuiGZ48XjCuFNm5USdzRjXz5Zj15ymYey9G6DYC4A5JBP1_OzXyJ3v7JOodIRvvHEqElv1k7sSKOrS_OJ42HrdM17k3keBZ9xE28nWJeFPUbdqz03tfInjHOE85FW7No_ghOIUJdx0" 
                             alt="Smiling children" class="img-fluid rounded-5 shadow-lg about-image">
                        <div class="position-absolute bottom-0 start-0 bg-white p-4 rounded-4 shadow-lg border m-n4" style="transform: translate(-20px, 20px);">
                            <p class="display-5 fw-bold fs-2 text-primary mb-0">100%</p>
                            <p class="small fw-bold text-dark mb-0">Security Record</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- TESTIMONIALS -->
        <section class="py-5 bg-light-custom reveal">
            <div class="container py-5">
                <div class="text-center mb-5">
                    <h2 class="display-6 fw-bolder">Trusted by the Community</h2>
                </div>
                <div class="row g-4">
                    <!-- Testimonial 1 -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border soft-shadow rounded-4 p-4">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <p class="fst-italic text-muted mb-4 fs-5">"VMS has completely changed how I manage my daughter's health. I no longer worry about missing dates thanks to the SMS alerts."</p>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-secondary" style="width: 48px; height: 48px; background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuB22yfC3NE8UEgMzG4Ziaxh-0J9sdhhwtC6f7gZ5M5zd8UbO35-3Yh4LZjSgeok7BuRe6JNeUJbzRC-MQik4XZt5UcrQjmU4TPwOiGvUlBc7YgDJ2a-1FkUxModMQ0wwv1FHm75JR2ie_pIecTtQBDPusEEj-fRUSA8WK4nj5Mzkds1zd_RlcsYXk5-z_QIX151CyZ3OH7hNdbH4IWZ3O5HKAzaG4LJuYiHswd8qpCCKQ9KQ_HbdEjBACivX7YqEmGE-wRT--lotJ4'); background-size: cover;"></div>
                                    <div>
                                        <p class="fw-bold text-dark mb-0">Sarah</p>
                                        <p class="small fw-bold text-primary text-uppercase mb-0">Parent</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Testimonial 2 -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border soft-shadow rounded-4 p-4">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <p class="fst-italic text-muted mb-4 fs-5">"The hospital dashboard is incredibly intuitive. It reduced our administrative paperwork by nearly 60% in the first quarter."</p>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-secondary" style="width: 48px; height: 48px; background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuACLFXk1HTN413KYrFmRJJQJGJ-aA8IamIxE1vSe7goSvDGEh84YMJCK6xIkDxEwqmRaODayalVLmmefq1kL_Y40GMO60Y2HoNsHPEk9wss2W1U8gHfPLuz9FKBiXZR2iBaR69zcYnOkMHOPrb-ER78_dHzApGzlrXoD2KquEi2wVo9f8KZY-1E3wIEVLlONMqYwadQg5A5jZhOvo1cIxWzfDXpAturTjsZwESg1oYPJdeOTzZDrmYts9aL1FBnA-Qw2W1j-MPcLFo'); background-size: cover;"></div>
                                    <div>
                                        <p class="fw-bold text-dark mb-0">Dr. Michael</p>
                                        <p class="small fw-bold text-primary text-uppercase mb-0">Hospital Director</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Testimonial 3 -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border soft-shadow rounded-4 p-4">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <p class="fst-italic text-muted mb-4 fs-5">"As a health worker, having digital records makes verifications instant. It's safe, secure, and very efficient."</p>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-secondary" style="width: 48px; height: 48px; background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDBmxyALjOSIGMf9OLYhkCL060y9kFQK8Uw2gKyCxx4HNPbPHTSn5YXHdyOw8pubutc78CW3Y9lFgHWidUfrGb2IEOeW8W40ml2E26A6dEz_26qXg_b1h7Wp0hh3rR9z6VV3L_WcPGQvKCi0IGF3AnK3b8QzHI2DgDKPSMx6TrwMk1IMtUFHVlgZ6fDxNi4PFbOeCLbAJT_-bWuqH0T1PojMka1Ts6AJdWJontRNGibq7rWOvJNgdLXb15WS_P8Ed3gZZvPpbhloys'); background-size: cover;"></div>
                                    <div>
                                        <p class="fw-bold text-dark mb-0">Sofia</p>
                                        <p class="small fw-bold text-primary text-uppercase mb-0">Medical Staff</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CALL TO ACTION -->
        <section class="py-5 px-3 reveal">
            <div class="container">
                <div class="bg-primary rounded-5 p-5 text-center text-white position-relative overflow-hidden shadow-lg">
                    <div class="position-relative z-1 py-lg-4">
                        <h2 class="display-5 fw-black mb-4 fw-bold text-white">Ready to secure your child's health?</h2>
                        <p class="lead text-white-50 mb-5 mx-auto" style="max-width: 700px;">Join thousands of families and hospitals using VMS to build a safer, immunized future.</p>
                        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                            <a href="auth/register_parent.php" class="btn btn-light btn-lg fw-bold px-5 py-3 rounded-5 text-primary shadow-sm">
                                Get Started Now
                            </a>
                            <a href="#" class="Contact-btn btn-lg text-white border border-2 fw-bold px-5 py-3 rounded-5 text-decoration-none">
                                <i class="fa-solid fa-headset"></i>
                                Contact Support
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- FOOTER -->
    <footer class="bg-white border-top pt-5 pb-4">
        <div class="container">
            <div class="row gy-5 mb-5">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <div class="bg-primary p-1 rounded-2 text-white d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="fa-solid fa-user-shield"></i>
                        </div>
                        <span class="fs-4 fw-bolder text-dark">VMS</span>
                    </div>
                    <p class="text-muted mb-4" style="max-width: 300px;">
                        Securing health records and managing vaccinations for the next generation. Professional, secure, and intuitive.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#" class="btn btn-light rounded-circle d-flex align-items-center justify-content-center text-muted hover-lift" style="width: 40px; height: 40px;">
                            <i class="fa-solid fa-share-nodes fs-5"></i>
                        </a>
                        <a href="#" class="btn btn-light rounded-circle d-flex align-items-center justify-content-center text-muted hover-lift" style="width: 40px; height: 40px;">
                            <i class="fa-solid fa-globe fs-5"></i>
                        </a>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-lg-2 offset-lg-1">
                    <h5 class="fw-bold mb-4 text-dark">Product</h5>
                    <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
                        <li><a href="#features">Features</a></li>
                        <li><a href="#process">Process</a></li>
                        <li><a href="#roles">Roles</a></li>
                        <li><a href="auth/register_parent.php">Get Started</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <h5 class="fw-bold mb-4 text-dark">Company</h5>
                    <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#">Partners</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="mailto:support@vms.com">Contact</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <h5 class="fw-bold mb-4 text-dark">Support</h5>
                    <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Status</a></li>
                        <li><a href="#">Privacy</a></li>
                        <li><a href="#">Terms</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-top pt-4 text-center">
                <p class="small text-muted mb-0">&copy; <?= date('Y'); ?> VMS Systems Inc. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Scroll Reveal Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.1
            };

            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.reveal').forEach(el => {
                observer.observe(el);
            });
        });
    </script>
</body>

</html>