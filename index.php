<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Project Santan - Event Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500&family=Roboto&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Roboto', sans-serif;
      background-color: #f4f4f4;
    }

    .navbar {
      background: linear-gradient(90deg, #1f0036, #42075f);
    }

    .navbar-brand {
      font-family: 'Orbitron', sans-serif;
      font-size: 1.8rem;
    }

    .hero {
      background: linear-gradient(to right, #430089, #82ffa1);
      height: 90vh;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .hero h1 {
      font-size: 3rem;
      font-weight: bold;
    }

    .hero p {
      font-size: 1.3rem;
    }

    .btn-custom {
      padding: 12px 30px;
      font-size: 1.1em;
      border-radius: 30px;
      transition: 0.3s;
    }

    .btn-custom:hover {
      box-shadow: 0 0 15px rgba(255, 255, 255, 0.6);
      transform: scale(1.05);
    }

    .belief-card {
      background-color: white;
      border: 2px solid transparent;
      border-radius: 15px;
      padding: 30px 20px;
      text-align: center;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease, box-shadow 0.4s ease, border-color 0.4s ease;
    }

    .belief-card:hover {
      transform: translateY(-10px);
      box-shadow:
        0 0 10px 2px #7f5af0,
        0 0 20px 4px #6f42c1,
        0 0 30px 8px #9f7df0;
      border-color: #7f5af0;
    }

    .belief-card h4 {
      font-size: 1.5rem;
      margin-bottom: 15px;
      color: #6f42c1;
    }

    .belief-card p {
      font-size: 1rem;
      color: #333;
    }

    #beliefs {
      background: #fff;
      padding: 80px 0;
    }

    .section-title {
      font-family: 'Orbitron', sans-serif;
      font-size: 2rem;
      margin-bottom: 40px;
      color: #42075f;
    }

    footer {
      background-color: #1f0036;
      color: white;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark px-4">
  <a class="navbar-brand" href="#">🎧 Santan Events</a>
  <div class="ms-auto">
    <a href="users/login.php" class="btn btn-outline-light me-2 btn-custom">Login</a>
    <a href="users/register.php" class="btn btn-light btn-custom">Register</a>
  </div>
</nav>

<!-- Hero Section -->
<section class="hero">
  <div class="container">
    <h1 class="display-4">Where Events meet Magic</h1>
    <p class="lead">Groovy vibes. Unforgettable experiences. Effortless bookings.</p>
    <a href="#beliefs" class="btn btn-light btn-lg mt-4 btn-custom">Discover Beliefs</a>
  </div>
</section>

<!-- Core Beliefs Section -->
<section id="beliefs">
  <div class="container">
    <h2 class="text-center section-title">Our Core Beliefs</h2>
    <div class="row justify-content-center">
      <div class="col-md-4 mb-4">
        <div class="belief-card">
          <h4>🔥 Passion</h4>
          <p>We bring energy and dedication to every stage, artist, and light beam.</p>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="belief-card">
          <h4>🎯 Precision</h4>
          <p>Flawless planning, smooth coordination — all for the perfect vibe.</p>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="belief-card">
          <h4>🎵 Vibes</h4>
          <p> We believe music heals and connects souls. </p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Footer -->
<footer class="text-center py-4 mt-5">
  <div class="container">
    <small>&copy; <?php echo date("Y"); ?> Santan Events. All rights reserved.</small>
  </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
