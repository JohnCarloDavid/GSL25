<?php
session_start();
include 'config.php'; // Ensure you have a config.php with database connection setup

$errorMessage = '';
$successMessage = '';

// Fetch security questions for the dropdown
$questions = [];
$stmt = $conn->prepare("SELECT id, question FROM security_questions");
if ($stmt === false) {
    die('Prepare failed: ' . $conn->error); // Handle error
}
$stmt->execute();
$stmt->bind_result($id, $question);
while ($stmt->fetch()) {
    $questions[] = ['id' => $id, 'question' => $question];
}
$stmt->close();

// Check for form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $surname = $_POST['surname'];
    $firstname = $_POST['firstname'];
    $username = $_POST['username'];
    $mobile = $_POST['mobile'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $security_question = $_POST['security_question'];
    $security_answer = $_POST['security_answer'];

    // Validate password
    if ($password !== $confirm_password) {
        $errorMessage = 'Passwords do not match.';
    } elseif (strlen($password) < 8 || strlen($password) > 16 || !preg_match('/[A-Z]/', $password) || !preg_match('/[\W]/', $password) || preg_match('/\s/', $password)) {
        $errorMessage = 'Password must be between 8 and 16 characters long, contain at least one uppercase letter, one special character, and have no spaces.';
    } else {
        // Check if mobile number is already in use
        $stmt = $conn->prepare("SELECT id FROM tb_gsl25 WHERE mobile = ?");
        if ($stmt === false) {
            die('Prepare failed: ' . $conn->error); // Handle error
        }
        $stmt->bind_param("s", $mobile);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errorMessage = 'Mobile number is already registered.';
            $stmt->close(); // Close the statement
        } else {
            // Check if username is already in use
            $stmt = $conn->prepare("SELECT id FROM tb_gsl25 WHERE username = ?");
            if ($stmt === false) {
                die('Prepare failed: ' . $conn->error); // Handle error
            }
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $errorMessage = 'Username is already taken.';
                $stmt->close(); // Close the statement
            } else {
                // Insert into the database
                $stmt = $conn->prepare("INSERT INTO tb_gsl25 (surname, firstname, username, mobile, password, security_question_id, security_answer) VALUES (?, ?, ?, ?, ?, ?, ?)");
                if ($stmt === false) {
                    die('Prepare failed: ' . $conn->error); // Handle error
                }
                $stmt->bind_param("sssssss", $surname, $firstname, $username, $mobile, $password, $security_question, $security_answer);

                if ($stmt->execute()) {
                    $successMessage = 'Account successfully created!';
                    header('Location: login.php'); // Redirect to login page after successful registration
                    exit();
                } else {
                    $errorMessage = 'Error occurred while registering. Please try again.';
                }
                $stmt->close(); // Close the statement
            }
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
            background: rgba(255, 255, 255, 0.8);
        }
        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 1rem; /* Center the icon within the input field */
            top: 70%;
            transform: translateY(-50%);
        }
        .input-container {
            position: relative;
        }
    </style>
</head>
<body class="bg-gradient-to-tl from-green-400 to-indigo-900 min-h-screen flex items-center justify-center">
    <div class="h-full bg-gradient-to-tl from-green-400 to-indigo-900 w-full py-16 px-4">
        <div class="flex justify-center mb-6">
            <a href="index.html" class="logo-link"><img src="img/GSL25_transparent 2.png" alt="logo" class="h-25"></a>
        </div>
        <div id="form-container" class="max-w-md mx-auto bg-gray-200 p-8 border border-gray-300 rounded-lg shadow-lg form-container">
            <h1 class="text-2xl font-extrabold leading-6 text-gray-800 mb-4 text-center">Sign Up</h1>

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

            <form id="signup-form" method="POST" action="signup.php">
                <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2">
                    <div class="relative mb-4">
                        <label for="surname" class="block text-gray-700 font-bold mb-2">Surname:</label>
                        <input type="text" id="surname" name="surname" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    <div class="relative mb-4">
                        <label for="firstname" class="block text-gray-700 font-bold mb-2">First Name:</label>
                        <input type="text" id="firstname" name="firstname" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 mb-4">
                    <div class="relative mb-4">
                        <label for="username" class="block text-gray-700 font-bold mb-2">Username:</label>
                        <input type="text" id="username" name="username" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    <div class="relative mb-4">
                        <label for="mobile" class="block text-gray-700 font-bold mb-2">Mobile Number:</label>
                        <input type="text" id="mobile" name="mobile" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2">
                    <div class="relative input-container mb-4">
                        <label for="password" class="block text-gray-700 font-bold mb-2">Password:</label>
                        <input type="password" id="password" name="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline pr-12" required>
                        <button type="button" id="toggle-password" class="password-toggle">
                            <i class="fa fa-eye" id="password-icon"></i>
                        </button>
                    </div>
                    <div class="relative input-container mb-4">
                        <label for="confirm_password" class="block text-gray-700 font-bold mb-2">Confirm Password:</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline pr-12" required>
                        <button type="button" id="toggle-confirm-password" class="password-toggle">
                            <i class="fa fa-eye" id="confirm-password-icon"></i>
                        </button>
                    </div>
                </div>
                <div class="relative mb-4">
                    <label for="security_question" class="block text-gray-700 font-bold mb-2">Security Question:</label>
                    <select id="security_question" name="security_question" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        <option value="" disabled selected>Select a question</option>
                        <?php foreach ($questions as $question): ?>
                            <option value="<?php echo $question['id']; ?>"><?php echo htmlspecialchars($question['question']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="relative mb-4">
                    <label for="security_answer" class="block text-gray-700 font-bold mb-2">Security Answer:</label>
                    <input type="text" id="security_answer" name="security_answer" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
               
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Sign Up</button>
            </form>
            <p class="mt-4 text-center text-gray-600">Already have an account? <a href="login.php" class="text-green-500 hover:text-green-700 font-bold">Log in here</a>.</p>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>
</body>
</html>
