<?php
namespace Views;

class KarenView {

    public function render() {
        ?>
            <div id="chatbox">
                <div id="messages"></div>
                <form id="chatForm">
                    <input type="text" name="message" id="userInput" placeholder="Tapez votre message ici..." required>
                    <button type="submit">Envoyer</button>
                </form>
            </div>

            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    margin: 0;
                    padding: 0;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                }
                #chatbox {
                    width: 400px;
                    background-color: #fff;
                    border: 1px solid #ccc;
                    border-radius: 8px;
                    padding: 20px;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                }
                #messages {
                    height: 300px;
                    overflow-y: auto;
                    border: 1px solid #ccc;
                    padding: 10px;
                    border-radius: 8px;
                    background-color: #eaeaea;
                    margin-bottom: 15px;
                }
                .user-message, .bot-message {
                    padding: 10px;
                    margin: 5px 0;
                    border-radius: 10px;
                }
                .user-message {
                    background-color: #d1e7dd;
                    text-align: right;
                }
                .bot-message {
                    background-color: #f8d7da;
                    text-align: left;
                }
                form {
                    display: flex;
                    justify-content: space-between;
                }
                input[type="text"] {
                    width: 80%;
                    padding: 10px;
                    border-radius: 5px;
                    border: 1px solid #ccc;
                }
                button {
                    padding: 10px 20px;
                    border: none;
                    background-color: #007bff;
                    color: white;
                    border-radius: 5px;
                    cursor: pointer;
                }
                button:hover {
                    background-color: #0056b3;
                }
            </style>

            <script>
                document.getElementById('chatForm').addEventListener('submit', function(e) {
                    e.preventDefault(); // Prevent page reload
                    
                    const userInput = document.getElementById('userInput').value;
                    
                    // Append user message to the chat
                    const messagesDiv = document.getElementById('messages');
                    const userMessageDiv = document.createElement('div');
                    userMessageDiv.classList.add('user-message');
                    userMessageDiv.innerText = userInput;
                    messagesDiv.appendChild(userMessageDiv);

                    // Clear the input field
                    document.getElementById('userInput').value = '';

                    // Send the message to the PHP backend via AJAX
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', '', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            // Parse the chatbot response
                            const botResponse = xhr.responseText;
                            
                            // Append chatbot message to the chat
                            const botMessageDiv = document.createElement('div');
                            botMessageDiv.classList.add('bot-message');
                            botMessageDiv.innerText = botResponse;
                            messagesDiv.appendChild(botMessageDiv);
                            
                            // Scroll to the bottom of the chatbox
                            messagesDiv.scrollTop = messagesDiv.scrollHeight;
                        }
                    };
                    xhr.send('message=' + encodeURIComponent(userInput));
                });
            </script>
        </body>
        </html>
        <?php
    }
}
