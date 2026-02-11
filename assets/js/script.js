// window.addEventListener('scroll', () => {
//     if (window.scrollY > 50) {
//         nav.classList.add('active');

//     } else {
//         nav.classList.remove('active');
//     }
// });

// Hamburger 
const hambergerOpen = document.querySelector('.hamburger-open');
const hambergerClose = document.querySelector('.hamburger-close');
const navbar = document.querySelector('.nav-inner');
const nav = document.querySelector('.nav');
hambergerOpen.addEventListener('click', () => {
    nav.style.height = "350px";
    nav.style.background = "white";
    navbar.style.height = "350px";

    hambergerOpen.style.display = "none";
    hambergerClose.style.display = "block";
})
hambergerClose.addEventListener('click', () => {
    nav.style.height = "60px";
    navbar.style.height = "60px";
    hambergerClose.style.display = "none";
    hambergerOpen.style.display = "flex";
})

// Reveal 
const elements = document.querySelectorAll(".reveal");
const elementsAnti = document.querySelectorAll(".reveal-anti");

const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add("active");
        }
    });
}, {
    threshold: 0.15
});

if (elements) {
    elements.forEach(el => observer.observe(el));
    elementsAnti.forEach(el => observer.observe(el));
}