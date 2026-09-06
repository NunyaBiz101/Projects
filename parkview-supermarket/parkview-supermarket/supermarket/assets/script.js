document.getElementById('hamburger').addEventListener('click', function() {
  document.getElementById('navLinks').classList.toggle('open');
});

function filterCategory(cat, btn) {
  document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  const cards = document.querySelectorAll('.product-card');
  const search = document.getElementById('searchInput').value.toLowerCase();
  cards.forEach(card => {
    const matchCat = cat === 'all' || card.dataset.category === cat;
    const matchSearch = !search || card.dataset.name.includes(search);
    card.classList.toggle('hidden', !(matchCat && matchSearch));
  });
}

function filterProducts() {
  const search = document.getElementById('searchInput').value.toLowerCase();
  const activeCat = document.querySelector('.cat-btn.active')?.textContent.trim();
  const cat = activeCat === 'All' ? 'all' : activeCat;
  const cards = document.querySelectorAll('.product-card');
  cards.forEach(card => {
    const matchCat = cat === 'all' || card.dataset.category === cat;
    const matchSearch = !search || card.dataset.name.includes(search);
    card.classList.toggle('hidden', !(matchCat && matchSearch));
  });
}

function addToCart(name) {
  document.getElementById('cartToastMsg').textContent = `"${name}" added to cart!`;
  const toast = document.getElementById('cartToast');
  toast.classList.add('show');
  setTimeout(() => toast.classList.remove('show'), 3000);
}

document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    const id = a.getAttribute('href');
    if (id === '#') return;
    const el = document.querySelector(id);
    if (el) { e.preventDefault(); el.scrollIntoView({ behavior: 'smooth' }); }
  });
});

const observer = new IntersectionObserver((entries) => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      e.target.style.animation = 'fadeUp 0.6s ease forwards';
    }
  });
}, { threshold: 0.1 });

document.querySelectorAll('.product-card, .deal-card, .feature-card').forEach(el => {
  el.style.opacity = '0';
  observer.observe(el);
});

const hamburger = document.getElementById('hamburger');
const navLinks = document.getElementById('navLinks');
if (hamburger && navLinks) {
  hamburger.addEventListener('click', () => {
    navLinks.style.display = navLinks.style.display === 'flex' ? 'none' : 'flex';
  });
}

document.querySelectorAll('.nav-links a').forEach(link => {
  link.addEventListener('click', () => {
    if (window.innerWidth <= 768) {
      const nl = document.getElementById('navLinks');
      if (nl) nl.style.display = 'none';
    }
  });
});
