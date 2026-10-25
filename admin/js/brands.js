(function () {
  'use strict';

  function apiError(response) {
    return response.json().then(function (data) {
      if (!response.ok) throw new Error(data.message || 'Não foi possível carregar as marcas.');
      return data;
    });
  }

  function showError(error) {
    if (error.message === 'Sessão administrativa necessária.') {
      window.location.replace('index.html');
      return;
    }
    alert(error.message || 'Ocorreu um erro.');
  }

  var tableBody = document.querySelector('#zctb tbody');
  if (!tableBody) return;
  if (window.loadAdminShell) window.loadAdminShell().catch(showError);

  function loadBrands() {
    fetch('api/brands.php', { credentials: 'same-origin' }).then(apiError).then(function (data) {
      tableBody.replaceChildren();
      data.brands.forEach(function (brand, index) {
        var row = document.createElement('tr');
        [index + 1, brand.BrandName, brand.CreationDate || '', brand.UpdationDate || ''].forEach(function (value) {
          var cell = document.createElement('td');
          cell.textContent = value;
          row.appendChild(cell);
        });
        var actions = document.createElement('td');
        var edit = document.createElement('a');
        edit.href = 'brand-form.html?id=' + encodeURIComponent(brand.id);
        edit.title = 'Editar';
        edit.innerHTML = '<i class="fa fa-edit"></i>';
        actions.appendChild(edit);
        actions.appendChild(document.createTextNode('  '));
        var remove = document.createElement('button');
        remove.type = 'button';
        remove.title = 'Eliminar';
        remove.className = 'btn-link';
        remove.innerHTML = '<i class="fa fa-close" style="color:red"></i>';
        remove.addEventListener('click', function () {
          if (!window.confirm('Deseja realmente eliminar esta marca?')) return;
          var form = new FormData();
          form.set('action', 'delete');
          form.set('id', brand.id);
          fetch('api/brands.php', { method: 'POST', body: form, credentials: 'same-origin' })
            .then(apiError).then(function (result) { alert(result.message); loadBrands(); }).catch(showError);
        });
        actions.appendChild(remove);
        row.appendChild(actions);
        tableBody.appendChild(row);
      });
      if (window.jQuery && window.jQuery.fn.DataTable) {
        var table = window.jQuery('#zctb');
        if (window.jQuery.fn.DataTable.isDataTable('#zctb')) table.DataTable().destroy();
        table.DataTable();
      }
    }).catch(showError);
  }

  loadBrands();
})();