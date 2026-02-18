/* ===============================
IMMEDIATE RESET FUNCTION
================================ */
(function () {
    // Reset any loading states
    document.querySelectorAll('.sec3-card .sec3-card-right span').forEach(span => {
        if (span.textContent === 'Loading...') {
            const card = span.closest('.sec3-card');
            if (card) card.style.pointerEvents = 'auto';
            // We'll restore original price later when re-rendering
        }
    });
})();

/* ===============================
AUTO DATES (TODAY + 5 DAYS)
================================ */
const today = new Date();
const returnDate = new Date();
returnDate.setDate(today.getDate() + 5);

const formatISO = d => d.toISOString().split("T")[0];

const AUTO_DEPART = formatISO(today);
const AUTO_RETURN = formatISO(returnDate);


/* ===============================
DATE FORMATTER (DISPLAY)
================================ */
function formatDisplayDate(dateStr) {
    const d = new Date(dateStr);
    return d.toLocaleDateString("en-US", {
        month: "short",
        day: "2-digit",
        year: "numeric"
    });
}

/* ===============================
CSRF TOKEN FETCH FUNCTION
================================ */
async function fetchCsrfToken() {
    try {
        const response = await fetch('../../php/handlers/get-csrf-token.php?' + new Date().getTime());
        const data = await response.json();
        if (data.success && data.csrf_token) {
            const csrfInput = document.getElementById('csrf_token');
            if (csrfInput) {
                csrfInput.value = data.csrf_token;
            }
            return data.csrf_token;
        }
    } catch (err) {
        console.log('CSRF token fetch note:', err.message);
    }
    return null;
}

/* ===============================
RENDER CARDS FUNCTION
================================ */
function renderFlightCards() {
    const container = document.getElementById("sec3Cards");
    if (!container) return;

    container.innerHTML = "";

    flightDeals.forEach((deal) => {
        // Create card
        const card = document.createElement("div");
        card.className = "sec3-card flex-center";
        card.style.cursor = "pointer";

        // Card HTML
        card.innerHTML = `
                <div class="sec3-card-left">
                    <p class="small">
                        ${formatDisplayDate(AUTO_DEPART)} -
                        ${formatDisplayDate(AUTO_RETURN)}
                    </p>
                    <h3>${deal.from} › ${deal.to}</h3>
                </div>
                <div class="sec3-card-right">
                    <h3><span data-original-price="${deal.price}">From $${deal.price}</span></h3>
                    <p class="small">RoundTrip</p>
                </div>
            `;

        // Click event
        card.addEventListener("click", async () => {
            // Prevent double clicks
            if (card.style.pointerEvents === "none") return;

            const priceSpan = card.querySelector(".sec3-card-right span");
            const originalPrice = priceSpan.dataset.originalPrice;

            card.style.pointerEvents = "none";
            priceSpan.textContent = "Loading...";

            // Get fresh CSRF token
            const csrfToken = await fetchCsrfToken();

            if (!csrfToken) {
                alert("Security token error. Please refresh the page.");
                card.style.pointerEvents = "auto";
                priceSpan.textContent = `From $${originalPrice}`;
                return;
            }

            const formData = new FormData();
            formData.append("origin", deal.origin);
            formData.append("destination", deal.destination);
            formData.append("departure_date", AUTO_DEPART);
            formData.append("return_date", AUTO_RETURN);
            formData.append("passengers", 1);
            formData.append("triptype", "roundtrip");
            formData.append("csrf_token", csrfToken);

            try {
                const response = await fetch("../../php/handlers/search.php", {
                    method: "POST",
                    body: formData,
                });

                const result = await response.json();

                if (result.success) {
                    sessionStorage.setItem(
                        "flightResults",
                        JSON.stringify(result.data)
                    );

                    sessionStorage.setItem(
                        "searchCriteria",
                        JSON.stringify({
                            origin: deal.origin,
                            destination: deal.destination,
                            departure_date: AUTO_DEPART,
                            return_date: AUTO_RETURN,
                            passengers: 1,
                        })
                    );

                    window.location.href = "pages/results.php";
                } else {
                    alert("No flights found");
                    // Reset card on error
                    card.style.pointerEvents = "auto";
                    priceSpan.textContent = `From $${originalPrice}`;
                }
            } catch (error) {
                console.error(error);
                alert("Search failed");
                // Reset card on error
                card.style.pointerEvents = "auto";
                priceSpan.textContent = `From $${originalPrice}`;
            }
        });

        container.appendChild(card);
    });
}

