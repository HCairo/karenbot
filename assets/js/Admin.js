
        document.getElementById('submitButton').addEventListener('click', function() {
            const form = document.getElementById('editUserForm');
            const formData = new FormData(form);
            
            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())  
            .then(data => {
                // Afficher un message de confirmation dans la div 'message'
                document.getElementById('message').innerHTML = "<p>Le mot de passe a bien été remplacé.</p>";
            })
            .catch(error => {
                // Gérer les erreurs
                document.getElementById('message').innerHTML = "<p>Une erreur est survenue lors de la mise à jour.</p>";
            });
        });

