
function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelector('#panel-' + tab).classList.add('active');
    event.currentTarget.classList.add('active');

    // Met à jour le badge actif dans le panneau gauche
    document.querySelectorAll('.role-badge').forEach((b, i) => {
        b.classList.toggle('active', (tab === 'etudiant' && i === 0) || (tab === 'professeur' && i === 1));
    });
}

function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}

// Loading sur submit
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function() {
        const btn = this.querySelector('.btn-submit');
        btn.textContent = 'Traitement en cours…';
        btn.classList.add('loading');
        btn.disabled = true;
    });
});

function togglePassword() {
    const input = document.getElementById('password');
    input.type = input.type === 'password' ? 'text' : 'password';
}

const pdfViewerEl = document.getElementById('pdfViewer');
if (pdfViewerEl) pdfViewerEl.addEventListener('contextmenu', e => e.preventDefault());
const btn = document.getElementById('submitBtn');


// Révélation au scroll
const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => {
        if (e.isIntersecting) {
            e.target.classList.add('visible');
            observer.unobserve(e.target);
        }
    });
}, { threshold: 0.12 });

document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

// ── VALIDATION — toggle formulaire rejet ──
function toggleRejetForm(idMemoire) {
    const form = document.getElementById('rejet-' + idMemoire);
    if (form) form.classList.toggle('visible');
}
function toggleReponse(id) {
    const div = document.getElementById('reponse-' + id);
    if (div.style.display === 'none' || div.style.display === '') {
        div.style.display = 'block';
    } else {
        div.style.display = 'none';
    }
}