<?php
session_start();

$connexion = new mysqli("localhost", "tvidal", "dbmotdepasse2024", "tvidal_vidaletfils");

if ($connexion->connect_error) {
    die("Échec de la connexion : " . $connexion->connect_error);
}

$loginError = '';

if (isset($_POST['login'])) {
    $email = $connexion->real_escape_string($_POST['email']);
    $motdepasse = $connexion->real_escape_string($_POST['motdepasse']);

    $requete = "SELECT * FROM utilisateur WHERE email='$email' AND motdepasse='$motdepasse'";
    $resultat = $connexion->query($requete);

    if ($resultat->num_rows > 0) {
        $_SESSION['email'] = $email;
        header("Location: espace_client.php"); 
        exit();
    } else {
        $loginError = "Identifiants incorrects.";
    }
}

if (isset($_POST['signup'])) {
    $nom = $connexion->real_escape_string($_POST['nom']);
    $prenom = $connexion->real_escape_string($_POST['prenom']);
    $email = $connexion->real_escape_string($_POST['email']);
    $motdepasse = $connexion->real_escape_string($_POST['motdepasse']);

    $requete = "INSERT INTO utilisateur (nom, prenom, email, motdepasse) VALUES ('$nom', '$prenom', '$email', '$motdepasse')";
    if ($connexion->query($requete) === TRUE) {
        echo "<p>Compte créé avec succès !</p>";
    } else {
        echo "<p>Erreur lors de la création du compte : " . $connexion->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="style_profil.css">
    <link rel="icon" href="img/icone.png" />
    <style>
        form label {
            display: block;
            margin-top: 10px;
        }

        form input {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="index.php"><img src="img/logo.png" alt="Accueil" class="logo"></a>
            <a href="mentions_legales.html" class="infos">À propos</a>
            <a href="profil.php"><img src="img/profile.png" alt="Profil" class="profile"></a>
        </nav>
    </header>
    <main>
        <section class="presentation">
            <div class="container">
                <h2>Connexion</h2>
                <form action="profil.php" method="POST">
                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" required>
                    <label for="motdepasse">Mot de passe :</label>
                    <input type="password" id="motdepasse" name="motdepasse" required><br>
                    <button type="submit" name="login">Se connecter</button>
                </form>
                <p>Pas de compte ? <a href="#" id="signup-link">Créer un compte</a></p>
                <?php if ($loginError) : ?>
                    <p class="error-message"><?php echo $loginError; ?></p>
                <?php endif; ?>
            </div>

            <div id="signup-popup" class="popup" style="display: none;">
                <div class="popup-content">
                    <span class="close" style="cursor: pointer">&times;</span>
                    <h2>Créer un compte</h2>
                    <form action="profil.php" method="POST">
                        <label for="nom">Nom :</label>
                        <input type="text" id="nom" name="nom" required>
                        <label for="prenom">Prénom :</label>
                        <input type="text" id="prenom" name="prenom" required>
                        <label for="email-signup">Email :</label>
                        <input type="email" id="email-signup" name="email" required>
                        <label for="motdepasse-signup">Mot de passe :</label>
                        <input type="password" id="motdepasse-signup" name="motdepasse" required><br>
                        <button type="submit" name="signup">S'inscrire</button>
                    </form>
                </div>
            </div>
        </section>
    </main>
    <footer style="position: fixed; bottom: 0">
        <p>&copy; 2024 Vidal et Fils</p>
    </footer>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var signupLink = document.getElementById("signup-link");
            var popup = document.getElementById("signup-popup");
            var close = document.getElementsByClassName("close")[0];

            signupLink.onclick = function(event) {
                event.preventDefault();
                popup.style.display = "block";
            }

            close.onclick = function() {
                popup.style.display = "none";
            }

            window.onclick = function(event) {
                if (event.target == popup) {
                    popup.style.display = "none";
                }
            }
        });
    </script>
</body>
</html>
