<?php
session_start();

$connexion = new mysqli("localhost", "tvidal", "dbmotdepasse2024", "tvidal_vidaletfils");

if ($connexion->connect_error) {
    die("Échec de la connexion : " . $connexion->connect_error);
}

if (!isset($_SESSION['email'])) {
    header("Location: profil.php");
    exit();
}

$email = $_SESSION['email'];
$email = $connexion->real_escape_string($email);
$requete = "SELECT * FROM utilisateur WHERE email='$email'";
$resultat = $connexion->query($requete);
$utilisateur = $resultat->fetch_assoc();
$user_id = $utilisateur['id_utilisateur'];

$requeteReservations = "SELECT * FROM reservation WHERE id_utilisateur='$user_id'";
$resultatReservations = $connexion->query($requeteReservations);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Client</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="img/icone.png" />
</head>
<body>
    <header>
        <nav>
            <a href="index.php"><img src="img/logo.png" alt="Accueil" class="logo"></a>
            <a href="mentions_legales.html" class="infos">À propos</a>
            <?php if (isset($_SESSION['email'])): ?>
                <a href="espace_client.php"><img src="img/profile.png" alt="Profil" class="profile"></a>
            <?php else: ?>
                <a href="profil.php"><img src="img/profile.png" alt="Profil" class="profile"></a>
            <?php endif; ?>
        </nav>
    </header>
    <main>
        <section class="presentation">
            <h2>Bienvenue <?php echo htmlspecialchars($utilisateur['prenom'] . ' ' . $utilisateur['nom']); ?></h2>
            <br>
            <h3>Vos Réservations</h3>
            <?php
            if ($resultatReservations->num_rows > 0) {
                while ($row = $resultatReservations->fetch_assoc()) {
                    echo "Logement réservé : " . htmlspecialchars($row['nom_logement']) . "<br>";
                    echo "Date de Début : " . htmlspecialchars($row['date_debut']) . "<br>";
                    echo "Date de Fin : " . htmlspecialchars($row['date_fin']) . "<br><br>";
                }
            } else {
                echo "Aucune réservation trouvée.<br><br>";
            }
            ?>
            <button onclick="window.location.href='index.php'">Se déconnecter</button>
        </section>
    </main>
    <footer style="margin-top: 191px;">
        <p>&copy; 2024 Vidal et Fils</p>
    </footer>
</body>
</html>