/* ===============================
PAGE LOAD/LIFECYCLE HANDLERS
================================ */
document.addEventListener('DOMContentLoaded', function () {
    renderFlightCards();
    initializeForm();
    fetchCsrfToken();
});

window.addEventListener('pageshow', function (event) {
    if (event.persisted) {
        // Re-render cards when coming from cache
        renderFlightCards();
        resetFormState();
        fetchCsrfToken();
    }
});

document.addEventListener('visibilitychange', function () {
    if (!document.hidden) {
        // Reset button states
        const searchBtn = document.getElementById("searchBtn");
        if (searchBtn) {
            const btnText = searchBtn.querySelector("h2");
            if (btnText) btnText.textContent = "Search";
            searchBtn.disabled = false;
        }
        // Re-render cards to ensure no "Loading..." states
        renderFlightCards();
        fetchCsrfToken();
    }
});

/* ===============================
EXISTING FORM CODE (Keep all your existing code below)
================================ */
const input = document.getElementById("travelerInput");
const dropdown = document.querySelector(".traveler-dropdown");

let counts = {
    adult: 1,
    child: 0,
    lap: 0,
    seat: 0
};

if (input) {
    input.addEventListener("click", () => {
        if (dropdown) dropdown.style.display = "block";
    });
}

document.querySelectorAll(".plus, .minus").forEach(btn => {
    btn.addEventListener("click", () => {
        const type = btn.dataset.type;
        if (btn.classList.contains("plus")) {
            counts[type]++;
        } else if (counts[type] > (type === "adult" ? 1 : 0)) {
            counts[type]--;
        }
        updateUI();
    });
});

const doneBtn = document.querySelector(".done-btn");
if (doneBtn) {
    doneBtn.addEventListener("click", () => {
        const total = counts.adult + counts.child + counts.lap + counts.seat;
        const cabin = document.getElementById("cabinClass")?.value || "Economy";

        if (input) input.value = `${total} Traveler(s), ${cabin}`;

        const travelerCount = document.getElementById("travelerCount");
        const cabinValue = document.getElementById("cabinValue");

        if (travelerCount) travelerCount.value = total;
        if (cabinValue) cabinValue.value = cabin;

        if (dropdown) dropdown.style.display = "none";
    });
}

function updateUI() {
    const adultSpan = document.getElementById('adultCount');
    const childSpan = document.getElementById('childCount');
    const lapSpan = document.getElementById('lapCount');
    const seatSpan = document.getElementById('seatCount');

    if (adultSpan) adultSpan.textContent = counts.adult;
    if (childSpan) childSpan.textContent = counts.child;
    if (lapSpan) lapSpan.textContent = counts.lap;
    if (seatSpan) seatSpan.textContent = counts.seat;
}

// Scroll Down
const trigger = document.querySelector('.sec1-center');
const target = document.querySelector('.sec1-top');
if (trigger && target) {
    trigger.addEventListener('click', () => {
        const y = target.getBoundingClientRect().top + window.pageYOffset;
        window.scrollTo({
            top: y,
            behavior: 'smooth'
        });
    });
}

/* =========================
DATE PICKER OPEN FIX
========================= */
document.querySelectorAll('input[type="date"]').forEach(input => {
    input.addEventListener('click', () => {
        input.showPicker && input.showPicker();
    });
});

