(function () {
  'use strict';

  if (window.loadAdminShell) window.loadAdminShell().catch(function (error) { console.error(error); });
  var body = document.querySelector('#zctb tbody');

  function parseResponse(response) {
    return response.json().then(function (data) {
      if (response.status === 401) { window.location.replace('index.html'); throw new Error('Sessão administrativa necessária.'); }
      if (!response.ok) throw new Error(data.message || 'Não foi possível carregar os depoimentos.');
      return data;
    });
  }

  function load() {
    fetch('api/testimonials.php', { credentials: 'same-origin' }).then(parseResponse).then(function (data) {
      if (window.jQuery && window.jQuery.fn.DataTable && window.jQuery.fn.DataTable.isDataTable('#zctb')) window.jQuery('#zctb').DataTable().destroy();
      body.replaceChildren();
      data.testimonials.forEach(function (testimonial, index) {
        var row = document.createElement('tr');
        [index + 1, testimonial.FullName, testimonial.UserEmail, testimonial.Testimonial, testimonial.PostingDate].forEach(function (value) {
          var cell = document.createElement('td');
          cell.textContent = value == null ? '' : String(value);
          row.appendChild(cell);
        });
        var actionCell = document.createElement('td');
        var active = Number(testimonial.status) === 1;
        var button = document.createElement('button');
        button.type = 'button';
        button.className = active ? 'btn btn-xs btn-danger' : 'btn btn-xs btn-success';
        button.innerHTML = active ? '<i class="fa fa-times"></i> Desativar' : '<i class="fa fa-check"></i> Ativar';
        button.addEventListener('click', function () {
          if (!window.confirm(active ? 'Desativar este depoimento?' : 'Ativar e publicar este depoimento?')) return;
          var form = new FormData();
          form.set('id', testimonial.id);
          form.set('status', active ? '0' : '1');
          fetch('api/testimonials.php', { method: 'POST', body: form, credentials: 'same-origin' }).then(parseResponse).then(function (result) { alert(result.message); load(); }).catch(function (error) { alert(error.message); });
        });
        actionCell.appendChild(button);
        row.appendChild(actionCell);
        body.appendChild(row);
      });
      if (window.jQuery && window.jQuery.fn.DataTable) window.jQuery('#zctb').DataTable();
    }).catch(function (error) { if (error.message !== 'Sessão administrativa necessária.') alert(error.message); });
  }

  load();
})();