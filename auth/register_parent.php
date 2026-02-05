<?php
include '../config/db.php';

if(isset($_POST['register_btn'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $role = 'parent';
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password != $confirm_password) {
        echo "Passwords do not match";
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    
    if($check -> num_rows()>0) {
        echo "Email Already Exists";
        exit();
    }

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Insert into users table
        $stmt_user = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?,?,?,?)");
        $stmt_user->bind_param("ssss", $name, $email, $hashed_password, $role);
        $stmt_user->execute();
        $user_id = $conn->insert_id;
        $stmt_user->close();

        // Insert into hospital table
        $stmt_parent = $conn->prepare("INSERT INTO parents (user_id, phone, address) VALUES (?,?,?)");
        $stmt_parent->bind_param("iss", $user_id, $phone, $address);
        $stmt_parent->execute();
        $stmt_parent->close();

        // Commit transaction
        $conn->commit();

        echo "<script>alert('Registration Successful'); window.location.href='login.php';</script>";

    } catch(Exception $e) {
        // Rollback if any error
        $conn->rollback();
        echo "<script>alert('Registration Failed: ".$e->getMessage()."'); window.history.back();</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Registration - Vaccination Management System</title>
    
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
            <div class="col-lg-5 col-xl-6 login-left d-none d-lg-flex position-fixed h-100" style="width: 50%; z-index: 1;">
                
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
                        <h1 class="display-5 fw-bold mb-3 text-white">Join Our<br>Community.</h1>
                        <p class="lead text-white-50" style="max-width: 400px;">Ensure your child's health with our state-of-the-art vaccination tracking system.</p>
                    </div>

                    <div class="login-testimonial">
                        <div class="d-flex gap-1 text-warning mb-3">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <p class="testimonial-quote mb-4">"Registering was simple, and now I never miss a vaccination date. It gives me peace of mind."</p>
                        <div class="d-flex align-items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name=Sarah+J&background=fff&color=0f172a&rounded=true" alt="User" class="rounded-3" width="48" height="48">
                            <div>
                                <h6 class="m-0 fw-bold text-white">Sarah Jenkins</h6>
                                <small class="text-white-50">Parent</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT SIDE: Registration Form -->
            <div class="col-lg-7 col-xl-6 ms-auto login-right overflow-auto" style="min-height: 100vh;">
                <div class="login-container" style="max-width: 550px; padding-top: 2rem; padding-bottom: 2rem;">
                    
                    <!-- Mobile Brand -->
                    <div class="login-brand-mobile">
                        <i class="fa-solid fa-feather-pointed"></i>
                        <h3 class="mt-2 fw-bold text-dark">VMS</h3>
                    </div>

                    <div class="login-header mb-4">
                        <h2 class="fw-bold text-gray-900">Create Parent Account</h2>
                        <p class="text-muted">Start tracking your child's vaccinations today.</p>
                    </div>

                    <form method="POST">
                        
                        <!-- Full Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label fw-medium">Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                                    <i class="fa-regular fa-user"></i>
                                </span>
                                <input type="text" class="form-control form-control-lg border-start-0 ps-2" id="name" name="name" placeholder="John Doe" required>
                            </div>
                        </div>
                        
                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-medium">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                                    <i class="fa-regular fa-envelope"></i>
                                </span>
                                <input type="email" class="form-control form-control-lg border-start-0 ps-2" id="email" name="email" placeholder="name@example.com" required>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="mb-3">
                            <label for="phone" class="form-label fw-medium">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                                    <i class="fa-solid fa-phone"></i>
                                </span>
                                <input type="tel" class="form-control form-control-lg border-start-0 ps-2" id="phone" name="phone" placeholder="+1 (555) 000-0000" required>
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="mb-3">
                            <label for="address" class="form-label fw-medium">Address</label>
                            <textarea class="form-control form-control-lg" id="address" name="address" rows="2" placeholder="Enter your full address" required></textarea>
                        </div>

                        <div class="row">
                            <!-- Password -->
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label fw-medium">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control form-control-lg border-end-0" id="password" name="password" placeholder="••••••••" required>
                                    <button class="btn btn-outline-secondary border-start-0 bg-gray-50 text-muted" type="button" id="togglePassword">
                                        <i class="fa-regular fa-eye-slash" id="toggleIcon"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-md-6 mb-4">
                                <label for="confirm_password" class="form-label fw-medium">Confirm Password</label>
                                <input type="password" class="form-control form-control-lg" id="confirm_password" name="confirm_password" placeholder="••••••••" required>
                            </div>
                        </div>

                        <!-- Terms Checkbox (Optional but good for UX) -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms" required>
                                <label class="form-check-label text-muted small" for="terms">
                                    I agree to the <a href="#" class="text-primary text-decoration-none">Terms of Service</a> and <a href="#" class="text-primary text-decoration-none">Privacy Policy</a>.
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid mb-4">
                            <button type="submit" name="register_btn" class="btn btn-primary btn-login shadow-lg">Create Account</button>
                        </div>

                        <!-- Login Link -->
                        <div class="text-center">
                            <p class="text-muted">Already have an account? 
                                <a href="login.php" class="text-primary fw-semibold text-decoration-none">Login here</a>
                            </p>
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
