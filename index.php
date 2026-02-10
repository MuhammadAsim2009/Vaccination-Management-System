<?php
include 'config/db.php';

// Parent count
$stmt_parentCount = $conn->query("SELECT COUNT(id) AS parentCount FROM users WHERE role = 'parent'");
$parentCount = $stmt_parentCount->fetch_assoc()['parentCount'];

// Hospital count
$stmt_hospitalCount = $conn->query("SELECT COUNT(id) AS hospitalCount FROM users WHERE role = 'hospital'");
$hospitalCount = $stmt_hospitalCount->fetch_assoc()['hospitalCount'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vaccination Management System</title>
    
    <!-- Google Fonts: Inter for modern UI -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        /* Landing Page Specific Styles */
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }

        /* Navbar Styling */
        .navbar {
            backdrop-filter: blur(10px);
            background-color: rgba(255, 255, 255, 0.95);
        }

        /* Hero Section Styling */
        .hero-section {
            background: linear-gradient(135deg, #0d6efd 0%, #0099ff 100%);
            color: white;
            padding: 100px 0 150px;
            position: relative;
            overflow: hidden;
        }

        /* Abstract Background Shapes */
        .hero-shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }
        .shape-1 { width: 300px; height: 300px; top: -50px; right: -50px; }
        .shape-2 { width: 150px; height: 150px; bottom: 50px; left: 10%; }

        /* Login Cards Section */
        .login-cards-section {
            margin-top: -80px;
            position: relative;
            z-index: 10;
        }

        .role-card {
            border: none;
            border-radius: 16px;
            transition: all 0.3s ease;
            background: white;
            overflow: hidden;
        }

        .role-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .icon-wrapper {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
            transition: all 0.3s ease;
        }

        .role-card:hover .icon-wrapper {
            transform: scale(1.1);
        }

        /* Soft Background Colors for Icons */
        .bg-soft-primary { background-color: rgba(13, 110, 253, 0.1); color: #0d6efd; }
        .bg-soft-success { background-color: rgba(25, 135, 84, 0.1); color: #198754; }
        .bg-soft-warning { background-color: rgba(255, 193, 7, 0.1); color: #ffc107; }

        /* Feature Icons */
        .feature-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            font-size: 1.25rem;
        }

        /* Footer */
        footer {
            background-color: #fff;
            border-top: 1px solid #e9ecef;
        }

        footer a.text-secondary:hover {
            color: #fff !important;
            transition: color 0.3s ease;
        }

        .placeholder-white::placeholder {
            color: #6c757d !important;
        }
    </style>
</head>
<body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="#">
                <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center" style="width: 32px; height: 32px;">
                    <i class="fa-solid fa-feather-pointed fs-6"></i>
                </div>
                <span class="text-primary">VMS</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                    <li class="nav-item ms-lg-3">
                        <a href="auth/register_parent.php" class="btn btn-primary px-4 rounded-pill">Sign In</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section text-center d-flex align-items-center">
        <div class="hero-shape shape-1"></div>
        <div class="hero-shape shape-2"></div>
        
        <div class="container position-relative z-1">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <span class="badge bg-white text-white bg-opacity-25 border border-white border-opacity-50 px-3 py-2 rounded-pill mb-3">
                        <i class="fa-solid fa-shield-heart me-2"></i> Trusted Healthcare Platform
                    </span>
                    <h1 class="display-4 fw-bold mb-4">Vaccination Management System</h1>
                    <p class="lead mb-5 text-white-50">
                        A comprehensive digital solution connecting parents, hospitals, and administrators. 
                        Ensuring a healthier future through efficient immunization tracking.
                    </p>
                </div>
            </div>
        </div>
    </header>

    <!-- Login Selection Cards -->
    <section class="login-cards-section pb-5">
        <div class="container">
            <div class="row g-4 justify-content-center">
                
                <!-- Parent Card -->
                <div class="col-md-6 col-lg-4">
                    <div class="card role-card shadow-lg h-100 p-4 text-center">
                        <div class="card-body">
                            <div class="icon-wrapper bg-soft-primary">
                                <i class="fa-solid fa-child"></i>
                            </div>
                            <h3 class="card-title fw-bold mb-3">Parent Login</h3>
                            <p class="card-text text-muted mb-4">
                                Access your child's vaccination records, schedule appointments, and receive timely reminders.
                            </p>
                            <a href="auth/login.php" class="btn btn-outline-primary w-100 py-2 fw-medium rounded-pill stretched-link">
                                Go to Login <i class="fa-solid fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Hospital Card -->
                <div class="col-md-6 col-lg-4">
                    <div class="card role-card shadow-lg h-100 p-4 text-center">
                        <div class="card-body">
                            <div class="icon-wrapper bg-soft-success">
                                <i class="fa-solid fa-hospital"></i>
                            </div>
                            <h3 class="card-title fw-bold mb-3">Hospital Login</h3>
                            <p class="card-text text-muted mb-4">
                                Manage vaccine inventory, update patient records, and coordinate vaccination drives efficiently.
                            </p>
                            <a href="auth/login.php" class="btn btn-outline-success w-100 py-2 fw-medium rounded-pill stretched-link">
                                Go to Login <i class="fa-solid fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Admin Card -->
                <div class="col-md-6 col-lg-4">
                    <div class="card role-card shadow-lg h-100 p-4 text-center">
                        <div class="card-body">
                            <div class="icon-wrapper bg-soft-warning">
                                <i class="fa-solid fa-user-shield"></i>
                            </div>
                            <h3 class="card-title fw-bold mb-3">Admin Login</h3>
                            <p class="card-text text-muted mb-4">
                                Oversee system operations, manage user accounts, and generate comprehensive health reports.
                            </p>
                            <a href="auth/login.php" class="btn btn-outline-warning w-100 py-2 fw-medium rounded-pill stretched-link text-dark">
                                Go to Login <i class="fa-solid fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- About Us Section -->
    <section class="py-5 bg-light" id="about">
        <div class="container py-4">
            <div class="row align-items-center">
                <div class="col-lg-6 order-lg-2 mb-4 mb-lg-0">
                    <div class="ps-lg-5">
                        <span class="text-primary fw-bold text-uppercase small letter-spacing-1">About Us</span>
                        <h2 class="display-6 fw-bold mb-4 text-dark">Dedicated to Child Healthcare</h2>
                        <p class="lead text-muted mb-4">
                            We are a team of healthcare professionals and technology experts committed to improving immunization coverage rates globally.
                        </p>
                        <p class="text-muted mb-4">
                            The Vaccination Management System was born out of a need to bridge the gap between parents and healthcare providers. By digitizing records and automating reminders, we ensure that no child misses a life-saving vaccine due to oversight or lack of information.
                        </p>
                        <div class="row g-4 mb-4">
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <div class="bg-white shadow-sm rounded-circle p-2 me-3 text-primary">
                                        <i class="fa-solid fa-users fs-5"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0"><?= $parentCount ?? '0' ; ?>+</h5>
                                        <small class="text-muted">Happy Parents</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <div class="bg-white shadow-sm rounded-circle p-2 me-3 text-primary">
                                        <i class="fa-solid fa-hospital-user fs-5"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0"><?= $hospitalCount ?? '0' ; ?>+</h5>
                                        <small class="text-muted">Hospitals</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="#" class="btn btn-primary rounded-pill px-4">Learn More</a>
                    </div>
                </div>
                <div class="col-lg-6 order-lg-1">
                    <div class="position-relative">
                        <div class="bg-white p-3 rounded-4 shadow-lg position-relative z-1">
                             <div class="bg-primary bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center" style="height: 400px;">
                                <i class="fa-solid fa-heart-pulse text-primary opacity-50" style="font-size: 8rem;"></i>
                             </div>
                        </div>
                        <div class="position-absolute top-0 start-0 translate-middle bg-warning rounded-circle" style="width: 100px; height: 100px; opacity: 0.2; z-index: 0;"></div>
                        <div class="position-absolute bottom-0 end-0 translate-middle bg-success rounded-circle" style="width: 150px; height: 150px; opacity: 0.1; z-index: 0;"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5 bg-white" id="features">
        <div class="container py-4">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="pe-lg-5">
                        <h2 class="fw-bold mb-4 text-dark">Why Choose VMS?</h2>
                        <p class="text-muted mb-4">
                            Our platform simplifies the complex process of vaccination management, ensuring no child is left behind. We provide a secure and user-friendly environment for all stakeholders.
                        </p>
                        
                        <div class="d-flex mb-3">
                            <div class="feature-icon bg-primary bg-opacity-10 text-primary me-3">
                                <i class="fa-solid fa-bell"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold">Smart Reminders</h5>
                                <p class="text-muted small">Automated SMS and email notifications for upcoming vaccinations.</p>
                            </div>
                        </div>

                        <div class="d-flex mb-3">
                            <div class="feature-icon bg-success bg-opacity-10 text-success me-3">
                                <i class="fa-solid fa-database"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold">Digital Records</h5>
                                <p class="text-muted small">Secure cloud-based storage for lifelong immunization history.</p>
                            </div>
                        </div>

                        <div class="d-flex">
                            <div class="feature-icon bg-info bg-opacity-10 text-info me-3">
                                <i class="fa-solid fa-chart-pie"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold">Real-time Analytics</h5>
                                <p class="text-muted small">Comprehensive dashboards for monitoring vaccination coverage.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="bg-light rounded-4 p-4 text-center position-relative">
                        <div class="py-5">
                            <i class="fa-solid fa-laptop-medical text-primary opacity-25" style="font-size: 10rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Section: How It Works -->
    <section class="py-5 bg-light" id="process">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h2 class="fw-bold">How It Works</h2>
                <p class="text-muted">Simple steps to immunize your child</p>
            </div>
            <div class="row g-4 text-center">
                <div class="col-md-3">
                    <div class="process-step position-relative">
                        <div class="bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                            <i class="fa-solid fa-user-plus text-primary fs-3"></i>
                        </div>
                        <h5 class="fw-bold">1. Register</h5>
                        <p class="text-muted small">Create an account as a parent and add your child's details.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="process-step position-relative">
                        <div class="bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                            <i class="fa-solid fa-calendar-check text-primary fs-3"></i>
                        </div>
                        <h5 class="fw-bold">2. Schedule</h5>
                        <p class="text-muted small">Book a vaccination appointment at a nearby hospital.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="process-step position-relative">
                        <div class="bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                            <i class="fa-solid fa-syringe text-primary fs-3"></i>
                        </div>
                        <h5 class="fw-bold">3. Vaccinate</h5>
                        <p class="text-muted small">Visit the hospital and get your child vaccinated safely.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="process-step position-relative">
                        <div class="bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                            <i class="fa-solid fa-file-medical text-primary fs-3"></i>
                        </div>
                        <h5 class="fw-bold">4. Certificate</h5>
                        <p class="text-muted small">Download the digital vaccination certificate instantly.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-5 bg-white" id="faq">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="text-center mb-5">
                        <h2 class="fw-bold">Frequently Asked Questions</h2>
                        <p class="text-muted">Common queries about the vaccination process</p>
                    </div>
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item border-0 shadow-sm mb-3 rounded overflow-hidden">
                            <h2 class="accordion-header">
                                <button class="accordion-button fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Is registration mandatory for vaccination?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    Yes, registration is mandatory to maintain a digital record of your child's vaccination history and to generate certificates.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item border-0 shadow-sm mb-3 rounded overflow-hidden">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    How do I download the vaccination certificate?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    Once the vaccination is marked as completed by the hospital, you can log in to your parent dashboard and download the certificate from the 'Records' section.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item border-0 shadow-sm mb-3 rounded overflow-hidden">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Can I reschedule an appointment?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    Yes, you can reschedule or cancel an appointment up to 24 hours before the scheduled time through your dashboard.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item border-0 shadow-sm mb-3 rounded overflow-hidden">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    Is my data secure?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    Absolutely. We use industry-standard encryption to protect your personal information and health records. Only authorized healthcare providers and you have access to this data.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item border-0 shadow-sm mb-3 rounded overflow-hidden">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                    What should I do if I miss a vaccination date?
                                </button>
                            </h2>
                            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    Don't panic. Contact your healthcare provider immediately or use the 'Reschedule' feature in your dashboard to book the next available slot. Catch-up vaccinations are often available.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Detailed Footer -->
    <footer class="bg-dark text-white pt-5 pb-3">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center" style="width: 32px; height: 32px;">
                            <i class="fa-solid fa-feather-pointed fs-6"></i>
                        </div>
                        <span class="h5 fw-bold mb-0">VMS</span>
                    </div>
                    <p class="text-secondary small mb-4">
                        The Vaccination Management System (VMS) is dedicated to streamlining the immunization process, ensuring every child receives timely healthcare protection through technology.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-light"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="text-light"><i class="fa-brands fa-twitter"></i></a>
                        <a href="#" class="text-light"><i class="fa-brands fa-linkedin-in"></i></a>
                        <a href="#" class="text-light"><i class="fa-brands fa-instagram"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h6 class="fw-bold mb-3 text-white">Quick Links</h6>
                    <ul class="list-unstyled small text-secondary">
                        <li class="mb-2"><a href="#" class="text-decoration-none text-secondary">Home</a></li>
                        <li class="mb-2"><a href="#about" class="text-decoration-none text-secondary">About Us</a></li>
                        <li class="mb-2"><a href="#features" class="text-decoration-none text-secondary">Features</a></li>
                        <li class="mb-2"><a href="#process" class="text-decoration-none text-secondary">How it Works</a></li>
                        <li class="mb-2"><a href="auth/login.php" class="text-decoration-none text-secondary">Login</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h6 class="fw-bold mb-3 text-white">Contact Us</h6>
                    <ul class="list-unstyled small text-secondary">
                        <li class="mb-3 d-flex gap-2">
                            <i class="fa-solid fa-location-dot mt-1"></i>
                            <span>123 Health Avenue, Medical District, City, Country 10001</span>
                        </li>
                        <li class="mb-3 d-flex gap-2">
                            <i class="fa-solid fa-phone mt-1"></i>
                            <span>+1 (555) 123-4567</span>
                        </li>
                        <li class="mb-3 d-flex gap-2">
                            <i class="fa-solid fa-envelope mt-1"></i>
                            <span>support@vms-system.com</span>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h6 class="fw-bold mb-3 text-white">Newsletter</h6>
                    <p class="small text-secondary mb-3">Subscribe to get updates on vaccination schedules and health tips.</p>
                    <form action="#" class="mb-3">
                        <div class="input-group">
                            <input type="email" class="form-control form-control-sm bg-dark border-secondary text-white placeholder-white" placeholder="Email address">
                            <button class="btn btn-primary btn-sm" type="button"><i class="fa-solid fa-paper-plane"></i></button>
                        </div>
                    </form>
                </div>
            </div>
            <hr class="border-secondary my-4">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <p class="mb-0 text-secondary small">&copy; <?php echo date('Y'); ?> Vaccination Management System. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item"><a href="#" class="text-secondary small text-decoration-none">Privacy Policy</a></li>
                        <li class="list-inline-item"><a href="#" class="text-secondary small text-decoration-none ms-3">Terms of Service</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>