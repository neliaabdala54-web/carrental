(function () {
  'use strict';

  if (window.loadAdminShell) window.loadAdminShell().catch(function (error) { console.error(error); });
  var form = document.getElementById('password-form');
  var status = document.getElementById('password-status');
  form.addEventListener('submit', function (event) {
    event.preventDefault();
    if (form.elements.newpassword.value !== form.elements.confirmpassword.value) {
      status.className = 'errorWrap';
      status.textContent = 'ERRO: A nova palavra-passe e a confirmação não coincidem.';
      status.hidden = false;
      form.elements.confirmpassword.focus();
      return;
    }
    var data = new FormData(form);
    data.set('action', 'change-password');
    fetch('api/auth.php', { method: 'POST', body: data, credentials: 'same-origin' })
      .then(function (response) {
        return response.json().then(function (result) {
          if (response.status === 401) { window.location.replace('index.html'); throw new Error('Inicie sessão novamente.'); }
          if (!response.ok) throw new Error(result.message || 'Não foi possível alterar a palavra-passe.');
          return result;
        });
      }).then(function (result) {
        status.className = 'succWrap';
        status.textContent = 'SUCESSO: ' + result.message;
        status.hidden = false;
        form.reset();
      }).catch(function (error) {
        status.className = 'errorWrap';
        status.textContent = 'ERRO: ' + (error.message || 'Não foi possível alterar a palavra-passe.');
        status.hidden = false;
      });
  });
})();