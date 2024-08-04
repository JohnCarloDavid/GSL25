<?php
session_start();
include 'config.php'; // Ensure you have a config.php with database connection setup

// Check if the user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
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
    <title>GSL25 Steel Trading - Products</title>
    <link rel="icon" href="img/GSL25_transparent 2.png">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .card-container {
            perspective: 1000px;
        }
        .card {
            position: relative;
            width: 100%;
            height: 300px;
            transform-style: preserve-3d;
            transition: transform 0.6s;
            cursor: pointer;
            border-radius: 0.75rem;
        }
        .card:hover {
            transform: rotateY(180deg);
        }
        .card-inner {
            position: absolute;
            width: 100%;
            height: 100%;
            transition: transform 0.6s;
            transform-style: preserve-3d;
        }
        .card-front, .card-back {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: 0.75rem;
        }
        .card-front {
            background-color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-back {
            background-color: #fefefe;
            transform: rotateY(180deg);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 20px;
        }
        .card-back p {
            margin: 0;
        }
        .product-image {
            height: 200px;
            object-fit: cover;
            width: 100%;
            border-radius: 0.75rem 0.75rem 0 0;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 50;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 0.75rem;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <!-- Navbar -->
    <nav class="bg-gray-800 p-4 shadow-md w-full z-10">
        <div class="container mx-auto flex justify-between items-center relative">
            <div class="flex items-center space-x-4">
                <a href="homepage.php"><img src="img/GSL25_transparent 2.png" alt="logo" class="h-12"></a>
                <a href="homepage.php" class="text-2xl font-bold text-white transition duration-300 hover:text-blue-400">GSL25 Steel Trading</a>
            </div>
        </div>
    </nav>
    
    <!-- Product Cards -->
    <section id="products" class="py-20 mt-16">
        <div class="container mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-8">
                <!-- Product 1 -->
                <div class="card-container">
                    <div class="card" onclick="showModal('modal1')">
                        <div class="card-inner">
                            <div class="card-front">
                                <img src="img/pr1.jpg" alt="Product 1" class="product-image">
                                <div class="p-4 text-center">
                                    <h3 class="text-xl font-semibold text-gray-900">Tubular</h3>
                                    <p class="text-medium mb-2 text-gray-700">High-quality tubular steel for various construction needs.</p>
                                </div>
                            </div>
                            <div class="card-back">
                                <p>Stock: 20 units</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Product 2 -->
                <div class="card-container">
                    <div class="card" onclick="showModal('modal2')">
                        <div class="card-inner">
                            <div class="card-front">
                                <img src="img/pr2.jpg" alt="Product 2" class="product-image">
                                <div class="p-4 text-center">
                                    <h3 class="text-xl font-semibold text-gray-900">C-purlins</h3>
                                    <p class="text-medium mb-2 text-gray-700">Versatile C-purlins for structural support in buildings.</p>
                                </div>
                            </div>
                            <div class="card-back">
                                <p>Stock: 15 units</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Product 3 -->
                <div class="card-container">
                    <div class="card" onclick="showModal('modal3')">
                        <div class="card-inner">
                            <div class="card-front">
                                <img src="img/pr3.jpg" alt="Product 3" class="product-image">
                                <div class="p-4 text-center">
                                    <h3 class="text-xl font-semibold text-gray-900">Wall Angle</h3>
                                    <p class="text-medium mb-2 text-gray-700">Sturdy wall angles for secure fixture installations.</p>
                                </div>
                            </div>
                            <div class="card-back">
                                <p>Stock: 25 units</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Product 4 -->
                <div class="card-container">
                    <div class="card" onclick="showModal('modal4')">
                        <div class="card-inner">
                            <div class="card-front">
                                <img src="img/pr4.jpeg" alt="Product 4" class="product-image">
                                <div class="p-4 text-center">
                                    <h3 class="text-xl font-semibold text-gray-900">Galvanized Steel Sheet</h3>
                                    <p class="text-medium mb-2 text-gray-700">Durable galvanized steel sheets for weather-resistant applications.</p>
                                </div>
                            </div>
                            <div class="card-back">
                                <p>Stock: 10 units</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Product 5 -->
                <div class="card-container">
                    <div class="card" onclick="showModal('modal5')">
                        <div class="card-inner">
                            <div class="card-front">
                                <img src="img/pr5.jpg" alt="Product 5" class="product-image">
                                <div class="p-4 text-center">
                                    <h3 class="text-xl font-semibold text-gray-900">Gi Pipe</h3>
                                    <p class="text-medium mb-2 text-gray-700">High-quality GI pipes for plumbing and structural applications.</p>
                                </div>
                            </div>
                            <div class="card-back">
                                <p>Stock: 18 units</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Product 6 -->
                <div class="card-container">
                    <div class="card" onclick="showModal('modal6')">
                        <div class="card-inner">
                            <div class="card-front">
                                <img src="img/pr6.webp" alt="Product 6" class="product-image">
                                <div class="p-4 text-center">
                                    <h3 class="text-xl font-semibold text-gray-900">MS Plates</h3>
                                    <p class="text-medium mb-2 text-gray-700">High-quality MS plates for construction and manufacturing.</p>
                                </div>
                            </div>
                            <div class="card-back">
                                <p>Stock: 12 units</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Product 7 -->
                <div class="card-container">
                    <div class="card" onclick="showModal('modal7')">
                        <div class="card-inner">
                            <div class="card-front">
                                <img src="img/pr7.webp" alt="Product 7" class="product-image">
                                <div class="p-4 text-center">
                                    <h3 class="text-xl font-semibold text-gray-900">Steel Angle Bar</h3>
                                    <p class="text-medium mb-2 text-gray-700">Durable steel angle bars for structural support.</p>
                                </div>
                            </div>
                            <div class="card-back">
                                <p>Stock: 22 units</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Product 8 -->
                <div class="card-container">
                    <div class="card" onclick="showModal('modal8')">
                        <div class="card-inner">
                            <div class="card-front">
                                <img src="img/pr8.jpg" alt="Product 8" class="product-image">
                                <div class="p-4 text-center">
                                    <h3 class="text-xl font-semibold text-gray-900">MS Square Tubes</h3>
                                    <p class="text-medium mb-2 text-gray-700">Reliable MS square tubes for construction and fabrication.</p>
                                </div>
                            </div>
                            <div class="card-back">
                                <p>Stock: 17 units</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Product 9 -->
                <div class="card-container">
                    <div class="card" onclick="showModal('modal9')">
                        <div class="card-inner">
                            <div class="card-front">
                                <img src="img/pr9.jpg" alt="Product 9" class="product-image">
                                <div class="p-4 text-center">
                                    <h3 class="text-xl font-semibold text-gray-900">GI Sheets</h3>
                                    <p class="text-medium mb-2 text-gray-700">High-quality GI sheets for various industrial uses.</p>
                                </div>
                            </div>
                            <div class="card-back">
                                <p>Stock: 30 units</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Product 10 -->
                <div class="card-container">
                    <div class="card" onclick="showModal('modal10')">
                        <div class="card-inner">
                            <div class="card-front">
                                <img src="img/pr10.jpg" alt="Product 10" class="product-image">
                                <div class="p-4 text-center">
                                    <h3 class="text-xl font-semibold text-gray-900">MS Channels</h3>
                                    <p class="text-medium mb-2 text-gray-700">Sturdy MS channels for structural applications.</p>
                                </div>
                            </div>
                            <div class="card-back">
                                <p>Stock: 14 units</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal Templates -->
    <!-- Product 1 Modal -->
    <div id="modal1" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('modal1')">&times;</span>
            <h2 class="text-2xl font-semibold">Tubular</h2>
            <p>High-quality tubular steel for various construction needs. Available in different sizes and thicknesses to suit your specific requirements.</p>
        </div>
    </div>
    <!-- Product 2 Modal -->
    <div id="modal2" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('modal2')">&times;</span>
            <h2 class="text-2xl font-semibold">C-purlins</h2>
            <p>Versatile C-purlins for structural support in buildings. Our C-purlins are made from premium materials to ensure durability and strength.</p>
        </div>
    </div>
    <!-- Product 3 Modal -->
    <div id="modal3" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('modal3')">&times;</span>
            <h2 class="text-2xl font-semibold">Wall Angle</h2>
            <p>Sturdy wall angles for secure fixture installations. Ideal for use in both commercial and residential projects.</p>
        </div>
    </div>
    <!-- Product 4 Modal -->
    <div id="modal4" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('modal4')">&times;</span>
            <h2 class="text-2xl font-semibold">Galvanized Steel Sheet</h2>
            <p>Durable galvanized steel sheets for weather-resistant applications. Available in various dimensions to meet your project needs.</p>
        </div>
    </div>
    <!-- Product 5 Modal -->
    <div id="modal5" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('modal5')">&times;</span>
            <h2 class="text-2xl font-semibold">Gi Pipe</h2>
            <p>High-quality GI pipes for plumbing and structural applications. Our GI pipes are designed to withstand high pressure and provide long-lasting performance.</p>
        </div>
    </div>
    <!-- Product 6 Modal -->
    <div id="modal6" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('modal6')">&times;</span>
            <h2 class="text-2xl font-semibold">MS Plates</h2>
            <p>High-quality MS plates for construction and manufacturing. Our MS plates are made from premium materials, ensuring durability and precision for all your fabrication needs.</p>
        </div>
    </div>
    <!-- Product 7 Modal -->
    <div id="modal7" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('modal7')">&times;</span>
            <h2 class="text-2xl font-semibold">Steel Angle Bar</h2>
            <p>Durable steel angle bars for structural support. Our angle bars come in various sizes to meet your project's specific requirements.</p>
        </div>
    </div>
    <!-- Product 8 Modal -->
    <div id="modal8" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('modal8')">&times;</span>
            <h2 class="text-2xl font-semibold">MS Square Tubes</h2>
            <p>Reliable MS square tubes for construction and fabrication. Our MS square tubes are designed for strength and precision in your projects.</p>
        </div>
    </div>
    <!-- Product 9 Modal -->
    <div id="modal9" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('modal9')">&times;</span>
            <h2 class="text-2xl font-semibold">GI Sheets</h2>
            <p>High-quality GI sheets for various industrial uses. These sheets offer excellent corrosion resistance and durability for a range of applications.</p>
        </div>
    </div>
    <!-- Product 10 Modal -->
    <div id="modal10" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('modal10')">&times;</span>
            <h2 class="text-2xl font-semibold">MS Channels</h2>
            <p>Sturdy MS channels for structural applications. Our MS channels are ideal for heavy-duty construction and support structures.</p>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        function showModal(modalId) {
            document.getElementById(modalId).style.display = "block";
        }
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
        }
        // Close modal when clicking outside of the modal content
        window.onclick = function(event) {
            var modals = document.getElementsByClassName('modal');
            for (var i = 0; i < modals.length; i++) {
                if (event.target == modals[i]) {
                    modals[i].style.display = "none";
                }
            }
        }
    </script>
</body>
</html>

