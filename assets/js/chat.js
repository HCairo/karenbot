document.getElementById('chatForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent page reload

    const userInput = document.getElementById('userInput').value;

    // Append user message to the chat interface
    appendUserMessage(userInput);

    // Send the message to the backend via fetch with request_type included
    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            message: userInput,
            request_type: 'chat' // Add request type to indicate a chat message
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
        console.error("Error during fetch:", error);
    });
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

function addAppelClickListeners() {
    const appelLinks = document.querySelectorAll('.appel-link');
    appelLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent the default anchor behavior

            const appelName = this.getAttribute('data-appel');

            // Send the selected appel name to the backend
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    message: appelName,
                    request_type: 'chat'  // Specify the type of request
                })
            })
            .then(response => {
                console.log(response); // Check the response status and headers
                return response.json(); // Attempt to parse it
            })
            .then(data => {
                console.log("Parsed JSON response:", data);
                appendBotMessage(data.response);
            })
            .catch(error => {
                console.error("Error during fetch:", error);
            });            
        });
    });
}

function addIncidentClickListeners() {
    // Get all incident links
    const incidentLinks = document.querySelectorAll('.incident-link');
    incidentLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent the default anchor behavior

            const incidentName = this.getAttribute('data-incident');

            // Send the selected incident name to the backend with the proper request type
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    message: incidentName,  // Use the incidentName instead of userInput
                    request_type: 'chat'  // Specify the type of request as 'chat'
                })
            })            
            .then(response => {
                console.log(response); // Check the response status and headers
                return response.json(); // Attempt to parse it
            })
            .then(data => {
                console.log("Parsed JSON response:", data);
                appendBotMessage(data.response);
            })
            .catch(error => {
                console.error("Error during fetch:", error);
            });            
        });
    });
}