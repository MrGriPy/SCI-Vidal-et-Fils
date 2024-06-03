<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="img/icone.png" />
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
        <section class="location-details" style="margin-bottom: 232px">
            <?php
            $connexion = new mysqli("localhost", "tvidal", "dbmotdepasse2024", "tvidal_vidaletfils");
            if ($connexion->connect_error) {
                die("Échec de la connexion : " . $connexion->connect_error);
            }
            $logement_id = $_GET['id'];
            $requete = "SELECT * FROM logement WHERE id = $logement_id";
            $resultat = $connexion->query($requete);

            if ($resultat->num_rows > 0) {
                $row = $resultat->fetch_assoc();
                echo '<h1>' . $row["nom"] . '</h1>';
                echo '<div class="location-images">';
                foreach(glob("img/{$row['nom']}/*.jpg") as $image) {
                    echo "<img src='$image' alt='{$row['nom']}' style='width: auto; max-height: 270px;'>";
                }
                echo '</div>';
                echo '<div class="location-info">';
                echo '<p><strong>Lieu :</strong> ' . $row["lieu"] . '</p>';
                echo '<p><strong>Prix :</strong> ' . $row["prix"] . ' € / '. $row["periode"] . '</p>';
                echo '<p><strong>Surface :</strong> ' . $row["surface"] . 'm²</p>';
                echo '<p><strong>Description :</strong> ' . $row["description"] . '</p>';
                echo '<button onclick="openModal()">Réserver</button>';
                echo '</div>';
            } else {
                echo "Aucune information disponible pour ce logement.";
            }

            $connexion->close();
            ?>
        </section>
        <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <form action="logement.php?id=<?php echo $logement_id; ?>" method="post">
                    <?php echo "Veuillez choisir vos dates en fonction de la période indiquée. <br><br>"; ?>
                    <label for="date_debut">Date de début :</label>
                    <input type="date" id="date_debut" name="date_debut" required><br><br>
                    <label for="date_fin">Date de fin :</label>
                    <input type="date" id="date_fin" name="date_fin" required><br><br>
                    <label for="email">Adresse e-mail :</label>
                    <input type="email" id="email" name="email" required><br><br>
                    <input type="hidden" name="logement_id" value="<?php echo $logement_id; ?>">
                    <input type="submit" value="Réserver">
                </form>
            </div>
        </div>
        <?php
$servername = "localhost";
$username = "tvidal";
$password = "dbmotdepasse2024";
$database = "tvidal_vidaletfils";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    $email = $_POST['email'];
    $logement_id = $_POST['logement_id'];

    $sql_get_user_id = "SELECT id_utilisateur FROM utilisateur WHERE email = ?";
    $stmt = $conn->prepare($sql_get_user_id);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_utilisateur = $row["id_utilisateur"];
    } else {
        $sql_insertion_utilisateur = "INSERT INTO utilisateur (email) VALUES (?)";
        $stmt_insertion = $conn->prepare($sql_insertion_utilisateur);
        $stmt_insertion->bind_param('s', $email);
        $stmt_insertion->execute();
        $id_utilisateur = $stmt_insertion->insert_id;
        $stmt_insertion->close();
    }
    $stmt->close();

    $sql_get_logement_name = "SELECT nom FROM logement WHERE id = ?";
    $stmt_logement = $conn->prepare($sql_get_logement_name);
    $stmt_logement->bind_param('i', $logement_id);
    $stmt_logement->execute();
    $result_logement = $stmt_logement->get_result();
    $logement = $result_logement->fetch_assoc();
    $nom_logement = $logement['nom'];
    $stmt_logement->close();

    $sql_insert_reservation = "INSERT INTO reservation (id_utilisateur, date_debut, date_fin, nom_logement) VALUES (?, ?, ?, ?)";
    $stmt_reservation = $conn->prepare($sql_insert_reservation);
    $stmt_reservation->bind_param('isss', $id_utilisateur, $date_debut, $date_fin, $nom_logement);

    if ($stmt_reservation->execute()) {
        echo '<p style="color: white; background-color: gray; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Réservation enregistrée avec succès.</p>';
        
        // Envoi de l'email de confirmation
        $destinataire = $email;
        $sujet = "Confirmation de votre réservation";
        $message = "
        <html>
        <body>
        <h1>Confirmation de votre réservation</h1>
        <p>Bonjour,</p>
        <p>Votre réservation pour le logement <strong>" . htmlspecialchars($nom_logement) . "</strong> a été confirmée.</p>
        <p>Dates : du <strong>" . htmlspecialchars($date_debut) . "</strong> au <strong>" . htmlspecialchars($date_fin) . "</strong>.</p>
        <p>Ceci est un mail automatique. En cas de problème, vous pouvez répondre à ce message.</p>
        <p>Merci de votre confiance.</p>
        <p>Cordialement,</p>
        <p>L'équipe Vidal et Fils</p>
        </body>
        </html>
        ";
        $headers = "From: thomas.vidal@edu.univ-eiffel.fr\r\n";
        $headers .= "Reply-To: thomas.vidal@edu.univ-eiffel.fr\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        if (mail($destinataire, $sujet, $message, $headers)) {
            echo '<p style="color: white; background-color: gray; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Email de confirmation envoyé avec succès.</p>';
        } else {
            echo '<p style="color: white; background-color: gray; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Erreur lors de l\'envoi de l\'email de confirmation.</p>';
        }
    } else {
        echo "Erreur : " . $stmt_reservation->error;
    }
    $stmt_reservation->close();
}

$conn->close();
?>
    </main>
    <footer>
        <p>&copy; 2024 Vidal et Fils</p>
    </footer>
    <script>
        function openModal() {
            document.getElementById("myModal").style.display = "block";
        }
        function closeModal() {
            document.getElementById("myModal").style.display = "none";
        }
    </script>
</body>
</html>
