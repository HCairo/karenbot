document.getElementById('chatForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent page reload

    const userInput = document.getElementById('userInput').value;

    // Append user message to the chat interface
    appendUserMessage(userInput);

    // Send the message to the backend via fetch
    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            message: userInput
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log("Parsed JSON response:", data);
        
        // Append the bot's response to the chat interface
        appendBotMessage(data.response);
        
        // Add event listeners to newly added incident links
        addIncidentClickListeners();
    })
    .catch(error => {
        // Handle fetch error and notify the user
        appendBotMessage('Oops! Something went wrong. Please try again later.');
        console.error("Error during fetch:", error);
    });

    // Clear the input field after submission
    document.getElementById('userInput').value = '';
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

function addIncidentClickListeners() {
    const incidentLinks = document.querySelectorAll('.incident-link');
    incidentLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const incidentName = this.getAttribute('data-incident');

            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    message: incidentName
                })
            })
            .then(response => response.json())
            .then(data => {
                appendBotMessage(data.response);
            })
            .catch(error => {
                appendBotMessage('Oops! Something went wrong. Please try again later.');
                console.error("Error during fetch:", error);
            });
        });
    });
}