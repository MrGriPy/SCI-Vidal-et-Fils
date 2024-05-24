// Fonction pour filtrer les réservations en fonction du lieu sélectionné
function filtrerReservations() {
    const lieuSelectionne = document.getElementById('lieu').value;
    const reservations = document.querySelectorAll('.reservation');

    // Parcourir toutes les réservations
    reservations.forEach(reservation => {
        const reservationLieu = reservation.dataset.lieu;

        // Vérifier si la réservation correspond au lieu sélectionné ou si "Tous" est sélectionné
        if (lieuSelectionne === "Tous" || reservationLieu === lieuSelectionne) {
            reservation.style.display = "block"; // Afficher la réservation
        } else {
            reservation.style.display = "none"; // Masquer la réservation
        }
    });
}

// Réinitialiser l'affichage des réservations lors du chargement de la page
window.addEventListener('load', function() {
    filtrerReservations();
});

// Slider
let slideIndex = 0;
const slides = document.querySelectorAll('.slide');
const dots = document.querySelectorAll('.dot');
const totalSlides = slides.length;

function showSlide(n) {
    slideIndex = (n + totalSlides) % totalSlides;
    slides.forEach(slide => slide.style.transform = `translateX(-${slideIndex * 100}%)`);
    dots.forEach(dot => dot.classList.remove('active'));
    dots[slideIndex].classList.add('active');
}

function nextSlide() {
    showSlide(slideIndex + 1);
}

function prevSlide() {
    showSlide(slideIndex - 1);
}

function currentSlide(n) {
    showSlide(n);
}

document.querySelector('.next').addEventListener('click', nextSlide);
document.querySelector('.prev').addEventListener('click', prevSlide);

dots.forEach((dot, index) => {
    dot.addEventListener('click', () => {
        currentSlide(index);
    });
});

let slideInterval = setInterval(nextSlide, 3000);

document.querySelector('.slider-container').addEventListener('mouseenter', () => {
    clearInterval(slideInterval);
});

document.querySelector('.slider-container').addEventListener('mouseleave', () => {
    slideInterval = setInterval(nextSlide, 3000);
});

showSlide(slideIndex);

let triCroissant = true; // Variable pour suivre l'état du tri

function filtrerEtTrierReservations() {
    const btnTri = document.getElementById('tri-prix');
    const iconTri = document.getElementById('tri-icon');
    const reservations = document.querySelectorAll('.reservation');

    // Convertir NodeList en tableau pour manipuler plus facilement
    const reservationsArray = Array.from(reservations);

    // Trier les réservations en fonction du prix
    reservationsArray.sort((a, b) => {
        const prixA = parseFloat(a.dataset.prix);
        const prixB = parseFloat(b.dataset.prix);

        return triCroissant ? prixA - prixB : prixB - prixA;
    });

    // Mettre à jour l'ordre des éléments dans le DOM
    const derniereReservationsListe = document.getElementById('dernieres-reservations-liste');
    derniereReservationsListe.innerHTML = '';
    reservationsArray.forEach(reservation => {
        derniereReservationsListe.appendChild(reservation);
    });

    // Mettre à jour l'icône du bouton
    iconTri.classList.toggle('tri-inverse', !triCroissant);
    triCroissant = !triCroissant;
}

// Appeler la fonction une fois au chargement de la page pour appliquer le filtre dès le début
window.addEventListener('load', filtrerEtTrierReservations);