/* =========================
DATE ELEMENTS
========================= */
const departInput = document.getElementById('departure_date');
const returnInput = document.getElementById('return_date');
const returnWrapper = document.getElementById('return-wrapper');
const roundTrip = document.getElementById('roundtrip');
const oneWay = document.getElementById('oneway');
const form = document.querySelector('.sec1-form');

/* =========================
DATE UTILITIES
========================= */
const formatDate = d => d.toISOString().split('T')[0];
const defaultReturn = new Date();
defaultReturn.setDate(today.getDate() + 5);

/* =========================
INITIAL DEFAULTS
========================= */
if (departInput) {
    departInput.value = formatDate(today);
    departInput.min = formatDate(today);
}

if (returnInput) {
    returnInput.value = formatDate(defaultReturn);
    returnInput.min = formatDate(today);
}

/* =========================
VALIDATION HELPERS
========================= */
function isReturnValid() {
    if (!returnInput || !returnInput.value) return true;
    return new Date(returnInput.value) >= new Date(departInput.value);
}

/* =========================
DEPART DATE CHANGE
========================= */
if (departInput) {
    departInput.addEventListener('change', () => {
        if (returnInput) returnInput.min = departInput.value;
    });
}

/* =========================
RETURN DATE CHANGE
========================= */
if (returnInput) {
    returnInput.addEventListener('change', () => {
        if (!isReturnValid()) {
            alert('Return date cannot be earlier than departure date');
            if (departInput) returnInput.value = departInput.value;
        }
    });
}

/* =========================
TOGGLE RETURN (ONE WAY)
========================= */
function toggleReturn() {
    if (!returnWrapper || !returnInput) return;

    if (oneWay && oneWay.checked) {
        returnWrapper.style.display = 'none';
        returnInput.removeAttribute('required');
    } else {
        returnWrapper.style.display = 'flex';
        returnInput.setAttribute('required', 'required');

        if (!isReturnValid() && departInput) {
            returnInput.value = departInput.value;
        }
    }
}

if (roundTrip) roundTrip.addEventListener('change', toggleReturn);
if (oneWay) oneWay.addEventListener('change', toggleReturn);
toggleReturn();

/* =========================
FORM SUBMIT HANDLER
========================= */
if (form) {
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const searchBtn = document.getElementById("searchBtn");
        if (!searchBtn) return;

        const btnText = searchBtn.querySelector("h2");
        const originalText = "Search";

        if (btnText) btnText.textContent = "Loading...";
        searchBtn.disabled = true;

        // Get fresh CSRF token
        await fetchCsrfToken();

        const travelers = document.getElementById("travelerCount")?.value;
        const cabin = document.getElementById("cabinValue")?.value;

        if (!travelers || travelers < 1 || !cabin) {
            alert("Please select travelers and cabin class");
            if (dropdown) dropdown.style.display = "block";
            if (btnText) btnText.textContent = originalText;
            searchBtn.disabled = false;
            return;
        }

        if (roundTrip && roundTrip.checked && !isReturnValid()) {
            alert('Please select a valid return date');
            if (btnText) btnText.textContent = originalText;
            searchBtn.disabled = false;
            return;
        }

        const originInput = document.getElementById('origin');
        const destinationInput = document.getElementById('destination');

        const origin = originInput?.dataset.iata || originInput?.value.match(/\(([^)]+)\)/)?.[1];
        const destination = destinationInput?.dataset.iata || destinationInput?.value.match(/\(([^)]+)\)/)?.[1];

        if (!origin || !destination) {
            alert('Please select valid airports from the suggestions');
            if (btnText) btnText.textContent = originalText;
            searchBtn.disabled = false;
            return;
        }

        const formData = new FormData(form);
        formData.set('origin', origin);
        formData.set('destination', destination);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                sessionStorage.setItem('flightResults', JSON.stringify(result.data));
                sessionStorage.setItem('searchCriteria', JSON.stringify({
                    origin: origin,
                    destination: destination,
                    departure_date: departInput?.value,
                    return_date: returnInput?.value || null,
                    passengers: travelers
                }));

                if (btnText) btnText.textContent = originalText;
                searchBtn.disabled = false;

                window.location.href = 'pages/results.php';
            } else {
                alert('Search failed: ' + (result.message || 'Unknown error'));
                if (btnText) btnText.textContent = originalText;
                searchBtn.disabled = false;
            }
        } catch (error) {
            console.error('Search error:', error);
            alert('An error occurred while searching for flights. Please try again.');
            if (btnText) btnText.textContent = originalText;
            searchBtn.disabled = false;
        }
    });
}

