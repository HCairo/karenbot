<?php
namespace Views;

class AdminView {
    // Afficher le formulaire de modification d'utilisateur
    public function renderEditForm($user) {
        ?>
        <div class="container">
            <h1 class="main-title">Administration</h1>
            <h2 class="sub-title">Modifier l'utilisateur</h2>

            <form id="editUserForm" action="?action=admin&admin_action=edit&id=<?= htmlspecialchars($user['id']); ?>" method="post" class="form-container">
                <label for="firstname">Prénom :</label>
                <input type="text" name="firstname" id="firstname" value="<?= htmlspecialchars($user['firstname']); ?>" required>

                <label for="lastname">Nom :</label>
                <input type="text" name="lastname" id="lastname" value="<?= htmlspecialchars($user['lastname']); ?>" required>

                <label for="mail">Mail :</label>
                <input type="email" name="mail" id="mail" value="<?= htmlspecialchars($user['mail']); ?>" required>

                <label for="pswd">Mot de passe :</label>
                <input type="password" name="pswd" id="pswd">
                <small>Le mot de passe doit contenir au moins 12 caractères, un chiffre et une lettre majuscule.</small>

                <label for="level_id">Niveau :</label>
                <select name="level_id" id="level_id" required>
                    <option value="1" <?= ($user['level_id'] == 1) ? 'selected' : ''; ?>>Technicien niveau 0-1</option>
                    <option value="2" <?= ($user['level_id'] == 2) ? 'selected' : ''; ?>>Technicien niveau 2</option>
                </select>

                <label for="is_admin">Role :</label>
                <select name="is_admin" id="is_admin" required>
                    <option value="0" <?= ($user['is_admin'] == 0) ? 'selected' : ''; ?>>Invite</option>
                    <option value="1" <?= ($user['is_admin'] == 1) ? 'selected' : ''; ?>>Admin</option>
                </select>

                <button type="button" id="submitButton" class="submit-button">Modifier</button>
            </form>

            <div id="message"></div>
        </div>


        <script src="assets/js/admin.js"></script>
        <?php
    }
}
?>
