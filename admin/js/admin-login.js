(function () {
  'use strict';

  var loginYear = document.querySelector('[data-admin-login-year]');
  if (loginYear) loginYear.textContent = new Date().getFullYear();

  window.togglePass = function (inputId, iconId) {
    var input = document.getElementById(inputId);
    var icon = document.getElementById(iconId);
    if (!input || !icon) return;
    input.type = input.type === 'password' ? 'text' : 'password';
    icon.style.color = input.type === 'password' ? '#AABAC8' : '#48B8D4';
  };

  var form = document.querySelector('.login-form');
  if (!form) return;
  form.addEventListener('submit', function (event) {
    event.preventDefault();
    fetch('api/auth.php', {
      method: 'POST',
      body: new FormData(form),
      credentials: 'same-origin'
    }).then(function (response) {
      return response.json().then(function (result) {
        if (!response.ok) throw new Error(result.message || 'Dados de acesso incorretos.');
        return result;
      });
    }).then(function (result) {
      window.location.href = result.redirect;
    }).catch(function (error) {
      alert(error.message || 'Não foi possível iniciar sessão.');
    });
  });
})();