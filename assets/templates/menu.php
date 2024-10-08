<?php 
// Vérifier si l'utilisateur est administrateur
$isAdmin = $_SESSION['is_admin'] ?? 1; 

// Si l'utilisateur n'est pas admin, ne pas afficher le menu
if ($isAdmin):
?>
<header>
    <div class="menu-icon" id="menu-icon">
        &#9776;
    </div>
    <nav>
        <a href="admin">Administration</a> 
    </nav>
</header>
<?php endif; ?>
<script src="./assets/js/menu.js"></script>