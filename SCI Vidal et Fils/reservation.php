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
        <section class="location-details">
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
                echo '<p><strong>Lieu:</strong> ' . $row["lieu"] . '</p>';
                echo '<p><strong>Prix:</strong> ' . $row["prix"] . '€ / mois</p>';
                echo '<p><strong>Surface:</strong> ' . $row["surface"] . 'm²</p>';
                echo '<p><strong>Description:</strong> ' . $row["description"] . '</p>';
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
                <form action="reservation.php" method="post">
                    <label for="start_date">Date de début :</label>
                    <input type="date" id="start_date" name="start_date" required><br><br>
                    <label for="end_date">Date de fin :</label>
                    <input type="date" id="end_date" name="end_date" required><br><br>
                    <label for="email">Adresse e-mail :</label>
                    <input type="email" id="email" name="email" required><br><br>
                    <input type="hidden" name="logement_id" value="<?php echo $logement_id; ?>">
                    <input type="submit" value="Réserver">
                </form>
            </div>
        </div>
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
