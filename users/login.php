<?php
session_start();
// Connect to database
require_once 'db_connect.php';

$email = $username = $password = $role = "";
$email_err = $username_err = $password_err = $login_err = $role_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate role
    if (empty($_POST["role"])) {
        $role_err = "Please select a role.";
    } else {
        $role = $_POST["role"];
    }

    // Validate identifier
    if ($role == "admin") {
        if (empty(trim($_POST["username"]))) {
            $username_err = "Please enter your username.";
        } else {
            $username = trim($_POST["username"]);
        }
    } else {
        if (empty(trim($_POST["email"]))) {
            $email_err = "Please enter your email.";
        } else {
            $email = trim($_POST["email"]);
        }
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Authenticate
    if (empty($email_err) && empty($username_err) && empty($password_err) && empty($role_err)) {
        if ($role == "user") {
            $sql = "SELECT id, name, password FROM users WHERE email = ?";
            $param = $email;
        } else {
            $sql = "SELECT id, username, password FROM admin_users WHERE username = ?";
            $param = $username;
        }

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                $stmt->bind_result($user_id, $user_name, $hashed_password);
                $stmt->fetch();
                if (password_verify($password, $hashed_password)) {
                    $_SESSION["user_id"] = $user_id;
                    $_SESSION["user_name"] = $user_name;

                    if ($role == "admin") {
                        $_SESSION["is_admin"] = true;
                        header("Location: admin_dashboard.php");
                    } else {
                        header("Location: dashboard.php");
                    }
                    exit;
                } else {
                    $login_err = "Invalid credentials.";
                }
            } else {
                $login_err = "Invalid credentials.";
            }
            $stmt->close();
        } else {
            $login_err = "Something went wrong. Please try again later.";
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login - Project Santan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500&family=Roboto&display=swap" rel="stylesheet" />
  <style>
    body {
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
    .login-container {
      max-width: 450px;
      margin: 80px auto;
      padding: 30px;
      background: white;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .form-control:focus {
      box-shadow: 0 0 8px #7f5af0;
      border-color: #7f5af0;
    }
    .btn-login {
      background: #7f5af0;
      color: white;
      font-weight: 600;
    }
    .btn-login:hover {
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

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold text-primary" href="../index.php">🎧 Santan Events</a>
  </div>
</nav>

<div class="login-container shadow-sm">
  <h2 class="mb-4 text-center">Login</h2>

  <?php 
  if (!empty($login_err)) {
      echo '<div class="alert alert-danger">' . $login_err . '</div>';
  }
  ?>

  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" novalidate>
    <div class="mb-3">
      <label for="role" class="form-label">Login as</label>
      <select name="role" id="role" class="form-select <?php echo (!empty($role_err)) ? 'is-invalid' : ''; ?>" required>
        <option value="">-- Select Role --</option>
        <option value="user" <?php if ($role == "user") echo "selected"; ?>>User</option>
        <option value="admin" <?php if ($role == "admin") echo "selected"; ?>>Admin</option>
      </select>
      <div class="invalid-feedback"><?php echo $role_err; ?></div>
    </div>

    <div class="mb-3" id="identifier-group">
      <label for="identifier" class="form-label" id="identifier-label">Email Address</label>
      <input type="text" name="email" id="identifier" class="form-control <?php echo (!empty($email_err) || !empty($username_err)) ? 'is-invalid' : ''; ?>" 
             value="<?php echo htmlspecialchars($role === 'admin' ? $username : $email); ?>" required />
      <div class="invalid-feedback">
        <?php echo $role === 'admin' ? $username_err : $email_err; ?>
      </div>
    </div>

    <div class="mb-4">
      <label for="password" class="form-label">Password</label>
      <input type="password" name="password" id="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" required />
      <div class="invalid-feedback"><?php echo $password_err; ?></div>
    </div>

    <button type="submit" class="btn btn-login w-100">Login</button>

    <p class="mt-3 text-center">Don't have an account? <a href="register.php">Register here</a>.</p>
  </form>
</div>

<script>
  const roleSelect = document.getElementById("role");
  const identifierInput = document.getElementById("identifier");
  const identifierLabel = document.getElementById("identifier-label");

  function updateIdentifierField() {
    if (roleSelect.value === "admin") {
      identifierLabel.textContent = "Username";
      identifierInput.name = "username";
      identifierInput.type = "text";
      identifierInput.placeholder = "Enter your username";
    } else {
      identifierLabel.textContent = "Email Address";
      identifierInput.name = "email";
      identifierInput.type = "email";
      identifierInput.placeholder = "Enter your email";
    }
  }

  roleSelect.addEventListener("change", updateIdentifierField);
  window.addEventListener("DOMContentLoaded", updateIdentifierField);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Footer -->
<footer>
  <div class="container">
    <small>&copy; <?= date("Y") ?> Santan Events. All rights reserved.</small>
  </div>
</footer>

</body>
</html>
