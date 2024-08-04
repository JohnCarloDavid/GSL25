<?php
// save_message.php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = $_POST['message'];
    
    // Validate and sanitize the message
    $message = htmlspecialchars(trim($message));
    
    // Default response and choices
    $response = 'Thank you for your message! We will get back to you soon.';
    $choices = [];
    
    if (stripos($message, 'help') !== false) {
        $response = 'I can help with the following:';
        $choices = [
            ['text' => 'Product Information', 'value' => 'product_info'],
            ['text' => 'Pricing Details', 'value' => 'pricing'],
            ['text' => 'Order Placement', 'value' => 'order_placement']
        ];
    } elseif (stripos($message, 'product_info') !== false) {
        $response = 'We offer various types of steel products. What specifically are you looking for?';
    } elseif (stripos($message, 'pricing') !== false) {
        $response = 'Our prices are competitive. Please specify the product you are interested in for exact pricing.';
    } elseif (stripos($message, 'order_placement') !== false) {
        $response = 'To place an order, please let us know the products and quantities you need.';
    }
    
    echo json_encode(['response' => $response, 'choices' => $choices]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GSL25 Steel Trading Chatbot</title>
    <style>
        /* Improved styling for the chatbot */
        #chatbox {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 350px;
            height: 450px;
            border: 1px solid #ccc;
            border-radius: 10px;
            overflow: hidden;
            background-color: #f9f9f9;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        #chatbox-header {
            background-color: #007BFF;
            color: white;
            padding: 10px;
            text-align: center;
            font-size: 18px;
        }
        #chatbox-body {
            height: calc(100% - 70px);
            padding: 10px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }
        #chatbox-footer {
            padding: 10px;
            border-top: 1px solid #ccc;
            background-color: #f1f1f1;
        }
        #message-input {
            width: calc(100% - 60px);
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        #send-button {
            width: 50px;
            height: 40px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .message {
            margin-bottom: 10px;
            padding: 5px;
            border-radius: 5px;
            max-width: 80%;
            clear: both;
        }
        .user-message {
            background-color: #e1ffc7;
            align-self: flex-end;
        }
        .bot-message {
            background-color: #d1e7dd;
            align-self: flex-start;
        }
        .message-time {
            font-size: 0.8em;
            color: #888;
            margin-top: 5px;
        }
        .choice-button {
            display: block;
            margin: 5px 0;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .choice-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div id="chatbox">
    <div id="chatbox-header">Chat with Us!</div>
    <div id="chatbox-body">
        <div class="message bot-message">Hello! How can we assist you today?</div>
        <div id="bot-choices" style="display:none;"></div>
    </div>
    <div id="chatbox-footer">
        <input type="text" id="message-input" placeholder="Type your message here...">
        <button id="send-button">Send</button>
    </div>
</div>

<script>
    function getCurrentTime() {
        const now = new Date();
        return now.toLocaleTimeString();
    }

    function createChoiceButtons(choices) {
        const container = document.getElementById('bot-choices');
        container.innerHTML = '';
        choices.forEach(choice => {
            const button = document.createElement('button');
            button.className = 'choice-button';
            button.textContent = choice.text;
            button.onclick = () => sendMessage(choice.value);
            container.appendChild(button);
        });
        container.style.display = 'block';
    }

    function sendMessage(message) {
        const chatboxBody = document.getElementById('chatbox-body');
        const userMessage = document.createElement('div');
        userMessage.className = 'message user-message';
        userMessage.innerHTML = `<div>${message}</div><div class="message-time">${getCurrentTime()}</div>`;
        chatboxBody.appendChild(userMessage);
        
        // Hide choices and clear input
        document.getElementById('bot-choices').style.display = 'none';
        document.getElementById('message-input').value = '';

        // Send message to server
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'save_message.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                var botMessage = document.createElement('div');
                botMessage.className = 'message bot-message';
                botMessage.innerHTML = `<div>${response.response}</div><div class="message-time">${getCurrentTime()}</div>`;
                chatboxBody.appendChild(botMessage);
                chatboxBody.scrollTop = chatboxBody.scrollHeight;

                if (response.choices) {
                    createChoiceButtons(response.choices);
                }
            }
        };
        xhr.send('message=' + encodeURIComponent(message));
    }

    document.getElementById('send-button').addEventListener('click', function() {
        var input = document.getElementById('message-input');
        var message = input.value;
        if (message.trim() !== '') {
            sendMessage(message);
        }
    });
</script>

</body>
</html>
