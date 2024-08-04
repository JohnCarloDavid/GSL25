<?php
session_start();
include 'config.php'; // Ensure you have a config.php with database connection setup

$errorMessage = '';

// Check for form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Retrieve user data
    $stmt = $conn->prepare("SELECT password FROM tb_gsl25 WHERE user_email = ?");
    if ($stmt === false) {
        die('Prepare failed: ' . $conn->error); // Handle error
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($hashedPassword);
    $stmt->fetch();
    $stmt->close();

    // Verify password
    if (password_verify($password, $hashedPassword)) {
        $_SESSION['user_email'] = $email;
        header('Location: homepage.php'); // Redirect to homepage after successful login
        exit();
    } else {
        $errorMessage = 'Invalid email or password.';
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="img/GSL25_transparent 2.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .form-container {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }
        .form-container.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .logo-link img {
            transition: transform 0.3s ease, filter 0.3s ease;
        }
        .logo-link:hover img {
            transform: scale(1.1);
            filter: brightness(1.2);
        }
    </style>
</head>
<body class="bg-gradient-to-tl from-green-400 to-indigo-900 min-h-screen flex items-center justify-center">
    <div class="h-full bg-gradient-to-tl from-green-400 to-indigo-900 w-full py-16 px-4">
        <div class="flex justify-center mb-6">
            <a href="index.html" class="logo-link"><img src="img/GSL25_transparent 2.png" alt="logo" class="h-25"></a>
        </div>
        <div id="form-container" class="max-w-md mx-auto bg-gray-200 p-8 border border-gray-300 rounded-lg shadow-lg form-container">
            <h1 class="focus:outline-none text-2xl font-extrabold leading-6 text-gray-800 mb-4 text-center">Log In</h1>

            <?php if (!empty($errorMessage)): ?>
                <div id="error-message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold"><?php echo $errorMessage; ?></strong>
                </div>
            <?php endif; ?>

            <form id="login-form" method="POST" action="login.php">
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 font-bold mb-2">Email:</label>
                    <input type="email" id="email" name="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-gray-700 font-bold mb-2">Password:</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" required>
                        <button type="button" id="toggle-password" class="absolute inset-y-0 right-0 px-3 py-2 text-gray-600">
                            <i class="fa fa-eye" id="password-icon"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Log In</button>
            </form>
            <p class="text-gray-600 text-center mt-4">
                Don't have an account? <a href="signup.php" class="text-blue-500 hover:text-blue-700">Sign up</a>
            </p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show form container with animation
            document.getElementById('form-container').classList.add('visible');

            // Toggle password visibility
            const togglePassword = document.getElementById('toggle-password');
            const passwordField = document.getElementById('password');
            const passwordIcon = document.getElementById('password-icon');
            togglePassword.addEventListener('click', function() {
                if (passwordField.type === 'password') {
                    passwordField.type = 'text';
                    passwordIcon.classList.replace('fa-eye', 'fa-eye-slash');
                } else {
                    passwordField.type = 'password';
                    passwordIcon.classList.replace('fa-eye-slash', 'fa-eye');
                }
            });
        });
    </script>
</body>
</html>
