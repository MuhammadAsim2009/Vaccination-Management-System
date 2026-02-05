<?php
include '../config/db.php';

if(isset($_POST['login_btn'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Fetch user from database
    $check = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
    $check->bind_param("ss", $email, $role);
    $check->execute();
    $result = $check->get_result();

    if($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // Verify password
        if(password_verify($password, $user['password'])) {
            // Start session and set user ID
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $role;

            // Redirect based on role
            if($role === 'admin' && $_SESSION['role'] === 'admin' && $user['status'] === 'active') {
                header('Location: ../admin/dashboard.php');
            } else if($role === 'parent' && $_SESSION['role'] === 'parent' && $user['status'] === 'active') {
                header('Location: ../parent/dashboard.php');
            } else if($role === 'hospital' && $_SESSION['role'] === 'hospital' && $user['status'] === 'active') {
                header('Location: ../hospital/dashboard.php');
            } else {
                echo "<script>alert('Invalid role');</script>";
            }
            exit();
        } else {
            echo "<script>alert('Invalid Password');</script>";
        }
    } else {
        echo "<script>alert('No account found with that email');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Vaccination Management System</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body class="bg-white">

    <div class="container-fluid p-0 overflow-hidden">
        <div class="row g-0 login-split-screen">
            
            <!-- LEFT SIDE: Branding & Visuals -->
            <div class="col-lg-6 col-xl-7 login-left d-none d-lg-flex">
                
                <!-- Abstract Background Shapes -->
                <div class="login-shape shape-1"></div>
                <div class="login-shape shape-2"></div>
                <div class="login-shape shape-3"></div>

                <div class="login-left-content">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-white bg-opacity-10 rounded-circle p-2 d-flex justify-content-center align-items-center border border-white border-opacity-25" style="width: 56px; height: 56px; backdrop-filter: blur(10px);">
                            <i class="fa-solid fa-feather-pointed text-white fs-4"></i>
                        </div>
                        <div>
                            <h4 class="text-white m-0 fw-bold">VMS</h4>
                            <small class="text-white-50">Healthcare Solutions</small>
                        </div>
                    </div>
                </div>
                
                <div class="login-left-content">
                    <div class="mb-5">
                        <h1 class="display-5 fw-bold mb-3 text-white">Protecting Future<br>Generations.</h1>
                        <p class="lead text-white-50" style="max-width: 400px;">Experience the next evolution in vaccination management. Secure, efficient, and reliable.</p>
                    </div>

                    <div class="login-testimonial">
                        <div class="d-flex gap-1 text-warning mb-3">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <p class="testimonial-quote mb-4">"The new VMS platform isn't just a tool; it's a complete ecosystem that ensures no child is left behind. The workflow is intuitive and seamless."</p>
                        <div class="d-flex align-items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name=James+Wilson&background=fff&color=0f172a&rounded=true" alt="User" class="rounded-3" width="48" height="48">
                            <div>
                                <h6 class="m-0 fw-bold text-white">Dr. James Wilson</h6>
                                <small class="text-white-50">Chief Medical Officer</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT SIDE: Login Form -->
            <div class="col-lg-6 col-xl-5 login-right">
                <div class="login-container">
                    
                    <!-- Mobile Brand -->
                    <div class="login-brand-mobile">
                        <i class="fa-solid fa-feather-pointed"></i>
                        <h3 class="mt-2 fw-bold text-dark">VMS</h3>
                    </div>

                    <div class="login-header mb-4">
                        <h2 class="fw-bold text-gray-900">Welcome back</h2>
                        <p class="text-muted">Enter your credentials to access your account.</p>
                    </div>

                    <form action="" method="POST">
                        <!-- Role Selector -->
                        <div class="mb-4">
                            <label class="form-label small text-uppercase text-muted fw-bold mb-2">Select Account Type</label>
                            <div class="row g-2">
                                <div class="col-4">
                                    <input type="radio" class="btn-check" name="role" id="role-admin" value="admin">
                                    <label class="btn btn-outline-primary w-100 py-2 fw-medium" for="role-admin">
                                        <i class="fa-solid fa-shield-halved d-block mb-1 fs-5"></i> Admin
                                    </label>
                                </div>
                                <div class="col-4">
                                    <input type="radio" class="btn-check" name="role" id="role-parent" value="parent" checked>
                                    <label class="btn btn-outline-primary w-100 py-2 fw-medium" for="role-parent">
                                        <i class="fa-solid fa-user-group d-block mb-1 fs-5"></i> Parent
                                    </label>
                                </div>
                                <div class="col-4">
                                    <input type="radio" class="btn-check" name="role" id="role-hospital" value="hospital">
                                    <label class="btn btn-outline-primary w-100 py-2 fw-medium" for="role-hospital">
                                        <i class="fa-solid fa-hospital d-block mb-1 fs-5"></i> Hospital
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Email Input -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-medium">Email address</label>
                            <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder="name@example.com" required>
                        </div>

                        <!-- Password Input -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="password" class="form-label fw-medium mb-0">Password</label>
                                <a href="#" class="small text-decoration-none text-primary fw-medium">Forgot password?</a>
                            </div>
                            <div class="input-group">
                                <input type="password" class="form-control form-control-lg border-end-0" id="password" name="password" placeholder="••••••••" required>
                                <button class="btn btn-outline-secondary border-start-0 bg-gray-50 text-muted" type="button" id="togglePassword">
                                    <i class="fa-regular fa-eye-slash" id="toggleIcon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Remember Me -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="rememberMe">
                                <label class="form-check-label text-muted" for="rememberMe">
                                    Keep me logged in for 30 days
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid mb-4">
                            <button type="submit" name="login_btn" class="btn btn-primary btn-login shadow-lg">Sign In</button>
                        </div>

                        <!-- Social Login -->
                        <div class="d-grid mb-4">
                            <button type="button" class="btn social-login-btn">
                                <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" width="20">
                                Continue with Google
                            </button>
                        </div>

                        <!-- Registration Options -->
                        <div class="mt-4 pt-4 text-center border-top">
                            <p class="text-muted small mb-3">Don't have an account? Join VMS today.</p>
                            <div class="row g-2">
                                <div class="col-6">
                                    <a href="register_parent.php" class="btn btn-outline-primary w-100 py-2 fw-medium">
                                        <i class="fa-solid fa-user-group d-block mb-1"></i> Parent
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="register_hospital.php" class="btn btn-outline-primary w-100 py-2 fw-medium">
                                        <i class="fa-solid fa-hospital d-block mb-1"></i> Hospital
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Password Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');
            const toggleIcon = document.querySelector('#toggleIcon');

            togglePassword.addEventListener('click', function () {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                
                if (type === 'text') {
                    toggleIcon.classList.remove('fa-eye-slash');
                    toggleIcon.classList.add('fa-eye');
                } else {
                    toggleIcon.classList.remove('fa-eye');
                    toggleIcon.classList.add('fa-eye-slash');
                }
            });
        });
    </script>
</body>
</html>
