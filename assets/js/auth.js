(function () {
  'use strict';

  function togglePasswordVisibility(inputId, iconId) {
    var input = document.getElementById(inputId);
    var icon = document.getElementById(iconId);
    if (!input || !icon) return;

    input.type = input.type === 'password' ? 'text' : 'password';
    icon.className = input.type === 'password' ? 'fa fa-eye' : 'fa fa-eye-slash';
  }

  window.togglePasswordVisibility = togglePasswordVisibility;

  document.addEventListener('submit', function (event) {
    var form = event.target.closest('[data-auth-form]');
    if (!form) return;

    event.preventDefault();
    var action = form.getAttribute('data-auth-form');
    var data = new FormData(form);

    if (action === 'signup' && data.get('password') !== data.get('confirmpassword')) {
      alert('A senha e a confirmação de senha não coincidem!');
      form.elements.confirmpassword.focus();
      return;
    }

    if (action === 'reset' && data.get('newpassword') !== data.get('confirmpassword')) {
      alert('A nova senha e a confirmação de senha não coincidem!');
      form.elements.confirmpassword.focus();
      return;
    }

    data.set('action', action);
    fetch('api/auth.php', {
      method: 'POST',
      body: data,
      credentials: 'same-origin'
    })
      .then(function (response) {
        return response.json().then(function (result) {
          if (!response.ok) throw new Error(result.message || 'Não foi possível concluir a solicitação.');
          return result;
        });
      })
      .then(function (result) {
        alert(result.message);
        if (action === 'login') window.location.reload();
        if (action === 'signup' || action === 'reset') form.reset();
      })
      .catch(function (error) {
        alert(error.message);
      });
  });

  document.addEventListener('blur', function (event) {
    if (event.target.id !== 'emailid') return;

    var email = event.target.value.trim();
    var status = document.getElementById('user-availability-status');
    if (!email || !status) return;

    var data = new FormData();
    data.set('action', 'availability');
    data.set('email', email);
    fetch('api/auth.php', { method: 'POST', body: data, credentials: 'same-origin' })
      .then(function (response) { return response.json(); })
      .then(function (result) {
        status.textContent = result.available ? 'E-mail disponível.' : 'Este e-mail já está cadastrado.';
        status.style.color = result.available ? 'green' : 'red';
      })
      .catch(function () {
        status.textContent = 'Não foi possível verificar este e-mail.';
        status.style.color = 'red';
      });
  }, true);
})();