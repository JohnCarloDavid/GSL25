<?php
session_start();
include 'config.php'; // Ensure you have a config.php with database connection setup

$errorMessage = '';
$successMessage = '';

// Check if username is provided
if (!isset($_GET['username'])) {
    header('Location: forgot_password.php');
    exit();
}

$username = $_GET['username'];

// Check for form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate passwords
    if ($new_password !== $confirm_password) {
        $errorMessage = 'Passwords do not match.';
    } elseif (strlen($new_password) < 8 || strlen($new_password) > 16 || !preg_match('/[A-Z]/', $new_password) || !preg_match('/[\W]/', $new_password) || preg_match('/\s/', $new_password)) {
        $errorMessage = 'Password must be between 8 and 16 characters long, contain at least one uppercase letter, one special character, and have no spaces.';
    } else {
        // Update password without hashing
        $stmt = $conn->prepare("UPDATE tb_gsl25 SET password = ? WHERE username = ?");
        if ($stmt === false) {
            die('Prepare failed: ' . $conn->error); // Handle error
        }

        // Bind parameters
        $stmt->bind_param("ss", $new_password, $username);
        
        if ($stmt->execute()) {
            $successMessage = 'Password successfully reset!';
        } else {
            $errorMessage = 'Error occurred while resetting the password. Please try again.';
        }
        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="icon" href="img/GSL25_transparent 2.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* Your existing styles */
    </style>
</head>
<body class="bg-gradient-to-tl from-green-400 to-indigo-900 min-h-screen flex items-center justify-center">
    <div class="h-full bg-gradient-to-tl from-green-400 to-indigo-900 w-full py-16 px-4">
        <div class="flex justify-center mb-6">
            <a href="index.html" class="logo-link"><img src="img/GSL25_transparent 2.png" alt="logo" class="h-25"></a>
        </div>
        <div id="form-container" class="max-w-md mx-auto bg-gray-200 p-8 border border-gray-300 rounded-lg shadow-lg form-container">
            <h1 class="focus:outline-none text-2xl font-extrabold leading-6 text-gray-800 mb-4 text-center">Reset Password</h1>

            <?php if (!empty($errorMessage)): ?>
                <div id="error-message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold"><?php echo $errorMessage; ?></strong>
                </div>
            <?php endif; ?>

            <?php if (!empty($successMessage)): ?>
                <div id="success-message" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold"><?php echo $successMessage; ?></strong>
                </div>
            <?php endif; ?>

            <form id="reset-password-form" method="POST" action="reset_password.php?username=<?php echo urlencode($username); ?>">
                <div class="mb-4">
                    <label for="new_password" class="block text-gray-700 font-bold mb-2">New Password:</label>
                    <div class="relative">
                        <input type="password" id="new_password" name="new_password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" required>
                        <button type="button" id="toggle-new-password" class="absolute inset-y-0 right-0 px-3 py-2 text-gray-600">
                            <i class="fa fa-eye" id="new-password-icon"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="confirm_password" class="block text-gray-700 font-bold mb-2">Confirm New Password:</label>
                    <div class="relative">
                        <input type="password" id="confirm_password" name="confirm_password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" required>
                        <button type="button" id="toggle-confirm-password" class="absolute inset-y-0 right-0 px-3 py-2 text-gray-600">
                            <i class="fa fa-eye" id="confirm-password-icon"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Reset Password</button>
            </form>
            <p class="mt-4 text-center text-gray-600">Remembered your password? <a href="login.php" class="text-green-500 hover:text-green-700 font-bold">Log in here</a>.</p>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle new password visibility
            const toggleNewPassword = document.getElementById('toggle-new-password');
            const newPasswordField = document.getElementById('new_password');
            const newPasswordIcon = document.getElementById('new-password-icon');
            toggleNewPassword.addEventListener('click', function() {
                if (newPasswordField.type === 'password') {
                    newPasswordField.type = 'text';
                    newPasswordIcon.classList.replace('fa-eye', 'fa-eye-slash');
                } else {
                    newPasswordField.type = 'password';
                    newPasswordIcon.classList.replace('fa-eye-slash', 'fa-eye');
                }
            });

            // Toggle confirm password visibility
            const toggleConfirmPassword = document.getElementById('toggle-confirm-password');
            const confirmPasswordField = document.getElementById('confirm_password');
            const confirmPasswordIcon = document.getElementById('confirm-password-icon');
            toggleConfirmPassword.addEventListener('click', function() {
                if (confirmPasswordField.type === 'password') {
                    confirmPasswordField.type = 'text';
                    confirmPasswordIcon.classList.replace('fa-eye', 'fa-eye-slash');
                } else {
                    confirmPasswordField.type = 'password';
                    confirmPasswordIcon.classList.replace('fa-eye-slash', 'fa-eye');
                }
            });
        });
    </script>
</body>
</html>
