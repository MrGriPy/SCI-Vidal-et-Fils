<?php
    $connexion = new mysqli("localhost", "tvidal", "dbmotdepasse2024", "tvidal_vidaletfils");

    if ($connexion->connect_error) {
        die("Échec de la connexion : " . $connexion->connect_error);
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
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
        <section class="presentation">
    <h2>Bienvenue sur notre site de location. Vous y trouverez nos appartements du Mans ainsi que notre maison sur Vicdessos.</h2>
    <div class="slider-container">
        <div class="slider">
            <?php
            $slider_images = array();
            $requete = "SELECT nom FROM logement";
            $resultat = $connexion->query($requete);
            while ($row = $resultat->fetch_assoc()) {
                $nom_logement = $row["nom"];
                $image_path = 'img/' . $nom_logement . '/Illustration.jpg';
                if (file_exists($image_path)) {
                    $slider_images[] = $image_path;
                }
            }

            foreach ($slider_images as $index => $image_path) {
                echo '<div class="slide">';
                echo '<a href="logement.php?id=' . ($index + 1) . '">';
                echo '<img src="' . $image_path . '" alt="Slide ' . ($index + 1) . '">';
                echo '</a>';
                echo '</div>';
            }
            ?>
        </div>

        <button class="prev">&#10094;</button>
        <button class="next">&#10095;</button>
    </div>
    <div class="dots-container">
        <?php
        $nombre_images = count($slider_images);
        for ($i = 0; $i < $nombre_images; $i++) {
            echo '<span class="dot"></span>';
        }
        ?>
    </div>
</section>
<section class="dernieres-reservations">
    <section class="recherche">
        <form id="recherche-formulaire" onsubmit="event.preventDefault(); filtrerReservations();">
            <label for="lieu">Lieu :</label>
            <select id="lieu" class="lieu">
                <option value="Tous">Tous</option>
                <option value="Le Mans">Le Mans</option>
                <option value="Vicdessos">Vicdessos</option>
            </select>
            <button type="submit">Rechercher</button>   
            <button id="tri-prix" class="btn-tri" aria-label="Trier par prix" onclick="filtrerEtTrierReservations()">Prix : 
                <span id="tri-icon" class="arrow-up">▼</span>
            </button>
            
        </form>
    </section>

            <h2>Résultats :</h2>
            <div id="dernieres-reservations-liste" style="display: flex;flex-wrap: wrap;justify-content: center;">
                <?php
                    $requete = "SELECT * FROM logement";
                    $resultat = $connexion->query($requete);
                    if ($resultat->num_rows > 0) {
                        while ($row = $resultat->fetch_assoc()) {
                            echo '<div class="location-container reservation" data-lieu="' . $row["lieu"] . '" data-prix="' . $row["prix"] . '">';
                            echo '<a href="logement.php?id=' . $row["id"] . '">';

                            echo '<h3>' . $row["nom"] . '</h3>';
                            echo '<img src="img/' . $row["nom"] . '/Illustration.jpg" alt="' . $row["nom"] . '" class="illustration">';
                            echo '<div>';
                            echo '<div class="description1">';
                            echo '<img src="img/prix.png" alt="Prix">';
                            echo '<img src="img/surface.png" alt="Surface">';
                            echo '<img src="img/lieu.png" alt="Lieu">';
                            echo '</div>';
                            echo '<div class="description2">';
                            echo '<p>' . $row["prix"] . '€ / mois' . $row["type"] . '</p>';
                            echo '<p>' . $row["surface"] . 'm²' . '</p>';
                            echo '<p>' . $row["lieu"] . '</p>';
                            echo '</div>';
                            echo '</div>';
                            echo '</a>';
                            echo '</div>';
                        }
                    } else {
                        echo "Aucun résultat trouvé.";
                    }
                ?>
            </div>
        </section>
        
    </main>
    <footer>
        <p style>&copy; 2024 Vidal et Fils</p>
    </footer>
    <script src="script.js"></script>
</body>
</html>
