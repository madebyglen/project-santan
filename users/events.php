<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION["user_id"]) || !isset($_SESSION["is_admin"])) {
    header("Location: login.php");
    exit;
}

$action = $_GET['action'] ?? '';
$event_id = $_GET['id'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $location = $_POST['location'] ?? '';
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';
    $category = $_POST['category'] ?? '';
    $ticket_price = $_POST['ticket_price'] ?? 0;

    if ($action === 'edit' && $event_id) {
        $sql = "UPDATE events SET title=?, description=?, location=?, date=?, time=?, category=?, ticket_price=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssdi", $title, $description, $location, $date, $time, $category, $ticket_price, $event_id);
        $stmt->execute();
        header("Location: events.php");
        exit;
    } else {
        $sql = "INSERT INTO events (title, description, location, date, time, category, ticket_price) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssd", $title, $description, $location, $date, $time, $category, $ticket_price);
        $stmt->execute();
        header("Location: events.php");
        exit;
    }
}

if ($action === 'delete' && $event_id) {
    $stmt = $conn->prepare("DELETE FROM events WHERE id=?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    header("Location: events.php");
    exit;
}

$edit_event = null;
if ($action === 'edit' && $event_id) {
    $stmt = $conn->prepare("SELECT * FROM events WHERE id=?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_event = $result->fetch_assoc();
}

$events = $conn->query("SELECT * FROM events ORDER BY date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Manage Events - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg" style="background-color: #1f0036;">
  <div class="container-fluid">
    <a class="navbar-brand text-white fw-bold" href="#">🎟️ Manage Events</a>
    <div class="ms-auto d-flex">
      <!-- <a href="events.php" class="btn btn-success me-2">Create New Event</a> -->
      <a href="admin_dashboard.php" class="btn btn-outline-light">Go to Dashboard</a>
    </div>
  </div>
</nav>

<div class="container mt-4">

  <h2 class="mb-4"><?= $action === 'edit' ? 'Edit Event' : 'Create New Event' ?></h2>

  <form method="POST" class="mb-4">
    <div class="mb-3">
      <label>Title</label>
      <input type="text" name="title" required class="form-control" value="<?= htmlspecialchars($edit_event['title'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label>Description</label>
      <textarea name="description" required class="form-control"><?= htmlspecialchars($edit_event['description'] ?? '') ?></textarea>
    </div>
    <div class="mb-3">
      <label>Location</label>
      <input type="text" name="location" required class="form-control" value="<?= htmlspecialchars($edit_event['location'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label>Date</label>
      <input type="date" name="date" required class="form-control" value="<?= htmlspecialchars($edit_event['date'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label>Time</label>
      <input type="time" name="time" required class="form-control" value="<?= htmlspecialchars($edit_event['time'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label>Category</label>
      <input type="text" name="category" required class="form-control" value="<?= htmlspecialchars($edit_event['category'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label>Ticket Price (Ksh)</label>
      <input type="number" step="0.01" name="ticket_price" required class="form-control" value="<?= htmlspecialchars($edit_event['ticket_price'] ?? '0.00') ?>">
    </div>

    <button type="submit" class="btn btn-primary"><?= $action === 'edit' ? 'Update' : 'Create' ?></button>
    <a href="events.php" class="btn btn-secondary">Cancel</a>
  </form>

  <h2>All Events</h2>

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Title</th>
        <th>Date</th>
        <th>Time</th>
        <th>Location</th>
        <th>Category</th>
        <th>Price (Ksh)</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php while ($event = $events->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($event['title']) ?></td>
        <td><?= htmlspecialchars($event['date']) ?></td>
        <td><?= htmlspecialchars($event['time']) ?></td>
        <td><?= htmlspecialchars($event['location']) ?></td>
        <td><?= htmlspecialchars($event['category']) ?></td>
        <td><?= number_format($event['ticket_price'], 2) ?></td>
        <td>
          <a href="events.php?action=edit&id=<?= $event['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
          <a href="events.php?action=delete&id=<?= $event['id'] ?>" onclick="return confirm('Are you sure?');" class="btn btn-sm btn-danger">Delete</a>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>

<!-- Footer -->
<footer>
  <div class="container">
    <small>&copy; <?= date("Y") ?> Santan Events. All rights reserved.</small>
  </div>
</footer>

<!-- Footer Style -->
<style>
  footer {
    background-color: #1f0036;
    color: white;
    text-align: center;
    padding: 15px 0;
    margin-top: 50px;
  }
</style>

</body>
</html>
