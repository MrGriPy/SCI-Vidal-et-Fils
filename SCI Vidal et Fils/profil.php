<?php
session_start();

$connexion = new mysqli("localhost", "tvidal", "dbmotdepasse2024", "tvidal_vidaletfils");

if ($connexion->connect_error) {
    die("Échec de la connexion : " . $connexion->connect_error);
}

if (isset($_POST['login'])) {
    $email = $connexion->real_escape_string($_POST['email']);
    $motdepasse = $connexion->real_escape_string($_POST['motdepasse']);

    $loginEmailValue = $email;
    $loginPasswordValue = $motdepasse;

    $requete = "SELECT * FROM utilisateur WHERE email='$email' AND motdepasse='$motdepasse'";
    $resultat = $connexion->query($requete);

    if ($resultat->num_rows > 0) {
        $_SESSION['email'] = $email;
        header("Location: espace_client.php");
        exit();
    } else {
        $loginEmailError = "Email incorrect.<br><br>";
        $loginPasswordError = "Mot de passe incorrect.<br><br>";
    }
}

if (isset($_POST['signup'])) {
    $nom = $connexion->real_escape_string($_POST['nom']);
    $prenom = $connexion->real_escape_string($_POST['prenom']);
    $email = $connexion->real_escape_string($_POST['email']);
    $motdepasse = $connexion->real_escape_string($_POST['motdepasse']);

    $registerNomValue = $nom;
    $registerPrenomValue = $prenom;
    $registerEmailValue = $email;
    $registerPasswordValue = $motdepasse;

    if (strlen($motdepasse) < 8) {
        $registerPasswordError = "Le mot de passe doit comporter au moins 8 caractères.<br><br>";
    } else {
        $requete_verification = "SELECT * FROM utilisateur WHERE email='$email'";
        $resultat_verification = $connexion->query($requete_verification);

        if ($resultat_verification->num_rows > 0) {
            $requete_mise_a_jour = "UPDATE utilisateur SET nom='$nom', prenom='$prenom', motdepasse='$motdepasse' WHERE email='$email'";
            if ($connexion->query($requete_mise_a_jour) === TRUE) {
                $updateSuccess = "<p>Compte mis à jour avec succès !</p>";
            } else {
                $updateError = "<p>Erreur lors de la mise à jour du compte : " . $connexion->error . "</p>";
            }
        } else {
            $requete_insertion = "INSERT INTO utilisateur (nom, prenom, email, motdepasse) VALUES ('$nom', '$prenom', '$email', '$motdepasse')";
            if ($connexion->query($requete_insertion) === TRUE) {
                $registerSuccess = "<p>Compte créé avec succès !</p>";
            } else {
                $registerError = "<p>Erreur lors de la création du compte : " . $connexion->error . "</p>";
                $registerEmailError = "Email déjà utilisé.";
            }
        }
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
    <link rel="script" href="script.js">
    <link rel="icon" href="img/icone.png" />
    <style>
        form label {
            display: block;
            margin-top: 10px;
        }

        form input {
            margin-bottom: 10px;
        }

        .error-message {
            color: red;
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
                    <label for="email">*Email :</label>
                    <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($loginEmailValue); ?>">
                    <?php if ($loginEmailError) : ?>
                        <p class="error-message"><?php echo $loginEmailError; ?></p>
                    <?php endif; ?>

                    <label for="motdepasse">*Mot de passe :</label>
                    <input type="password" id="motdepasse" name="motdepasse" required value="<?php echo htmlspecialchars($loginPasswordValue); ?>">
                    <input type="checkbox" id="showPassword" onclick="togglePassword()"> Afficher le mot de passe<br>
                    <?php if ($loginPasswordError) : ?>
                        <p class="error-message"><?php echo $loginPasswordError; ?></p>
                    <?php endif; ?>

                    <button type="submit" name="login">Se connecter</button>
                </form>
                <p>Pas de compte ? <a href="#" id="signup-link" style="text-decoration: underline">Créer un compte</a></p>
                <?php if ($loginError) : ?>
                <?php endif; ?>
                <?php if (isset($updateSuccess)) : ?>
                    <p><?php echo $updateSuccess; ?></p>
                <?php elseif (isset($updateError)) : ?>
                    <p class="error-message"><?php echo $updateError; ?></p>
                <?php endif; ?>
                <?php if (isset($registerSuccess)) : ?>
                    <p><?php echo $registerSuccess; ?></p>
                <?php elseif (isset($registerError)) : ?>
                    <p class="error-message"><?php echo $registerError; ?></p>
                    <?php if ($registerEmailError) : ?>
                        <p class="error-message"><?php echo $registerEmailError; ?></p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div id="signup-popup" class="popup" style="display: none;">
                <div class="popup-content">
                    <span class="close" style="cursor: pointer">&times;</span>
                    <h2>Créer un compte</h2>
                    <form action="profil.php" method="POST">
                        <label for="nom">Nom :</label>
                        <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($registerNomValue); ?>">

                        <label for="prenom">Prénom :</label>
                        <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($registerPrenomValue); ?>">

                        <label for="email-signup">*Email :</label>
                        <input type="email" id="email-signup" name="email" required value="<?php echo htmlspecialchars($registerEmailValue); ?>">
                        <?php if ($registerEmailError) : ?>
                            <p class="error-message"><?php echo $registerEmailError; ?></p>
                        <?php endif; ?>

                        <label for="motdepasse-signup">*Mot de passe :</label>
                        <input type="password" id="motdepasse-signup" name="motdepasse" required value="<?php echo htmlspecialchars($registerPasswordValue); ?>">
                        <input type="checkbox" id="showPasswordSignup" onclick="togglePasswordSignup()"> Afficher le mot de passe<br>
                        <?php if ($registerPasswordError) : ?>
                            <p class="error-message"><?php echo $registerPasswordError; ?></p>
                        <?php endif; ?>

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

        function togglePassword() {
            var passwordField = document.getElementById("motdepasse");
            var showPasswordCheckbox = document.getElementById("showPassword");
            if (showPasswordCheckbox.checked) {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }

        function togglePasswordSignup() {
            var passwordField = document.getElementById("motdepasse-signup");
            var showPasswordCheckbox = document.getElementById("showPasswordSignup");
            if (showPasswordCheckbox.checked) {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }
    </script>
</body>
</html>
