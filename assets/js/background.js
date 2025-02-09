const images = [
    '../assets/img/fondo1.jpg',
    '../assets/img/fondo2.jpg',
    '../assets/img/fondo3.jpg'
];

let index = 0;
function changeBackground() {
    document.body.style.backgroundImage = `url(${images[index]})`;
    index = (index + 1) % images.length;
}

setInterval(changeBackground, 10000); // Cambia cada 10 segundos
changeBackground(); // Aplica el primer cambio inmediato
