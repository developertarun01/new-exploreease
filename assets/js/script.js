// window.addEventListener('scroll', () => {
//     if (window.scrollY > 50) {
//         nav.classList.add('active');

//     } else {
//         nav.classList.remove('active');
//     }
// });

// Yesterday Date 
function formatYesterday() {
    const el = document.querySelector(".yesterday");

    const now = new Date();

    // Get yesterday
    now.setDate(now.getDate() - 1);

    // Format parts
    const days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

    let dayName = days[now.getDay()];
    let month = months[now.getMonth()];
    let date = now.getDate();
    let year = now.getFullYear();

    // Time formatting (12-hour)
    let hours = now.getHours();
    let minutes = now.getMinutes().toString().padStart(2, '0');

    let ampm = hours >= 12 ? "PM" : "AM";
    hours = hours % 12 || 12;

    let formatted =
        `${dayName} ${month} ${date},${year} at ${hours}:${minutes} ${ampm}`;

    el.textContent = formatted;
}

formatYesterday();

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