<?php
session_start();

if (!isset($_SESSION['utilisateur'])) {
    header("Location: profil.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Compte</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="img/logo.PNG" />
</head>
<body>
    <header>
        <nav>
            <a href="index.php"><img src="img/logo.PNG" alt="Logo" class="logo"></a>
            <a href="mentions_legales.html" class="infos">À propos</a>
            <a href="profil.php"><img src="img/profile.png" alt="Profil" class="profile"></a>
        </nav>
    </header>
    <main>
        <section class="compte-details">
            <h2>Bienvenue, <?php echo $_SESSION['utilisateur']; ?></h2>
            <p>Ceci est votre espace compte.</p>
            <a href="logout.php">Déconnexion</a>
        </section>
    </main>
    <footer style="margin=0">
        <p>&copy; 2024 Vidal et Fils</p>
    </footer>
</body>
</html>
