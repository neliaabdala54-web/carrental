(function () {
  'use strict';

  window.loadAdminShell = function () {
    return Promise.all([
      fetch('partials/header.html').then(function (response) { return response.text(); }),
      fetch('partials/sidebar.html').then(function (response) { return response.text(); }),
      fetch('partials/footer.html').then(function (response) { return response.text(); })
    ]).then(function (fragments) {
      document.getElementById('admin-header').innerHTML = fragments[0];
      document.getElementById('admin-sidebar').innerHTML = fragments[1];
      var content = document.querySelector('.content-wrapper');
      if (content && !content.querySelector('.admin-footer')) {
        var footer = document.createElement('footer');
        footer.innerHTML = fragments[2];
        content.appendChild(footer);
        footer.querySelector('[data-admin-year]').textContent = new Date().getFullYear();
      }
      document.querySelectorAll('.ts-sidebar-menu li a').forEach(function (link) {
        if (!link.nextElementSibling) return;
        link.classList.add('parent');
        var toggle = document.createElement('div');
        toggle.className = 'more';
        toggle.innerHTML = '<i class="fa fa-angle-down"></i>';
        link.parentElement.insertBefore(toggle, link);
        toggle.addEventListener('click', function () { link.parentElement.classList.toggle('open'); });
        link.addEventListener('click', function (event) {
          event.preventDefault();
          link.parentElement.classList.toggle('open');
        });
      });
      var menuButton = document.querySelector('.menu-btn');
      if (menuButton) menuButton.addEventListener('click', function () {
        document.querySelector('nav.ts-sidebar').classList.toggle('menu-open');
      });
    });
  };
})();