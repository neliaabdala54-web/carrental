(function () {
  'use strict';

  var parameters = new URLSearchParams(window.location.search);
  var id = parameters.get('id');
  var form = document.getElementById('brand-form');
  var title = document.getElementById('brand-form-title');
  var submit = document.getElementById('brand-submit');
  if (window.loadAdminShell) window.loadAdminShell().catch(function (error) { console.error(error); });

  function parseResponse(response) {
    return response.json().then(function (data) {
      if (!response.ok) throw new Error(data.message || 'Não foi possível concluir a operação.');
      return data;
    });
  }

  if (id) {
    title.textContent = 'Editar Marca';
    submit.textContent = 'Atualizar Marca';
    fetch('api/brands.php?id=' + encodeURIComponent(id), { credentials: 'same-origin' })
      .then(parseResponse)
      .then(function (data) { form.elements.brand.value = data.brand.BrandName; })
      .catch(function () { window.location.replace('brands.html'); });
  }

  form.addEventListener('submit', function (event) {
    event.preventDefault();
    var data = new FormData(form);
    data.set('action', id ? 'update' : 'create');
    if (id) data.set('id', id);
    fetch('api/brands.php', { method: 'POST', body: data, credentials: 'same-origin' })
      .then(parseResponse)
      .then(function (result) {
        alert(result.message);
        window.location.href = 'brands.html';
      })
      .catch(function (error) {
        if (error.message === 'Sessão administrativa necessária.') window.location.replace('index.html');
        else alert(error.message || 'Não foi possível guardar a marca.');
      });
  });
})();