<?php
session_start();
include 'config.php'; // Ensure you have a config.php with database connection setup

// Check if the user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

// Fetch user information
$userEmail = $_SESSION['user_email'];
$stmt = $conn->prepare("SELECT firstname FROM tb_gsl25 WHERE user_email = ?");
if ($stmt === false) {
    die('Prepare failed: ' . $conn->error); // Handle error
}
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$stmt->bind_result($firstname);
$stmt->fetch();
$stmt->close();

if (!$firstname) {
    $firstname = "User"; // Default if no name is found
}

// Logout functionality
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GSL25 Steel Trading</title>
  <link rel="icon" href="img/GSL25_transparent 2.png">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
  .nav-link {
    transition: color 0.3s, transform 0.3s;
  }
  .nav-link:hover {
    color: #3b82f6;
    transform: scale(1.1);
  }
  .hero-section {
    background: url('img/backgroundhome.jpg') no-repeat center center;
    background-size: cover;
    position: relative;
  }
  .hero-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    color: white;
  }
  .hero-user-name {
    font-size: 1.5rem; /* Adjust size */
    font-weight: bold; /* Adjust weight */
    font-family: 'Arial', sans-serif; 
  }
  .hero-button {
    background: linear-gradient(90deg, #1d4ed8, #3b82f6);
  }
  .hero-button:hover {
    background: linear-gradient(90deg, #1e40af, #2563eb);
  }
  .logout-button {
    background: #ef4444;
    color: white;
  }
  .logout-button:hover {
    background: #dc2626;
  }
</style>

</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">
<!-- Navbar -->
<nav class="bg-gray-800 p-4 shadow-md fixed w-full z-10">
    <div class="container mx-auto flex justify-between items-center">
        <!-- Logo and Title -->
        <div class="flex items-center space-x-4">
            <img src="img/GSL25_transparent 2.png" alt="logo" class="h-12">
            <a href="homepage.php" class="text-2xl font-bold text-white transition duration-300 hover:text-blue-400">GSL25 Steel Trading</a>
        </div>

        <!-- Desktop Menu -->
        <div class="hidden md:flex space-x-4 items-center">
            <a href="homepage.php" class="nav-link text-white">Home</a>
            <a href="products.php" class="nav-link text-white">Products</a>
            <a href="#about" class="nav-link text-white">About</a>
            <a href="#contact" class="nav-link text-white">Contact</a>
            <a href="homepage.php?logout=true" class="logout-button px-4 py-2 rounded-md text-lg hover:bg-red-700 transition duration-300">Log Out</a>
        </div>

        <!-- Mobile Menu Button -->
        <button id="menu-button" class="md:hidden text-white focus:outline-none">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    <!-- Mobile Menu -->
    <div id="mobile-menu" class="md:hidden fixed top-0 right-0 w-2/3 h-full bg-gray-800 text-white transform translate-x-full transition-transform duration-300">
        <div class="flex justify-end p-4">
            <button id="close-menu" class="text-white text-2xl">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="flex flex-col items-center mt-8 space-y-4">
            <a href="homepage.php" class="text-xl">Home</a>
            <a href="products.php" class="text-xl">Products</a>
            <a href="#about" class="text-xl">About</a>
            <a href="#contact" class="text-xl">Contact</a>
            <a href="homepage.php?logout=true" class="logout-button px-4 py-2 rounded-md text-lg hover:bg-red-700 transition duration-300">Log Out</a>
        </div>
    </div>
</nav>
<!-- Hero Section -->
<section class="hero-section h-screen flex flex-col justify-center items-center text-center">
    <div class="bg-black bg-opacity-50 h-full w-full flex flex-col justify-center items-center">
        <p class="text-xl font-semibold text-white mb-4">
            Hello, <span class="font-bold"><?php echo htmlspecialchars($firstname); ?></span>! Welcome to GSL25 Steel Trading
        </p>
        <h1 class="text-white text-5xl font-bold mb-4">Your Trusted Source for Steel Supplies</h1>
        <p class="text-white text-xl mb-8">Affordable and reliable construction supplies in Sasmuan, Guagua, or Pampanga</p>
        <a href="products.php" class="hero-button text-white px-6 py-3 rounded-md text-lg hover:bg-blue-800 transition duration-300 transform hover:scale-105 flex items-center justify-center">
            <lord-icon
              src="https://cdn.lordicon.com/cjknrhek.json"
              trigger="hover"
              style="width:30px;height:30px"
              class="mr-2">
            </lord-icon>
            Explore Products
        </a>
    </div>
</section>


  <!-- About Section -->
  <section id="about" class="py-20 bg-white">
    <div class="container mx-auto text-center">
      <h2 class="text-4xl font-bold mb-8">About Us</h2>
      <div class="flex flex-col md:flex-row items-center md:space-x-8">
        <img src="img/contactimg.jpg" alt="About Image" class="mb-8 md:mb-0 md:w-1/2 rounded-lg shadow-md">
        <div class="md:w-1/2 text-left">
          <p class="text-gray-700 leading-relaxed mb-4">GSL25 Steel Trading is your go-to supplier for galvanized steel sheets, tubulars, c-purlins, angle bars, and more. Our commitment to pricing and reliability sets us apart in the industry.</p>
          <p class="text-gray-700 leading-relaxed">Whether you're building a small project or a large-scale construction, we have the supplies you need to get the job done. Our knowledgeable team is here to assist you with any questions or needs you may have.</p>
          <div class="mt-4 flex items-center space-x-4">
            <i class="fas fa-phone-alt text-blue-600"></i>
            <span class="text-gray-700">+123 456 7890</span>
          </div>
          <div class="mt-4 flex items-center space-x-4">
            <i class="fas fa-map-marker-alt text-blue-600"></i>
            <span class="text-gray-700">San Nicolas 2nd, Sasmuan, Guagua, Pampanga</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Products Section -->
  <section id="products" class="py-20 bg-gray-100">
    <div class="container mx-auto text-center">
      <h2 class="text-4xl font-bold mb-8">Our Products</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Product 1 -->
        <div class="bg-white p-6 shadow-md rounded-md transition duration-300 transform hover:scale-105">
          <img src="img/galvanized.jpg" alt="Product 1" class="mb-4 w-full h-48 object-cover rounded-md">
          <h3 class="text-2xl font-bold mb-2">Galvanized Steel Sheets</h3>
          <p class="text-gray-700 mb-4">Durable and versatile steel sheets for all your construction needs.</p>
        </div>
        <!-- Product 2 -->
        <div class="bg-white p-6 shadow-md rounded-md transition duration-300 transform hover:scale-105">
          <img src="img/tubulars.webp" alt="Product 2" class="mb-4 w-full h-48 object-cover rounded-md">
          <h3 class="text-2xl font-bold mb-2">Tubulars</h3>
          <p class="text-gray-700 mb-4">High-quality tubulars available in various sizes and specifications.</p>
        </div>
        <!-- Product 3 -->
        <div class="bg-white p-6 shadow-md rounded-md transition duration-300 transform hover:scale-105">
          <img src="img/cpurlins.jpg" alt="Product 3" class="mb-4 w-full h-48 object-cover rounded-md">
          <h3 class="text-2xl font-bold mb-2">C-Purlins</h3>
          <p class="text-gray-700 mb-4">Sturdy c-purlins perfect for structural support in buildings.</p>
        </div>
      </div>
    </div>
  </section>

<!-- Contact Section -->
  <section id="contact" class="py-20 bg-white">
    <div class="container mx-auto text-center">
      <h2 class="text-4xl font-bold mb-8">Contact Us</h2>
      <p class="text-gray-700 leading-relaxed mb-4">Have questions or need assistance? Reach out to us using the information below:</p>
        <div class="max-w-lg mx-auto">
            <p class="text-lg mb-4">
                <i class="fas fa-map-marker-alt text-blue-600"></i> <span class="font-semibold">Address:</span> 123 Construction Rd, Sasmuan, Pampanga
            </p>
            <p class="text-lg mb-4">
                <i class="fas fa-phone-alt text-blue-600"></i> <span class="font-semibold">Phone:</span> (123) 456-7890
            </p>
            <p class="text-lg mb-4">
                <i class="fas fa-envelope text-blue-600"></i> <span class="font-semibold">Email:</span> contact@gsl25.com
            </p>
            <p class="text-lg mb-4">
                <i class="fas fa-clock text-blue-600"></i> <span class="font-semibold">Business Hours:</span> Mon-Fri: 8 AM - 6 PM, Sat: 9 AM - 3 PM
            </p>
        </div>
    </div>
  </section>


  <!-- Footer -->
  <footer class="bg-gray-800 text-white py-8">
    <div class="container mx-auto text-center">
      <p>&copy; 2024 GSL25 Steel Trading. All rights reserved.</p>
    </div>
  </footer>
  <script>
  document.getElementById('menu-button').addEventListener('click', function() {
    document.getElementById('mobile-menu').classList.remove('translate-x-full');
  });

  document.getElementById('close-menu').addEventListener('click', function() {
    document.getElementById('mobile-menu').classList.add('translate-x-full');
  });
</script>
<script src="https://cdn.lordicon.com/lordicon.js"></script>
</body>
</html>
