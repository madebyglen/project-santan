<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION["user_id"]) || !isset($_SESSION["is_admin"])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    header("Location: users.php");
    exit;
}

$users = $conn->query("SELECT id, name, email, created_at FROM users ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Manage Users - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      margin: 0;
    }
    .container.mt-4 {
      flex: 1;
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
<nav class="navbar navbar-expand-lg" style="background-color: #1f0036;">
  <div class="container-fluid">
    <span class="navbar-brand text-white fw-bold">👥 Manage Users</span>
    <div class="ms-auto">
      <a href="admin_dashboard.php" class="btn btn-outline-light">Go to Dashboard</a>
    </div>
  </div>
</nav>

<div class="container mt-4">
  <table class="table table-bordered">
    <thead>
      <tr><th>Name</th><th>Email</th><th>Registered At</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php while ($user = $users->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($user['name']) ?></td>
        <td><?= htmlspecialchars($user['email']) ?></td>
        <td><?= htmlspecialchars($user['created_at']) ?></td>
        <td>
          <a href="users.php?delete=<?= $user['id'] ?>" onclick="return confirm('Delete this user?');" class="btn btn-danger btn-sm">Delete</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<footer>
  <div class="container">
    <small>&copy; <?= date("Y") ?> Santan Events. All rights reserved.</small>
  </div>
</footer>

</body>
</html>
