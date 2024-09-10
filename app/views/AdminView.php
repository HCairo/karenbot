<?php
namespace Views;

class AdminView {
    // Afficher la liste des utilisateurs
    public function renderUserList($users) {
        ?>
        <h1>Administration</h1>
        <h2>Gestion des utilisateurs</h2>
        <a href="?action=admin&admin_action=create">Créer un nouvel utilisateur</a>

        <table border="1">
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
                            <a href="?action=admin&admin_action=edit&id=<?= htmlspecialchars($user['id'] ?? ''); ?>">Modifier</a> |
                            <a href="?action=admin&admin_action=delete&id=<?= htmlspecialchars($user['id'] ?? ''); ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php
    }

    // Afficher le formulaire de création d'utilisateur
    public function renderCreateForm() {
        ?>
        <h1>Administration</h1>
        <h2>Créer un nouvel utilisateur</h2>

        <form action="?action=admin&admin_action=create" method="post">
            <label for="firstname">Prénom :</label>
            <input type="text" name="firstname" id="firstname" required><br>

            <label for="lastname">Nom :</label>
            <input type="text" name="lastname" id="lastname" required><br>

            <label for="mail">Mail :</label>
            <input type="email" name="mail" id="mail" required><br>

            <label for="pswd">Mot de passe :</label>
            <input type="password" name="pswd" id="pswd" required><br>

            <label for="level_id">Niveau</label>
            <select name="level_id" id="level_id" required>
                <option value="1">Technicien niveau 0-1</option>
                <option value="2">Technicien niveau 2</option>
            </select><br>

            <label for="is_admin">Role</label>
            <select name="is_admin" id="is_admin" required>
                <option value="0">Invite</option>
                <option value="1">Admin</option>
            </select><br>

            <button type="submit">Créer</button>
        </form>
        <?php
    }

    // Afficher le formulaire de modification d'utilisateur
    public function renderEditForm($user) {
        ?>
        <h1>Administration</h1>
        <h2>Modifier l'utilisateur</h2>

        <form action="?action=admin&admin_action=edit&id=<?= htmlspecialchars($user['id']); ?>" method="post">
            <label for="firstname">Prénom :</label>
            <input type="text" name="firstname" id="firstname" value="<?= htmlspecialchars($user['firstname']); ?>" required><br>

            <label for="lastname">Nom :</label>
            <input type="text" name="lastname" id="lastname" value="<?= htmlspecialchars($user['lastname']); ?>" required><br>

            <label for="mail">Mail :</label>
            <input type="email" name="mail" id="mail" value="<?= htmlspecialchars($user['mail']); ?>" required><br>

            <label for="pswd">Mot de passe :</label>
            <input type="password" name="pswd" id="pswd" placeholder="Laisser vide pour ne pas changer"><br>

            <label for="level_id">Niveau :</label>
            <select name="level_id" id="level_id" required>
                <option value="1" <?= ($user['level_id'] == 1) ? 'selected' : ''; ?>>Technicien niveau 0-1</option>
                <option value="2" <?= ($user['level_id'] == 2) ? 'selected' : ''; ?>>Technicien niveau 2</option>
            </select><br>

            <label for="is_admin">Role :</label>
            <select name="is_admin" id="is_admin" required>
                <option value="1" <?= ($user['is_admin'] == 0) ? 'selected' : ''; ?>>Invite</option>
                <option value="2" <?= ($user['is_admin'] == 1) ? 'selected' : ''; ?>>Admin</option>
            </select><br>

            <button type="submit">Modifier</button>
        </form>
        <?php
    }
}
?>
