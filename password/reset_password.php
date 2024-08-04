<?php
session_start();
include 'config.php'; // Ensure you have a config.php with database connection setup

$resetSuccessMessage = "";
$resetErrorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['new_password'], $_POST['confirm_password'])) {
        $email = $_SESSION['email'];
        $new_password = $conn->real_escape_string($_POST['new_password']);
        $confirm_password = $conn->real_escape_string($_POST['confirm_password']);

        // Validate new password and confirm password
        if (strlen($new_password) < 6) {
            $resetErrorMessage = "Password must be at least 6 characters long.";
        } elseif ($new_password !== $confirm_password) {
            $resetErrorMessage = "Passwords do not match.";
        } else {
            // Update password (plain text)
            $sql = "UPDATE tb_gsl25 SET password = ?, otp_code = NULL, otp_status = 'used' WHERE user_email = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                die('Prepare failed: ' . htmlspecialchars($conn->error) . '<br> SQL: ' . $sql);
            }

            $stmt->bind_param("ss", $new_password, $email);
            if ($stmt->execute()) {
                $resetSuccessMessage = "Password has been reset successfully.";
                unset($_SESSION['email']); // Clear the session email
                $stmt->close();
                
                // Redirect to login page
                header("Location: login.php");
                exit();
            } else {
                $resetErrorMessage = "Failed to reset password.";
            }

            $stmt->close();
        }
    } else {
        $resetErrorMessage = "New password and confirm password are required.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gradient-to-tl from-green-400 to-indigo-900 min-h-screen flex items-center justify-center">
    <div class="max-w-md mx-auto bg-gray-200 p-8 border border-gray-300 rounded-lg shadow-lg">
        <h1 class="text-2xl font-extrabold leading-6 text-gray-800 mb-4 text-center">Reset Password</h1>

        <?php if (!empty($resetSuccessMessage)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold"><?php echo $resetSuccessMessage; ?></strong>
            </div>
        <?php endif; ?>

        <?php if (!empty($resetErrorMessage)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold"><?php echo $resetErrorMessage; ?></strong>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-4">
                <label for="new_password" class="block text-gray-700 font-bold mb-2">New Password:</label>
                <input type="password" id="new_password" name="new_password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-4">
                <label for="confirm_password" class="block text-gray-700 font-bold mb-2">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Update Password</button>
            </div>
        </form>
    </div>
</body>
</html>
