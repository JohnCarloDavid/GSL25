<!-- request_otp.php -->
<?php
session_start();
include 'config.php'; // Ensure you have a config.php with database connection setup

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

$successMessage = "";
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email'])) {
        $email = $conn->real_escape_string($_POST['email']);

        // Check if the email exists in the database
        $sql = "SELECT user_email FROM tb_gsl25 WHERE user_email = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($conn->error) . '<br> SQL: ' . $sql);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            // Generate OTP code
            $otp_code = rand(100000, 999999);

            // Save OTP code to the database with status 'pending'
            $sql = "UPDATE tb_gsl25 SET otp_code = ?, otp_status = 'pending' WHERE user_email = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                die('Prepare failed: ' . htmlspecialchars($conn->error) . '<br> SQL: ' . $sql);
            }

            $stmt->bind_param("ss", $otp_code, $email);
            if (!$stmt->execute()) {
                die('Execute failed: ' . htmlspecialchars($stmt->error));
            }

            // Send OTP code to the user's email
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'johncarlodavid019@gmail.com'; // Your Gmail address
                $mail->Password   = 'jbsk harg upeb immf'; // Your Gmail App Password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('johncarlodavid019@gmail.com', 'Your Name');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Your OTP Code';
                $mail->Body    = "Your OTP code is: <b>$otp_code</b>";

                $mail->send();
                $successMessage = "OTP has been sent to your email.";

                // Redirect to OTP verification page
                header("Location: verify_otp.php?email=" . urlencode($email));
                exit();
            } catch (Exception $e) {
                $errorMessage = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            $errorMessage = "Email does not exist.";
        }

        $stmt->close();
    } else {
        $errorMessage = "Email is required.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request OTP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gradient-to-tl from-green-400 to-indigo-900 min-h-screen flex items-center justify-center">
    <div class="max-w-md mx-auto bg-gray-200 p-8 border border-gray-300 rounded-lg shadow-lg">
        <h1 class="text-2xl font-extrabold leading-6 text-gray-800 mb-4 text-center">Request OTP</h1>

        <?php if (!empty($successMessage)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold"><?php echo $successMessage; ?></strong>
            </div>
        <?php endif; ?>

        <?php if (!empty($errorMessage)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold"><?php echo $errorMessage; ?></strong>
            </div>
        <?php endif; ?>

        <form method="POST" action="request_otp.php">
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-bold mb-2">Email:</label>
                <input type="email" id="email" name="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Send OTP</button>
            </div>
        </form>
    </div>
</body>
</html>
