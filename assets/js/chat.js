document.getElementById('chatForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent page reload

    const userInput = document.getElementById('userInput').value;

    // Append user message to the chat interface
    appendUserMessage(userInput);

    // Send the message to the backend via fetch
    sendMessageToBackend(userInput, 'chat');
});

function appendUserMessage(message) {
    const messagesDiv = document.getElementById('messages');
    const userMessageDiv = document.createElement('div');
    userMessageDiv.classList.add('user-message');
    userMessageDiv.innerText = message;
    messagesDiv.appendChild(userMessageDiv);
}

function appendBotMessage(message) {
    const messagesDiv = document.getElementById('messages');
    const botMessageDiv = document.createElement('div');
    botMessageDiv.classList.add('bot-message');
    botMessageDiv.innerHTML = message; // Use innerHTML to insert clickable links
    messagesDiv.appendChild(botMessageDiv);

    // Scroll to the bottom of the chatbox
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
}

function addLinkClickListeners(selector, requestType) {
    const links = document.querySelectorAll(selector);
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent the default anchor behavior

            const name = this.getAttribute(`data-${requestType}`);
            sendMessageToBackend(name, requestType);
        });
    });
}

function sendMessageToBackend(message, requestType) {
    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            message: message,
            request_type: requestType // Specify the type of request
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json(); // Attempt to parse it
    })
    .then(data => {
        console.log("Parsed JSON response:", data);
        appendBotMessage(data.response);
    })
    .catch(error => {
        console.error("Error during fetch:", error);
    });
}

// Initialize click listeners for links
addLinkClickListeners('.appel-link', 'appel');
addLinkClickListeners('.demande-link', 'demande');
addLinkClickListeners('.incident-link', 'incident');