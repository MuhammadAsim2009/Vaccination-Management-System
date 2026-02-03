<?php
include '../config/db.php';

if(isset($_POST['register_btn'])) {
    $hospital_name = $_POST['hospital_name'];
    $reg_no = $_POST['reg_no'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $role = 'hospital';
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
    $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();

    if($check_email -> num_rows()>0) {
        echo "Email Already Exists";
        exit();
    }

    // Check if registration number already exists
    $check_reg_no = $conn->prepare("SELECT id FROM hospitals WHERE registration_no = ?");
    $check_reg_no->bind_param("s", $reg_no);
    $check_reg_no->execute();
    $check_reg_no->store_result();

    if($check_reg_no -> num_rows()>0) {
        echo "Registration Number Already Exists";
        exit();
    }

    // Insert user into database
    $stmt_user = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES(?,?,?,?)");
    $stmt_user->bind_param("ssss", $hospital_name, $email, $hashed_password, $role);
    $stmt_user->execute();
    $stmt_user->close();

    // Get the user ID after insertion
    $user_id = $conn->insert_id;

    // Insert parent details into parents table
    $stmt_parent = $conn->prepare("INSERT INTO hospitals (user_id, hospital_name, registration_no, phone, address) VALUES(?,?,?,?,?)");
    $stmt_parent->bind_param("issss", $user_id, $hospital_name, $reg_no, $phone, $address);
    $stmt_parent->execute();
    $stmt_parent->close();

    if($stmt_user && $stmt_parent) {
        echo "<script>alert('Registration Successful.');</script>";
    } else {
        echo "Something went Wrong";
    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Registration - Vaccination Management System</title>
    
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
                            <i class="fa-solid fa-hospital text-white fs-4"></i>
                        </div>
                        <div>
                            <h4 class="text-white m-0 fw-bold">VMS</h4>
                            <small class="text-white-50">Partner Portal</small>
                        </div>
                    </div>
                </div>
                
                <div class="login-left-content">
                    <div class="mb-5">
                        <h1 class="display-5 fw-bold mb-3 text-white">Partner with<br>Innovation.</h1>
                        <p class="lead text-white-50" style="max-width: 400px;">Streamline your vaccination services and manage appointments with efficiency.</p>
                    </div>

                    <div class="login-testimonial">
                        <div class="d-flex gap-1 text-warning mb-3">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <p class="testimonial-quote mb-4">"VMS has optimized our workflow, reducing wait times and improving patient satisfaction significantly."</p>
                        <div class="d-flex align-items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name=Dr+Robert&background=fff&color=0f172a&rounded=true" alt="User" class="rounded-3" width="48" height="48">
                            <div>
                                <h6 class="m-0 fw-bold text-white">Dr. Robert Chen</h6>
                                <small class="text-white-50">Director, City General</small>
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
                        <i class="fa-solid fa-hospital"></i>
                        <h3 class="mt-2 fw-bold text-dark">VMS</h3>
                    </div>

                    <div class="login-header mb-4">
                        <h2 class="fw-bold text-gray-900">Hospital Registration</h2>
                        <p class="text-muted">Register your facility to join our network.</p>
                    </div>

                    <!-- Info Alert -->
                    <div class="alert alert-info border-0 shadow-sm rounded-3 d-flex align-items-start gap-3 mb-4" role="alert">
                        <i class="fa-solid fa-circle-info mt-1 fs-5"></i>
                        <div>
                            <h6 class="fw-bold mb-1">Approval Required</h6>
                            <p class="mb-0 small opacity-75">Your hospital account will be active only after verification and approval by the system administrator.</p>
                        </div>
                    </div>

                    <form method="POST">
                        
                        <!-- Hospital Name -->
                        <div class="mb-3">
                            <label for="hospital_name" class="form-label fw-medium">Hospital Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                                    <i class="fa-regular fa-building"></i>
                                </span>
                                <input type="text" class="form-control form-control-lg border-start-0 ps-2" id="hospital_name" name="hospital_name" placeholder="City General Hospital" required>
                            </div>
                        </div>

                        <!-- Registration Number -->
                        <div class="mb-3">
                            <label for="reg_no" class="form-label fw-medium">Registration Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                                    <i class="fa-solid fa-id-card"></i>
                                </span>
                                <input type="text" class="form-control form-control-lg border-start-0 ps-2" id="reg_no" name="reg_no" placeholder="REG-XXXX-YYYY" required>
                            </div>
                        </div>
                        
                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-medium">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                                    <i class="fa-regular fa-envelope"></i>
                                </span>
                                <input type="email" class="form-control form-control-lg border-start-0 ps-2" id="email" name="email" placeholder="contact@hospital.com" required>
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
                            <label for="address" class="form-label fw-medium">Hospital Address</label>
                            <textarea class="form-control form-control-lg" id="address" name="address" rows="2" placeholder="Full address of the facility" required></textarea>
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

                        <!-- Terms Checkbox -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms" required>
                                <label class="form-check-label text-muted small" for="terms">
                                    I agree to the <a href="#" class="text-primary text-decoration-none">Terms of Service</a> and confirm this is a verified medical facility.
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid mb-4">
                            <button type="submit" name="register_btn" class="btn btn-primary btn-login shadow-lg">Register Hospital</button>
                        </div>

                        <!-- Login Link -->
                        <div class="text-center">
                            <p class="text-muted">Already registered? 
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
