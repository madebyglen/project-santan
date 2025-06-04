<?php
// Start session
session_start();

// Connect to database
require_once 'db_connect.php';

$name = $email = $password = "";
$name_err = $email_err = $password_err = $register_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter your name.";
    } else {
        $name = trim($_POST["name"]);
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $email_err = "Please enter a valid email address.";
    } else {
        $sql = "SELECT id FROM users WHERE email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_email);
            $param_email = trim($_POST["email"]);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $email_err = "This email is already registered.";
            } else {
                $email = trim($_POST["email"]);
            }
            $stmt->close();
        } else {
            $register_err = "Something went wrong. Please try again later.";
        }
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must be at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // If no errors, insert user
    if (empty($name_err) && empty($email_err) && empty($password_err)) {
        $sql = "INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sss", $param_name, $param_email, $param_password);
            $param_name = $name;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            if ($stmt->execute()) {
                header("Location: login.php?register=success");
                exit();
            } else {
                $register_err = "Failed to register. Please try again.";
            }
            $stmt->close();
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>User Registration - Project Santan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500&family=Roboto&display=swap" rel="stylesheet" />
  <style>
    html, body {
      height: 100%;
      margin: 0;
    }

    body {
      display: flex;
      flex-direction: column;
      background: #f7f9fc;
      font-family: 'Roboto', sans-serif;
    }

    .navbar {
      background: linear-gradient(90deg, #1f0036, #42075f);
    }

    .navbar-brand {
      font-family: 'Orbitron', sans-serif;
      font-size: 1.8rem;
    }

    .main-content {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .register-container {
      max-width: 450px;
      width: 100%;
      margin: 40px 0;
      padding: 30px;
      background: white;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .form-control:focus {
      box-shadow: 0 0 8px #7f5af0;
      border-color: #7f5af0;
    }

    .btn-register {
      background: #7f5af0;
      color: white;
      font-weight: 600;
    }

    .btn-register:hover {
      background: #6f42c1;
    }

    footer {
      background-color: #1f0036;
      color: white;
      text-align: center;
      padding: 15px 0;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold text-primary" href="../index.php">🎧 Santan Events</a>
  </div>
</nav>

<!-- Main Registration Form -->
<div class="main-content">
  <div class="register-container shadow-sm">
    <h2 class="mb-4 text-center text-purple">Create an Account</h2>

    <?php 
    if(!empty($register_err)){
        echo '<div class="alert alert-danger">' . $register_err . '</div>';
    }        
    ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" novalidate>
      <div class="mb-3">
        <label for="name" class="form-label">Full Name</label>
        <input type="text" name="name" id="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($name); ?>" required />
        <div class="invalid-feedback"><?php echo $name_err; ?></div>
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <input type="email" name="email" id="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($email); ?>" required />
        <div class="invalid-feedback"><?php echo $email_err; ?></div>
      </div>

      <div class="mb-4">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" id="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" required />
        <div class="invalid-feedback"><?php echo $password_err; ?></div>
      </div>

      <button type="submit" class="btn btn-register w-100">Register</button>

      <p class="mt-3 text-center">Already have an account? <a href="login.php">Login here</a>.</p>
    </form>
  </div>
</div>

<!-- Footer -->
<footer>
  <div class="container">
    <small>&copy; <?= date("Y") ?> Santan Events. All rights reserved.</small>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
