<?php
session_start();
include 'config.php'; // Ensure you have a config.php with database connection setup

$errorMessage = '';
$successMessage = '';

// Check for form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $surname = $_POST['surname'];
    $firstname = $_POST['firstname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate email domain
    if (!preg_match('/@gmail\.com$/i', $email)) {
        $errorMessage = 'Only Gmail addresses are allowed.';
    } elseif ($password !== $confirm_password) {
        $errorMessage = 'Passwords do not match.';
    } elseif (strlen($password) < 8 || strlen($password) > 16 || !preg_match('/[A-Z]/', $password) || !preg_match('/[\W]/', $password) || preg_match('/\s/', $password)) {
        $errorMessage = 'Password must be between 8 and 16 characters long, contain at least one uppercase letter, one special character, and have no spaces.';
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM tb_gsl25 WHERE user_email = ?");
        if ($stmt === false) {
            die('Prepare failed: ' . $conn->error); // Handle error
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($emailCount);
        $stmt->fetch();
        $stmt->close();

        if ($emailCount > 0) {
            $errorMessage = 'This email is already registered.';
        } else {
            // Insert into the database without hashing the password
            $stmt = $conn->prepare("INSERT INTO tb_gsl25 (surname, firstname, user_email, password) VALUES (?, ?, ?, ?)");
            if ($stmt === false) {
                die('Prepare failed: ' . $conn->error); // Handle error
            }
            $stmt->bind_param("ssss", $surname, $firstname, $email, $password);

            if ($stmt->execute()) {
                $successMessage = 'Account successfully created!'; // Set success message
                header('Location: login.php'); // Redirect to login after successful registration
                exit();
            } else {
                $errorMessage = 'Error: ' . $stmt->error;
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
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
        .form-group {
            display: flex;
            justify-content: space-between;
        }
        .form-group .input-container {
            flex: 1;
            margin-right: 1rem;
        }
        .form-group .input-container:last-child {
            margin-right: 0;
        }
    </style>
</head>
<body class="bg-gradient-to-tl from-green-400 to-indigo-900 min-h-screen flex items-center justify-center">
    <div class="h-full bg-gradient-to-tl from-green-400 to-indigo-900 w-full py-16 px-4">
        <div class="flex justify-center mb-6">
            <a href="index.html" class="logo-link"><img src="img/GSL25_transparent 2.png" alt="logo" class="h-25"></a>
        </div>
        <div id="form-container" class="max-w-md mx-auto bg-gray-200 p-8 border border-gray-300 rounded-lg shadow-lg form-container">
            <h1 class="focus:outline-none text-2xl font-extrabold leading-6 text-gray-800 mb-4 text-center">Sign Up</h1>

            <div id="response-message"></div>

            <form id="signup-form">
                <div class="form-group mb-4">
                    <div class="input-container">
                        <label for="surname" class="block text-gray-700 font-bold mb-2">Surname:</label>
                        <input type="text" id="surname" name="surname" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    <div class="input-container">
                        <label for="firstname" class="block text-gray-700 font-bold mb-2">First Name:</label>
                        <input type="text" id="firstname" name="firstname" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 font-bold mb-2">Email:</label>
                    <input type="email" id="email" name="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-gray-700 font-bold mb-2">Password:</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" minlength="8" maxlength="16" required>
                        <button type="button" id="toggle-password" class="absolute inset-y-0 right-0 px-3 py-2 text-gray-600">
                            <i class="fa fa-eye" id="password-icon"></i>
                        </button>
                    </div>
                    <p id="password-error" class="text-red-500 text-xs italic hidden">Password must be between 8 and 16 characters long, contain at least one uppercase letter, one special character, and have no spaces.</p>
                </div>
                <div class="mb-6">
                    <label for="confirm_password" class="block text-gray-700 font-bold mb-2">Confirm Password:</label>
                    <div class="relative">
                        <input type="password" id="confirm_password" name="confirm_password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" required>
                        <button type="button" id="toggle-confirm-password" class="absolute inset-y-0 right-0 px-3 py-2 text-gray-600">
                            <i class="fa fa-eye" id="confirm-password-icon"></i>
                        </button>
                    </div>
                    <p id="confirm-password-error" class="text-red-500 text-xs italic hidden">Passwords do not match.</p>
                </div>
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Sign Up</button>
            </form>
            <p class="text-gray-600 text-center mt-4">
                Already have an account? <a href="login.php" class="text-blue-500 hover:text-blue-700">Log in</a>
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

            // Handle form submission with AJAX
            const form = document.getElementById('signup-form');
            form.addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent the default form submission

                if (!validatePassword()) {
                    return; // Stop if validation fails
                }

                const formData = new FormData(form);

                fetch('signup.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    const responseMessage = document.getElementById('response-message');
                    responseMessage.innerHTML = '';

                    if (data.success) {
                        responseMessage.innerHTML = `<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert"><strong class="font-bold">${data.success}</strong></div>`;
                        setTimeout(() => {
                            window.location.href = 'login.php'; // Redirect to login page after successful registration
                        }, 2000); // Redirect after 2 seconds to show the success message
                    } else if (data.error) {
                        responseMessage.innerHTML = `<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert"><strong class="font-bold">${data.error}</strong></div>`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        });

        function validatePassword() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            const passwordError = document.getElementById('password-error');
            const confirmPasswordError = document.getElementById('confirm-password-error');
            let valid = true;

            // Password validation
            const passwordRegex = /^(?=.*[A-Z])(?=.*[\W])(?=\S)(?!.*\s).{8,16}$/;
            if (!passwordRegex.test(password)) {
                passwordError.classList.remove('hidden');
                valid = false;
            } else {
                passwordError.classList.add('hidden');
            }

            // Confirm password validation
            if (password !== confirmPassword) {
                confirmPasswordError.classList.remove('hidden');
                valid = false;
            } else {
                confirmPasswordError.classList.add('hidden');
            }

            return valid;
        }
    </script>
</body>
</html>

