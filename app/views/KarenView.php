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

        <!-- Le fichier JS pour gérer le chat -->
        <script src="./assets/js/chat.js"></script>
        <?php
    }
}