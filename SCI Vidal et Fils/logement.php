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
            // Configuration de la connexion PDO
            $dsn = 'mysql:host=localhost;dbname=tvidal_vidaletfils;charset=utf8';
            $username = 'tvidal';
            $password = 'dbmotdepasse2024';

            $options = array(
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            );

            try {
                $connexion = new PDO($dsn, $username, $password, $options);
            } catch (PDOException $e) {
                die("Échec de la connexion : " . $e->getMessage());
            }

            $logement_id = $_GET['id'];
            $requete = "SELECT * FROM logement WHERE id = :logement_id";
            $stmt = $connexion->prepare($requete);
            $stmt->bindParam(':logement_id', $logement_id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                echo '<h1>' . htmlspecialchars($row["nom"]) . '</h1>';
                echo '<div class="location-images">';
                foreach (glob("img/" . htmlspecialchars($row['nom']) . "/*.jpg") as $image) {
                    echo "<img src='" . htmlspecialchars($image) . "' alt='" . htmlspecialchars($row['nom']) . "' style='width: auto; max-height: 270px;'>";
                }
                echo '</div>';
                echo '<div class="location-info">';
                echo '<p><strong>Lieu :</strong> ' . htmlspecialchars($row["lieu"]) . '</p>';
                echo '<p><strong>Prix :</strong> ' . htmlspecialchars($row["prix"]) . ' € / ' . htmlspecialchars($row["periode"]) . '</p>';
                echo '<p><strong>Surface :</strong> ' . htmlspecialchars($row["surface"]) . 'm²</p>';
                echo '<p><strong>Description :</strong> ' . htmlspecialchars($row["description"]) . '</p>';
                echo '<button onclick="openModal()">Réserver</button>';
                echo '</div>';
            } else {
                echo "Aucune information disponible pour ce logement.";
            }
            ?>
        </section>
        <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <form action="logement.php?id=<?php echo htmlspecialchars($logement_id); ?>" method="post">
                    <?php echo "Veuillez choisir vos dates en fonction de la période indiquée. <br><br>"; ?>
                    <label for="date_debut">Date de début :</label>
                    <input type="date" id="date_debut" name="date_debut" required><br><br>
                    <label for="date_fin">Date de fin :</label>
                    <input type="date" id="date_fin" name="date_fin" required><br><br>
                    <label for="email">Adresse e-mail :</label>
                    <input type="email" id="email" name="email" required><br><br>
                    <input type="hidden" name="logement_id" value="<?php echo htmlspecialchars($logement_id); ?>">
                    <input type="submit" value="Réserver">
                </form>
            </div>
        </div>
        <?php
        $dsn = 'mysql:host=localhost;dbname=tvidal_vidaletfils;charset=utf8';
        $username = 'tvidal';
        $password = 'dbmotdepasse2024';

        $options = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        try {
            $conn = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            die("La connexion a échoué : " . $e->getMessage());
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $date_debut = $_POST['date_debut'];
            $date_fin = $_POST['date_fin'];
            $email = $_POST['email'];
            $logement_id = $_POST['logement_id'];

            $sql_get_user_id = "SELECT id_utilisateur FROM utilisateur WHERE email = :email";
            $stmt = $conn->prepare($sql_get_user_id);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $id_utilisateur = $result["id_utilisateur"];
            } else {
                $sql_insertion_utilisateur = "INSERT INTO utilisateur (email) VALUES (:email)";
                $stmt_insertion = $conn->prepare($sql_insertion_utilisateur);
                $stmt_insertion->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt_insertion->execute();
                $id_utilisateur = $conn->lastInsertId();
            }

            $sql_get_logement_name = "SELECT nom FROM logement WHERE id = :logement_id";
            $stmt_logement = $conn->prepare($sql_get_logement_name);
            $stmt_logement->bindParam(':logement_id', $logement_id, PDO::PARAM_INT);
            $stmt_logement->execute();
            $logement = $stmt_logement->fetch(PDO::FETCH_ASSOC);
            $nom_logement = $logement['nom'];

            $sql_insert_reservation = "INSERT INTO reservation (id_utilisateur, date_debut, date_fin, nom_logement) VALUES (:id_utilisateur, :date_debut, :date_fin, :nom_logement)";
            $stmt_reservation = $conn->prepare($sql_insert_reservation);
            $stmt_reservation->bindParam(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
            $stmt_reservation->bindParam(':date_debut', $date_debut, PDO::PARAM_STR);
            $stmt_reservation->bindParam(':date_fin', $date_fin, PDO::PARAM_STR);
            $stmt_reservation->bindParam(':nom_logement', $nom_logement, PDO::PARAM_STR);

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
                <p>Ceci est un mail automatique. Pour toute assistance, vous pouvez répondre à ce courriel. Nous vous contacterons dans les meilleurs délais.</p>
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
                echo "Erreur : " . $stmt_reservation->errorInfo()[2];
            }
        }
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
