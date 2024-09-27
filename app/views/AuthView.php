<?php
namespace Views;

class AuthView {
    public function render() {
        ?>
        <h1>Connexion</h1>

        <form action="login" method="post">
            <label for="mail">Adresse mail</label>
            <input type="text" name="mail" id="mail" required>

            <label for="pswd">Mot de passe</label>
            <input type="password" name="pswd" id="pswd" required>

            <button type="submit">Se connecter</button>
        </form>
        <?php
    }
}
?>
