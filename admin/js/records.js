(function () {
  'use strict';

  var definitions = {
    subscribers: {
      title: 'Gerir Subscritores da Newsletter',
      panel: 'Lista de Subscritores',
      columns: ['#', 'E-mail do Subscritor', 'Data da Subscrição', 'Ação'],
      values: function (record, index) { return [index + 1, record.SubscriberEmail, record.PostingDate]; },
      action: 'delete-subscriber',
      actionTitle: 'Deseja realmente eliminar este subscritor?',
      actionText: 'Eliminar'
    },
    contacts: {
      title: 'Mensagens de Contacto',
      panel: 'Mensagens Recebidas',
      columns: ['#', 'Nome', 'E-mail', 'Contacto', 'Mensagem', 'Data do Envio', 'Estado / Ação'],
      values: function (record, index) { return [index + 1, record.name, record.EmailId, record.ContactNumber, record.Message, record.PostingDate]; },
      action: 'read-contact',
      actionTitle: 'Marcar esta mensagem como lida?',
      actionText: 'Marcar como Lida'
    },
    users: {
      title: 'Utilizadores Registados',
      panel: 'Lista de Utilizadores',
      columns: ['#', 'Nome Completo', 'E-mail', 'Contacto', 'Data Nasc.', 'Endereço', 'Cidade', 'País', 'Data de Registo'],
      values: function (record, index) { return [index + 1, record.FullName, record.EmailId, record.ContactNo, record.dob, record.Address, record.City, record.Country || 'Moçambique', record.RegDate]; }
    }
  };
  var type = new URLSearchParams(window.location.search).get('type') || '';
  var definition = definitions[type];
  if (!definition) {
    window.location.replace('dashboard.html');
    return;
  }
  if (window.loadAdminShell) window.loadAdminShell().catch(function (error) { console.error(error); });

  document.title = 'Portal de Aluguer de Carros | ' + definition.title;
  document.getElementById('records-title').textContent = definition.title;
  document.getElementById('records-panel-title').textContent = definition.panel;
  var head = document.getElementById('records-head');
  var foot = document.getElementById('records-foot');
  definition.columns.forEach(function (column) {
    var th = document.createElement('th');
    th.textContent = column;
    head.appendChild(th);
    th = document.createElement('th');
    th.textContent = column;
    foot.appendChild(th);
  });

  function responseData(response) {
    return response.json().then(function (data) {
      if (response.status === 401) {
        window.location.replace('index.html');
        throw new Error('Sessão administrativa necessária.');
      }
      if (!response.ok) throw new Error(data.message || 'Não foi possível carregar os registos.');
      return data;
    });
  }

  function loadRecords() {
    fetch('api/records.php?type=' + encodeURIComponent(type), { credentials: 'same-origin' })
      .then(responseData)
      .then(function (data) {
        var body = document.querySelector('#zctb tbody');
        if (window.jQuery && window.jQuery.fn.DataTable && window.jQuery.fn.DataTable.isDataTable('#zctb')) {
          window.jQuery('#zctb').DataTable().destroy();
        }
        body.replaceChildren();
        data.records.forEach(function (record, index) {
          var row = document.createElement('tr');
          definition.values(record, index).forEach(function (value) {
            var cell = document.createElement('td');
            cell.textContent = value == null ? '' : String(value);
            row.appendChild(cell);
          });
          if (type !== 'users') {
            var actionCell = document.createElement('td');
            if (type === 'contacts' && Number(record.status) === 1) {
              var label = document.createElement('span');
              label.className = 'label label-success';
              label.innerHTML = '<i class="fa fa-check"></i> Lida';
              actionCell.appendChild(label);
            } else {
              var button = document.createElement('button');
              button.type = 'button';
              button.className = type === 'contacts' ? 'btn btn-xs btn-warning' : 'btn-link';
              button.textContent = definition.actionText;
              button.addEventListener('click', function () {
                if (!window.confirm(definition.actionTitle)) return;
                var form = new FormData();
                form.set('action', definition.action);
                form.set('id', record.id);
                fetch('api/records.php', { method: 'POST', body: form, credentials: 'same-origin' })
                  .then(responseData)
                  .then(function (result) { alert(result.message); loadRecords(); })
                  .catch(function (error) { alert(error.message || 'Não foi possível concluir a ação.'); });
              });
              actionCell.appendChild(button);
            }
            row.appendChild(actionCell);
          }
          body.appendChild(row);
        });
        if (window.jQuery && window.jQuery.fn.DataTable) window.jQuery('#zctb').DataTable();
      })
      .catch(function (error) {
        if (error.message !== 'Sessão administrativa necessária.') alert(error.message);
      });
  }

  loadRecords();
})();