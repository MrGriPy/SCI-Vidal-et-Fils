function filtrerReservations() {
    const lieuSelectionne = document.getElementById('lieu').value;
    const reservations = document.querySelectorAll('.reservation');

    reservations.forEach(reservation => {
        const reservationLieu = reservation.dataset.lieu;

        if (lieuSelectionne === "Tous" || reservationLieu === lieuSelectionne) {
            reservation.style.display = "block";
        } else {
            reservation.style.display = "none";
        }
    });
}

window.addEventListener('load', function() {
    filtrerReservations();
});

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

let triCroissant = true;

function filtrerEtTrierReservations() {
    const btnTri = document.getElementById('tri-prix');
    const iconTri = document.getElementById('tri-icon');
    const reservations = document.querySelectorAll('.reservation');
    const reservationsArray = Array.from(reservations);

    reservationsArray.sort((a, b) => {
        const prixA = parseFloat(a.dataset.prix);
        const prixB = parseFloat(b.dataset.prix);

        return triCroissant ? prixA - prixB : prixB - prixA;
    });
    const derniereReservationsListe = document.getElementById('dernieres-reservations-liste');
    derniereReservationsListe.innerHTML = '';
    reservationsArray.forEach(reservation => {
        derniereReservationsListe.appendChild(reservation);
    });
    iconTri.classList.toggle('tri-inverse', !triCroissant);
    triCroissant = !triCroissant;
}
window.addEventListener('load', filtrerEtTrierReservations);
