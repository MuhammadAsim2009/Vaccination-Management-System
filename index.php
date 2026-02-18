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
<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Vaccination Management System | VMS</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#0d0dfd",
                        "success-green": "#22c55e",
                        "background-light": "#f5f5f8",
                        "background-dark": "#0f0f23",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.5rem",
                        "lg": "1rem",
                        "xl": "1.5rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <style>
        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
        }

        .soft-shadow {
            box-shadow: 0 10px 30px -10px rgba(13, 13, 253, 0.1);
        }

        .hero-gradient {
            background: radial-gradient(circle at 10% 20%, rgba(13, 13, 253, 0.03) 0%, rgba(255, 255, 255, 1) 90%);
        }

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
    </style>
</head>

<body class="bg-background-light text-[#101018] font-display antialiased">
    <!-- Top Navigation -->
    <header class="sticky top-0 z-50 w-full bg-white/80 backdrop-blur-md border-b border-[#dadae7]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center gap-2">
                    <div class="bg-primary p-1.5 rounded-lg text-white">
                        <span class="material-symbols-outlined text-2xl">verified_user</span>
                    </div>
                    <span class="text-xl font-extrabold tracking-tight text-[#101018]">VMS</span>
                </div>
                <nav class="hidden md:flex items-center gap-8">
                    <a class="text-sm font-semibold text-[#5e5e8d] hover:text-primary transition-colors" href="#features">Features</a>
                    <a class="text-sm font-semibold text-[#5e5e8d] hover:text-primary transition-colors" href="#process">Process</a>
                    <a class="text-sm font-semibold text-[#5e5e8d] hover:text-primary transition-colors" href="#roles">Roles</a>
                    <a class="text-sm font-semibold text-[#5e5e8d] hover:text-primary transition-colors" href="#about">About</a>
                </nav>
                <div class="flex items-center gap-4">
                    <a href="auth/login.php" class="hidden sm:block text-sm font-bold text-primary px-4 py-2">Log In</a>
                    <a href="auth/register_parent.php" class="bg-primary text-white text-sm font-bold px-6 py-2.5 rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-primary/20">
                        Get Started
                    </a>
                </div>
            </div>
        </div>
    </header>
    <main>
        <!-- HERO SECTION -->
        <section class="hero-gradient overflow-hidden pt-12 pb-20 lg:pt-24 lg:pb-32 reveal">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div class="space-y-8">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold uppercase tracking-wider">
                            <span class="material-symbols-outlined text-sm">health_and_safety</span>
                            Trusted Healthcare Platform
                        </div>
                        <h1 class="text-5xl lg:text-7xl font-black text-[#101018] leading-[1.1] tracking-tight">
                            Vaccination <span class="text-primary">Management</span> System
                        </h1>
                        <p class="text-xl text-[#5e5e8d] leading-relaxed max-w-lg">
                            Track, Schedule, and Manage Child Vaccinations Easily. A secure, centralized platform for parents and hospitals to ensure timely immunization.
                        </p>
                        <div class="flex flex-wrap gap-4 pt-4">
                            <div class="flex items-center gap-2 text-sm font-medium text-[#101018]">
                                <span class="material-symbols-outlined text-success-green">check_circle</span>
                                Fully HIPAA Compliant
                            </div>
                            <div class="flex items-center gap-2 text-sm font-medium text-[#101018]">
                                <span class="material-symbols-outlined text-success-green">check_circle</span>
                                24/7 Support Available
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="absolute -inset-4 bg-primary/5 rounded-full blur-3xl"></div>
                        <img alt="Medical professional with a digital tablet" class="relative rounded-3xl shadow-2xl object-cover aspect-[4/3] w-full" data-alt="Medical professional using digital tablet in clinic" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCbMN3V0yjzeg2FB8GqfnEFJq7JxhjrRX3tm7dPHBI3iAyvnlLoDglgnFWMlP9GhHyoLJ_NHGslWPpErIk1hVsIDTY0cdT-nQQfsVjkMLdM8j9sW1-Zad7eawQ3MG7BYbnSlu3V6S3Jw4u8AHRNnx-AIQL2YVHeAD0iM9Viwx8aCcyjS4t99N2bt8sTv3sI4tbw0zSiauu4OhFwXTEy_j3_7BZiUYCdjkanQctmMo6CqeL2rsn8zySKwmOHjZ2CiULjGDz5J-4AaRk" />
                    </div>
                </div>
            </div>
        </section>
        <!-- STATISTICS -->
        <section class="py-12 bg-white border-y border-[#dadae7] reveal">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="text-center">
                        <p class="text-4xl font-black text-[#101018] mb-1"><?= $childrenCount ?? '0' ?>+</p>
                        <p class="text-sm font-medium text-[#5e5e8d]">Children Tracked</p>
                    </div>
                    <div class="text-center">
                        <p class="text-4xl font-black text-[#101018] mb-1"><?= $vaccineCount ?? '0' ?>+</p>
                        <p class="text-sm font-medium text-[#5e5e8d]">Vaccines Given</p>
                    </div>
                    <div class="text-center">
                        <p class="text-4xl font-black text-[#101018] mb-1"><?= $hospitalCount ?? '0' ?>+</p>
                        <p class="text-sm font-medium text-[#5e5e8d]">Hospitals Linked</p>
                    </div>
                    <div class="text-center">
                        <p class="text-4xl font-black text-[#101018] mb-1"><?= $appointmentCount ?? '0' ?>+</p>
                        <p class="text-sm font-medium text-[#5e5e8d]">Appointments</p>
                    </div>
                </div>
            </div>
        </section>
        <!-- FEATURES -->
        <section class="py-24 bg-background-light reveal" id="features">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 space-y-4">
                    <h2 class="text-3xl font-black text-[#101018] sm:text-4xl">Comprehensive Healthcare Tools</h2>
                    <p class="text-lg text-[#5e5e8d] max-w-2xl mx-auto">Everything you need to manage the immunization lifecycle with precision and ease.</p>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature Cards -->
                    <div class="bg-white p-8 rounded-2xl border border-[#dadae7] soft-shadow hover:border-primary/30 transition-all hover:-translate-y-1 hover:shadow-xl">
                        <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center text-primary mb-6">
                            <span class="material-symbols-outlined">child_care</span>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Child Tracking</h3>
                        <p class="text-[#5e5e8d] leading-relaxed">Complete digital health records for every child, accessible anywhere, anytime.</p>
                    </div>
                    <div class="bg-white p-8 rounded-2xl border border-[#dadae7] soft-shadow hover:border-primary/30 transition-all hover:-translate-y-1 hover:shadow-xl">
                        <div class="w-12 h-12 bg-success-green/10 rounded-xl flex items-center justify-center text-success-green mb-6">
                            <span class="material-symbols-outlined">calendar_today</span>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Scheduling</h3>
                        <p class="text-[#5e5e8d] leading-relaxed">Automated vaccine slot allocation and seamless management for clinics.</p>
                    </div>
                    <div class="bg-white p-8 rounded-2xl border border-[#dadae7] soft-shadow hover:border-primary/30 transition-all hover:-translate-y-1 hover:shadow-xl">
                        <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center text-primary mb-6">
                            <span class="material-symbols-outlined">local_hospital</span>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Hospital Dashboard</h3>
                        <p class="text-[#5e5e8d] leading-relaxed">A centralized hub for medical staff to manage patients and inventory.</p>
                    </div>
                    <div class="bg-white p-8 rounded-2xl border border-[#dadae7] soft-shadow hover:border-primary/30 transition-all hover:-translate-y-1 hover:shadow-xl">
                        <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center text-primary mb-6">
                            <span class="material-symbols-outlined">family_restroom</span>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Parent Dashboard</h3>
                        <p class="text-[#5e5e8d] leading-relaxed">Intuitive interface for parents to view records, history, and certificates.</p>
                    </div>
                    <div class="bg-white p-8 rounded-2xl border border-[#dadae7] soft-shadow hover:border-primary/30 transition-all hover:-translate-y-1 hover:shadow-xl">
                        <div class="w-12 h-12 bg-success-green/10 rounded-xl flex items-center justify-center text-success-green mb-6">
                            <span class="material-symbols-outlined">analytics</span>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Detailed Reports</h3>
                        <p class="text-[#5e5e8d] leading-relaxed">In-depth analytics for hospitals and compliance reporting for regulators.</p>
                    </div>
                    <div class="bg-white p-8 rounded-2xl border border-[#dadae7] soft-shadow hover:border-primary/30 transition-all hover:-translate-y-1 hover:shadow-xl">
                        <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center text-primary mb-6">
                            <span class="material-symbols-outlined">notifications_active</span>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Instant Alerts</h3>
                        <p class="text-[#5e5e8d] leading-relaxed">Automated SMS and email reminders for upcoming or overdue shots.</p>
                    </div>
                </div>
            </div>
        </section>
        <!-- HOW IT WORKS -->
        <section class="py-24 bg-white reveal" id="process">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-black text-[#101018] sm:text-4xl">How It Works</h2>
                    <p class="text-[#5e5e8d] mt-4">Three simple steps to a healthier tomorrow</p>
                </div>
                <div class="relative grid md:grid-cols-3 gap-12 text-center">
                    <div class="absolute top-1/2 left-0 w-full h-0.5 bg-[#f0f0f5] -translate-y-1/2 hidden md:block"></div>
                    <!-- Step 1 -->
                    <div class="relative z-10">
                        <div class="bg-white p-8 rounded-2xl border border-[#dadae7] soft-shadow h-full space-y-6 relative z-10">
                            <div class="w-16 h-16 bg-primary text-white rounded-full flex items-center justify-center text-2xl font-black mx-auto shadow-xl shadow-primary/30">1</div>
                            <h4 class="text-xl font-bold">Parent Registers</h4>
                            <p class="text-[#5e5e8d]">Create an account and set up profiles for your children in minutes.</p>
                        </div>
                    </div>
                    <!-- Step 2 -->
                    <div class="relative z-10">
                        <div class="bg-white p-8 rounded-2xl border border-[#dadae7] soft-shadow h-full space-y-6 relative z-10">
                            <div class="w-16 h-16 bg-primary text-white rounded-full flex items-center justify-center text-2xl font-black mx-auto shadow-xl shadow-primary/30">2</div>
                            <h4 class="text-xl font-bold">Hospital Schedules</h4>
                            <p class="text-[#5e5e8d]">Hospitals manage vaccine availability and assign specific appointment slots.</p>
                        </div>
                    </div>
                    <!-- Step 3 -->
                    <div class="relative z-10">
                        <div class="bg-white p-8 rounded-2xl border border-[#dadae7] soft-shadow h-full space-y-6 relative z-10">
                            <div class="w-16 h-16 bg-primary text-white rounded-full flex items-center justify-center text-2xl font-black mx-auto shadow-xl shadow-primary/30">3</div>
                            <h4 class="text-xl font-bold">System Tracks</h4>
                            <p class="text-[#5e5e8d]">VMS tracks progress, sends reminders, and generates digital certificates.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- USER ROLES -->
        <section class="py-24 bg-background-light reveal" id="roles">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-black text-[#101018] sm:text-4xl">Tailored Panels for Every Role</h2>
                </div>
                <div class="grid lg:grid-cols-3 gap-8"><!-- Parent Panel -->
                    <div class="bg-white rounded-2xl p-8 pb-10 shadow-lg border-b-4 border-primary flex flex-col items-start gap-6">
                        <div class="text-primary">
                            <span class="material-symbols-outlined text-5xl font-bold">family_restroom</span>
                        </div>
                        <h3 class="text-2xl font-bold text-[#101018]">Parent Panel</h3>
                        <ul class="space-y-4">
                            <li class="flex items-center gap-3 text-[#5e5e8d] font-medium">
                                <span class="material-symbols-outlined text-success-green text-xl">check_circle</span>
                                Digital vaccination cards
                            </li>
                            <li class="flex items-center gap-3 text-[#5e5e8d] font-medium">
                                <span class="material-symbols-outlined text-success-green text-xl">check_circle</span>
                                Appointment booking
                            </li>
                            <li class="flex items-center gap-3 text-[#5e5e8d] font-medium">
                                <span class="material-symbols-outlined text-success-green text-xl">check_circle</span>
                                Growth tracking tools
                            </li>
                        </ul>
                    </div>
                    <!-- Hospital Panel -->
                    <div class="bg-white rounded-2xl p-8 pb-10 shadow-lg border-b-4 border-primary flex flex-col items-start gap-6">
                        <div class="text-primary">
                            <span class="material-symbols-outlined text-5xl font-bold">medical_services</span>
                        </div>
                        <h3 class="text-2xl font-bold text-[#101018]">Hospital Panel</h3>
                        <ul class="space-y-4">
                            <li class="flex items-center gap-3 text-[#5e5e8d] font-medium">
                                <span class="material-symbols-outlined text-success-green text-xl">check_circle</span>
                                Patient queue management
                            </li>
                            <li class="flex items-center gap-3 text-[#5e5e8d] font-medium">
                                <span class="material-symbols-outlined text-success-green text-xl">check_circle</span>
                                Stock/Inventory control
                            </li>
                            <li class="flex items-center gap-3 text-[#5e5e8d] font-medium">
                                <span class="material-symbols-outlined text-success-green text-xl">check_circle</span>
                                Digital verification
                            </li>
                        </ul>
                    </div>
                    <!-- Admin Panel -->
                    <div class="bg-white rounded-2xl p-8 pb-10 shadow-lg border-b-4 border-primary flex flex-col items-start gap-6">
                        <div class="text-primary">
                            <span class="material-symbols-outlined text-5xl font-bold">policy</span>
                        </div>
                        <h3 class="text-2xl font-bold text-[#101018]">Admin Panel</h3>
                        <ul class="space-y-4">
                            <li class="flex items-center gap-3 text-[#5e5e8d] font-medium">
                                <span class="material-symbols-outlined text-success-green text-xl">check_circle</span>
                                Region-wide statistics
                            </li>
                            <li class="flex items-center gap-3 text-[#5e5e8d] font-medium">
                                <span class="material-symbols-outlined text-success-green text-xl">check_circle</span>
                                Hospital onboarding
                            </li>
                            <li class="flex items-center gap-3 text-[#5e5e8d] font-medium">
                                <span class="material-symbols-outlined text-success-green text-xl">check_circle</span>
                                System configuration
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <!-- ABOUT -->
        <section class="py-24 bg-white overflow-hidden reveal" id="about">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row gap-16 items-center">
                    <div class="lg:w-1/2 space-y-6">
                        <h2 class="text-3xl font-black text-[#101018] sm:text-4xl">Our Mission for a Healthier Future</h2>
                        <p class="text-lg text-[#5e5e8d] leading-relaxed">
                            At VMS, we believe that every child deserves the best start in life. Our platform was built to bridge the gap between healthcare providers and families, ensuring that no essential vaccination is ever missed.
                        </p>
                        <p class="text-lg text-[#5e5e8d] leading-relaxed">
                            We combine cutting-edge technology with rigorous security standards to create a reliable ecosystem that empowers parents and equips hospitals with the tools they need to succeed in their mission of child health protection.
                        </p>
                        <div class="pt-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-primary">groups</span>
                                </div>
                                <div>
                                    <p class="font-bold">Community Driven</p>
                                    <p class="text-sm text-[#5e5e8d]">Supporting global health equity</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="lg:w-1/2 relative">
                        <img alt="Smiling children" class="rounded-3xl shadow-2xl rotate-3 hover:rotate-0 transition-transform duration-500" data-alt="Group of diverse smiling healthy children" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCz0SZnHsi9Qb7syDPRISV15kpKFP9o2bu4sxOqA721N-bbKhLpiIufjJmkgLCgRUn1EiXk0sJYAFcD9El5qjaXmahywLqBX_z2QhITKRajwByyeYcO4PknaVHawvAPfsy1xwuiGZ48XjCuFNm5USdzRjXz5Zj15ymYey9G6DYC4A5JBP1_OzXyJ3v7JOodIRvvHEqElv1k7sSKOrS_OJ42HrdM17k3keBZ9xE28nWJeFPUbdqz03tfInjHOE85FW7No_ghOIUJdx0" />
                        <div class="absolute -bottom-6 -left-6 bg-white p-6 rounded-2xl shadow-xl border border-[#dadae7]">
                            <p class="text-3xl font-black text-primary">100%</p>
                            <p class="text-sm font-bold text-[#101018]">Security Record</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- TESTIMONIALS -->
        <section class="py-24 bg-background-light reveal">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-black text-[#101018]">Trusted by the Community</h2>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Testimonial 1 -->
                    <div class="bg-white p-8 rounded-2xl border border-[#dadae7] flex flex-col justify-between">
                        <p class="text-lg text-[#5e5e8d] italic mb-8">"VMS has completely changed how I manage my daughter's health. I no longer worry about missing dates thanks to the SMS alerts."</p>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-slate-200 bg-cover" data-alt="Portrait of a mother" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuB22yfC3NE8UEgMzG4Ziaxh-0J9sdhhwtC6f7gZ5M5zd8UbO35-3Yh4LZjSgeok7BuRe6JNeUJbzRC-MQik4XZt5UcrQjmU4TPwOiGvUlBc7YgDJ2a-1FkUxModMQ0wwv1FHm75JR2ie_pIecTtQBDPusEEj-fRUSA8WK4nj5Mzkds1zd_RlcsYXk5-z_QIX151CyZ3OH7hNdbH4IWZ3O5HKAzaG4LJuYiHswd8qpCCKQ9KQ_HbdEjBACivX7YqEmGE-wRT--lotJ4")'></div>
                            <div>
                                <p class="font-bold text-[#101018]">Sarah Johnson</p>
                                <p class="text-xs font-semibold text-primary uppercase tracking-wider">Parent</p>
                            </div>
                        </div>
                    </div>
                    <!-- Testimonial 2 -->
                    <div class="bg-white p-8 rounded-2xl border border-[#dadae7] flex flex-col justify-between">
                        <p class="text-lg text-[#5e5e8d] italic mb-8">"The hospital dashboard is incredibly intuitive. It reduced our administrative paperwork by nearly 60% in the first quarter."</p>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-slate-200 bg-cover" data-alt="Portrait of a male doctor" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuACLFXk1HTN413KYrFmRJJQJGJ-aA8IamIxE1vSe7goSvDGEh84YMJCK6xIkDxEwqmRaODayalVLmmefq1kL_Y40GMO60Y2HoNsHPEk9wss2W1U8gHfPLuz9FKBiXZR2iBaR69zcYnOkMHOPrb-ER78_dHzApGzlrXoD2KquEi2wVo9f8KZY-1E3wIEVLlONMqYwadQg5A5jZhOvo1cIxWzfDXpAturTjsZwESg1oYPJdeOTzZDrmYts9aL1FBnA-Qw2W1j-MPcLFo")'></div>
                            <div>
                                <p class="font-bold text-[#101018]">Dr. Michael Chen</p>
                                <p class="text-xs font-semibold text-primary uppercase tracking-wider">Hospital Director</p>
                            </div>
                        </div>
                    </div>
                    <!-- Testimonial 3 -->
                    <div class="bg-white p-8 rounded-2xl border border-[#dadae7] flex flex-col justify-between">
                        <p class="text-lg text-[#5e5e8d] italic mb-8">"As a health worker, having digital records makes verifications instant. It's safe, secure, and very efficient."</p>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-slate-200 bg-cover" data-alt="Portrait of a female nurse" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDBmxyALjOSIGMf9OLYhkCL060y9kFQK8Uw2gKyCxx4HNPbPHTSn5YXHdyOw8pubutc78CW3Y9lFgHWidUfrGb2IEOeW8W40ml2E26A6dEz_26qXg_b1h7Wp0hh3rR9z6VV3L_WcPGQvKCi0IGF3AnK3b8QzHI2DgDKPSMx6TrwMk1IMtUFHVlgZ6fDxNi4PFbOeCLbAJT_-bWuqH0T1PojMka1Ts6AJdWJontRNGibq7rWOvJNgdLXb15WS_P8Ed3gZZvPpbhloys")'></div>
                            <div>
                                <p class="font-bold text-[#101018]">Elena Rodriguez</p>
                                <p class="text-xs font-semibold text-primary uppercase tracking-wider">Medical Staff</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- CALL TO ACTION -->
        <section class="py-20 px-4 reveal">
            <div class="max-w-5xl mx-auto bg-primary rounded-3xl p-8 lg:p-16 text-center text-white relative overflow-hidden shadow-2xl shadow-primary/40">
                <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/2 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                <div class="relative z-10 space-y-8">
                    <h2 class="text-3xl lg:text-5xl font-black">Ready to secure your child's health?</h2>
                    <p class="text-xl text-white/80 max-w-2xl mx-auto">Join thousands of families and hospitals using VMS to build a safer, immunized future.</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center pt-4">
                        <a href="auth/register_parent.php" class="bg-white text-primary font-black px-10 py-4 rounded-xl hover:bg-opacity-90 transition-all text-lg">
                            Get Started Now
                        </a>
                        <a href="#" class="bg-primary border-2 border-white/30 text-white font-black px-10 py-4 rounded-xl hover:bg-white/10 transition-all text-lg flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined">support_agent</span>
                            Contact Support
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <!-- FOOTER -->
    <footer class="bg-white border-t border-[#dadae7] pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-12 mb-16">
                <div class="col-span-2 lg:col-span-2 space-y-6">
                    <div class="flex items-center gap-2">
                        <div class="bg-primary p-1.5 rounded-lg text-white">
                            <span class="material-symbols-outlined text-2xl">verified_user</span>
                        </div>
                        <span class="text-xl font-extrabold tracking-tight text-[#101018]">VMS</span>
                    </div>
                    <p class="text-[#5e5e8d] max-w-sm">
                        Securing health records and managing vaccinations for the next generation. Professional, secure, and intuitive.
                    </p>
                    <div class="flex gap-4">
                        <a class="w-10 h-10 rounded-full bg-background-light flex items-center justify-center text-[#5e5e8d] hover:bg-primary hover:text-white transition-all" href="#">
                            <span class="material-symbols-outlined text-xl">share</span>
                        </a>
                        <a class="w-10 h-10 rounded-full bg-background-light flex items-center justify-center text-[#5e5e8d] hover:bg-primary hover:text-white transition-all" href="#">
                            <span class="material-symbols-outlined text-xl">language</span>
                        </a>
                    </div>
                </div>
                <div>
                    <h5 class="font-bold mb-6">Product</h5>
                    <ul class="space-y-4 text-sm text-[#5e5e8d]">
                        <li><a class="hover:text-primary transition-colors" href="#">Features</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">Pricing</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">Security</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">API Docs</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-bold mb-6">Company</h5>
                    <ul class="space-y-4 text-sm text-[#5e5e8d]">
                        <li><a class="hover:text-primary transition-colors" href="#">About Us</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">Partners</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">Careers</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-bold mb-6">Support</h5>
                    <ul class="space-y-4 text-sm text-[#5e5e8d]">
                        <li><a class="hover:text-primary transition-colors" href="#">Help Center</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">Status</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">Privacy</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">Terms</a></li>
                    </ul>
                </div>
            </div>
            <div class="pt-8 border-t border-[#f0f0f5] text-center">
                <p class="text-sm text-[#5e5e8d]">© <?php echo date('Y'); ?> VMS Systems Inc. All rights reserved.</p>
            </div>
        </div>
    </footer>
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