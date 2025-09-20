(function () {
  function setYear() {
    var yearSpan = document.getElementById('year');
    if (yearSpan) {
      yearSpan.textContent = String(new Date().getFullYear());
    }
  }

  function markActiveNav() {
    var path = location.pathname.replace(/\/index\.html?$/, '/');
    var links = document.querySelectorAll('.site-nav a');
    for (var i = 0; i < links.length; i++) {
      var link = links[i];
      if (link.getAttribute('href') === path) {
        link.classList.add('active');
      }
    }
  }

  setYear();
  markActiveNav();
})();

