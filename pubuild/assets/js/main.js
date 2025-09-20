document.addEventListener('DOMContentLoaded', () => {
  const yearEl = document.getElementById('year');
  if (yearEl) yearEl.textContent = String(new Date().getFullYear());

  // Enforce .gov/.mil visibility hint on contact page
  const email = document.querySelector('input[type="email"][data-gov-only]');
  if (email) {
    email.addEventListener('input', () => {
      const value = email.value.trim().toLowerCase();
      const message = document.getElementById('gov-only-msg');
      const valid = /\.(gov|mil)$/.test(value.split('@')[1] || '');
      if (message) {
        message.textContent = valid ? '' : 'Government email (.gov or .mil) required.';
      }
    });
  }
});

