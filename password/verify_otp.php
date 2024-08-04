<!-- verify_otp.php -->
<?php
session_start();
include 'config.php'; // Ensure you have a config.php with database connection setup

$otpErrorMessage = "";
$otpSuccessMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['otp'], $_POST['email'])) {
        $email = $conn->real_escape_string($_POST['email']);
        $otp = $conn->real_escape_string($_POST['otp']);
        
        // Verify OTP
        $sql = "SELECT otp_code FROM tb_gsl25 WHERE user_email = ? AND otp_status = 'pending'";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($conn->error) . '<br> SQL: ' . $sql);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($stored_otp);
        $stmt->fetch();

        if ($otp == $stored_otp) {
            // OTP is correct, allow the user to set a new password
            $_SESSION['email'] = $email;
            header("Location: reset_password.php");
            exit();
        } else {
            $otpErrorMessage = "Invalid OTP.";
        }

        $stmt->close();
    } else {
        $otpErrorMessage = "OTP and email are required.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gradient-to-tl from-green-400 to-indigo-900 min-h-screen flex items-center justify-center">
    <div class="max-w-md mx-auto bg-gray-200 p-8 border border-gray-300 rounded-lg shadow-lg">
        <h1 class="text-2xl font-extrabold leading-6 text-gray-800 mb-4 text-center">Verify OTP</h1>

        <?php if (!empty($otpErrorMessage)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold"><?php echo $otpErrorMessage; ?></strong>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-4">
                <label for="otp" class="block text-gray-700 font-bold mb-2">OTP:</label>
                <input type="text" id="otp" name="otp" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email']); ?>">
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Verify OTP</button>
            </div>
        </form>
    </div>
</body>
</html>
