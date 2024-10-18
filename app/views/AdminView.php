<?php
namespace Views;

class AdminView {
    // Afficher la liste des utilisateurs
    public function renderUserList($users) {
        ?>
        <a href="."><==</a>
        <div class="container">
            <h1 class="main-title">Administration</h1>
            <h2 class="sub-title">Gestion des utilisateurs</h2>
            <a href="?action=admin&admin_action=create" class="create-button">Créer un nouvel utilisateur</a>

            <table class="user-table">
                <thead>
                    <tr>
                        <th>Prénom</th>
                        <th>Nom</th>
                        <th>Mail</th>
                        <th>Niveau</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user) { ?>
                        <tr>
                            <td><?= htmlspecialchars($user['firstname'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($user['lastname'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($user['mail'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($user['level_id'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($user['is_admin'] ?? ''); ?></td>
                            <td>
                                <a href="?action=admin&admin_action=edit&id=<?= htmlspecialchars($user['id'] ?? ''); ?>" class="action-link">Modifier</a> |
                                <a href="?action=admin&admin_action=delete&id=<?= htmlspecialchars($user['id'] ?? ''); ?>" class="action-link delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">Supprimer</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    // Afficher le formulaire de création d'utilisateur
    public function renderCreateForm() {
        ?>
        <div class="container">
            <h1 class="main-title">Administration</h1>
            <h2 class="sub-title">Créer un nouvel utilisateur</h2>

            <form action="?action=admin&admin_action=create" method="post" class="form-container">
                <label for="firstname">Prénom :</label>
                <input type="text" name="firstname" id="firstname" required>

                <label for="lastname">Nom :</label>
                <input type="text" name="lastname" id="lastname" required>

                <label for="mail">Mail :</label>
                <input type="email" name="mail" id="mail" required>

                <label for="pswd">Mot de passe :</label>
                <input type="password" name="pswd" id="pswd" required>

                <label for="level_id">Niveau</label>
                <select name="level_id" id="level_id" required>
                    <option value="1">Technicien niveau 0-1</option>
                    <option value="2">Technicien niveau 2</option>
                </select>

                <label for="is_admin">Role</label>
                <select name="is_admin" id="is_admin" required>
                    <option value="0">Invite</option>
                    <option value="1">Admin</option>
                </select>

                <button type="submit" class="submit-button">Créer</button>
            </form>
        </div>
        <?php
    }

    // Afficher le formulaire de modification d'utilisateur
    public function renderEditForm($user) {
        ?>
        <div class="container">
            <h1 class="main-title">Administration</h1>
            <h2 class="sub-title">Modifier l'utilisateur</h2>

            <form action="?action=admin&admin_action=edit&id=<?= htmlspecialchars($user['id']); ?>" method="post" class="form-container">
                <label for="firstname">Prénom :</label>
                <input type="text" name="firstname" id="firstname" value="<?= htmlspecialchars($user['firstname']); ?>" required>

                <label for="lastname">Nom :</label>
                <input type="text" name="lastname" id="lastname" value="<?= htmlspecialchars($user['lastname']); ?>" required>

                <label for="mail">Mail :</label>
                <input type="email" name="mail" id="mail" value="<?= htmlspecialchars($user['mail']); ?>" required>

                <label for="pswd">Mot de passe :</label>
                <input type="password" name="pswd" id="pswd" placeholder="Laisser vide pour ne pas changer">

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

                <button type="submit" class="submit-button">Modifier</button>
            </form>
        </div>
        <?php
    }
}
?>