/* =========================
AIRPORT AUTOCOMPLETE
========================= */
let airports = [];

fetch('../../data/airports.json?' + new Date().getTime())
    .then(res => res.json())
    .then(data => {
        airports = Object.values(data)
            .filter(a => a.iata && a.city && a.name);
    })
    .catch(err => console.log('Airports fetch error:', err));

function setupAutocomplete(inputId, suggestionId) {
    const input = document.getElementById(inputId);
    const suggestionBox = document.getElementById(suggestionId);

    if (!input || !suggestionBox) return;

    input.addEventListener('input', () => {
        const query = input.value.toLowerCase();
        suggestionBox.innerHTML = '';

        if (query.length < 2) {
            suggestionBox.style.display = 'none';
            return;
        }
        suggestionBox.style.display = 'block';

        const matches = airports.filter(a =>
            a.city?.toLowerCase().includes(query) ||
            a.name?.toLowerCase().includes(query) ||
            a.iata?.toLowerCase().includes(query)
        ).slice(0, 10);

        matches.forEach(a => {
            const li = document.createElement('li');
            li.textContent = `${a.city} – ${a.name} (${a.iata})`;

            li.onclick = () => {
                input.value = `${a.city} (${a.iata})`;
                input.dataset.iata = a.iata;
                suggestionBox.innerHTML = '';
                suggestionBox.style.display = 'none';
            };

            suggestionBox.appendChild(li);
        });
    });

    document.addEventListener('click', e => {
        if (!suggestionBox.contains(e.target) && e.target !== input) {
            suggestionBox.style.display = 'none';
            suggestionBox.innerHTML = '';
        }
    });
}

setupAutocomplete('origin', 'from-suggestions');
setupAutocomplete('destination', 'to-suggestions');

// Function to reset form state (add this)
function resetFormState() {
    // Reset counts
    counts = {
        adult: 1,
        child: 0,
        lap: 0,
        seat: 0
    };
    updateUI();

    // Reset traveler input
    const travelerInput = document.getElementById('travelerInput');
    const travelerCount = document.getElementById('travelerCount');
    const cabinValue = document.getElementById('cabinValue');
    const cabinClass = document.getElementById('cabinClass');

    if (cabinClass) cabinClass.value = 'Economy';
    if (travelerInput) travelerInput.value = '1 Traveler(s), Economy';
    if (travelerCount) travelerCount.value = '1';
    if (cabinValue) cabinValue.value = 'Economy';

    // Clear airport inputs
    const origin = document.getElementById('origin');
    const destination = document.getElementById('destination');

    if (origin) {
        origin.value = '';
        delete origin.dataset.iata;
    }

    if (destination) {
        destination.value = '';
        delete destination.dataset.iata;
    }

    // Hide dropdown
    if (dropdown) dropdown.style.display = 'none';
}

// Initialize form state
function initializeForm() {
    const searchBtn = document.getElementById("searchBtn");
    if (searchBtn) {
        const btnText = searchBtn.querySelector("h2");
        if (btnText) btnText.textContent = "Search";
        searchBtn.disabled = false;
        if (travelerInput) travelerInput.value = '1 Traveler(s), Economy';
    }
}