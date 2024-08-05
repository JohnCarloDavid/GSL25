<!-- forgot_password.php -->
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
    $username = $_POST['username'];
    $security_question = $_POST['security_question'];
    $security_answer = $_POST['security_answer'];

    // Check if security answer is correct
    $stmt = $conn->prepare("SELECT id FROM tb_gsl25 WHERE username = ? AND security_question_id = ? AND security_answer = ?");
    if ($stmt === false) {
        die('Prepare failed: ' . $conn->error); // Handle error
    }
    $stmt->bind_param("sss", $username, $security_question, $security_answer);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows === 1) {
        // Redirect to password reset page
        header('Location: reset_password.php?username=' . urlencode($username));
        exit();
    } else {
        $errorMessage = 'Security answer is incorrect.';
    }
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
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
            <h1 class="focus:outline-none text-2xl font-extrabold leading-6 text-gray-800 mb-4 text-center">Forgot Password</h1>

            <?php if (!empty($errorMessage)): ?>
                <div id="error-message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold"><?php echo $errorMessage; ?></strong>
                </div>
            <?php endif; ?>

            <form id="forgot-password-form" method="POST" action="forgot_password.php">
                <div class="mb-4">
                    <label for="username" class="block text-gray-700 font-bold mb-2">Username:</label>
                    <input type="text" id="username" name="username" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label for="security_question" class="block text-gray-700 font-bold mb-2">Security Question:</label>
                    <select id="security_question" name="security_question" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        <option value="" disabled selected>Select a question</option>
                        <?php foreach ($questions as $question): ?>
                            <option value="<?php echo $question['id']; ?>"><?php echo htmlspecialchars($question['question']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="security_answer" class="block text-gray-700 font-bold mb-2">Security Answer:</label>
                    <input type="text" id="security_answer" name="security_answer" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Submit</button>
            </form>
            <p class="mt-4 text-center text-gray-600">Remembered your password? <a href="login.php" class="text-green-500 hover:text-green-700 font-bold">Log in here</a>.</p>
        </div>
    </div>
</body>
</html>
