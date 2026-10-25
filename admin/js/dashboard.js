(function () {
  'use strict';

  if (window.loadAdminShell) window.loadAdminShell().catch(function (error) { console.error(error); });

  fetch('api/dashboard-data.php', { credentials: 'same-origin' })
    .then(function (response) {
      if (response.status === 401) {
        window.location.replace('index.html');
        throw new Error('Sessão administrativa necessária.');
      }
      if (!response.ok) throw new Error('Não foi possível carregar o painel.');
      return response.json();
    }).then(function (counts) {
      Object.keys(counts).forEach(function (key) {
        var target = document.querySelector('[data-count="' + key + '"]');
        if (target) target.textContent = counts[key];
      });
    }).catch(function (error) {
      if (error.message !== 'Sessão administrativa necessária.') console.error(error);
    });
})();