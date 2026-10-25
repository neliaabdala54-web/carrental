(function () {
  'use strict';

  var status = new URLSearchParams(window.location.search).get('status') || 'pending';
  var titles = {
    pending: ['Novas Reservas Pendentes', 'Lista de Novas Reservas'],
    confirmed: ['Reservas Confirmadas', 'Lista de Reservas Confirmadas'],
    cancelled: ['Reservas Canceladas', 'Lista de Reservas Canceladas'],
    all: ['Gerir Reservas', 'Todas as Reservas']
  };
  if (!titles[status]) { window.location.replace('dashboard.html'); return; }
  if (window.loadAdminShell) window.loadAdminShell().catch(function (error) { console.error(error); });
  document.title = 'Portal de Aluguer de Carros | ' + titles[status][0];
  document.getElementById('booking-list-title').textContent = titles[status][0];
  document.getElementById('booking-panel-title').textContent = titles[status][1];

  var headers = status === 'all'
    ? ['#', 'Nome do Cliente', 'Veículo', 'Data Início', 'Data Fim', 'Mensagem', 'Estado', 'Data do Pedido', 'Ações']
    : ['#', 'Nome do Cliente', 'Nº da Reserva', 'Veículo', 'Data Início', 'Data Fim', 'Estado', 'Data do Pedido', 'Ação'];
  ['booking-head', 'booking-foot'].forEach(function (id) {
    var row = document.getElementById(id);
    headers.forEach(function (header) {
      var cell = document.createElement('th');
      cell.textContent = header;
      row.appendChild(cell);
    });
  });

  function statusLabel(value) {
    var label = document.createElement('span');
    var states = { 0: ['Pendente', 'label label-warning'], 1: ['Confirmada', 'label label-success'], 2: ['Cancelada', 'label label-danger'] };
    var state = states[Number(value)] || states[0];
    label.className = state[1];
    label.textContent = state[0];
    return label;
  }

  function render(records) {
    var body = document.querySelector('#zctb tbody');
    body.replaceChildren();
    records.forEach(function (booking, index) {
      var row = document.createElement('tr');
      var vehicle = document.createElement('a');
      vehicle.href = 'vehicle-form.html?id=' + encodeURIComponent(booking.vid);
      vehicle.textContent = booking.BrandName + ' , ' + booking.VehiclesTitle;
      var values = status === 'all'
        ? [index + 1, booking.FullName, vehicle, booking.FromDate, booking.ToDate, booking.message, statusLabel(booking.Status), booking.PostingDate]
        : [index + 1, booking.FullName, booking.BookingNumber, vehicle, booking.FromDate, booking.ToDate, statusLabel(booking.Status), booking.PostingDate];
      values.forEach(function (value) {
        var cell = document.createElement('td');
        if (value instanceof Node) cell.appendChild(value);
        else cell.textContent = value == null ? '' : String(value);
        row.appendChild(cell);
      });
      var actions = document.createElement('td');
      if (status === 'all') {
        [['confirm', 'Confirmar', 'btn btn-xs btn-success', 'Deseja realmente confirmar esta reserva?'], ['cancel', 'Cancelar', 'btn btn-xs btn-danger', 'Deseja realmente cancelar esta reserva?']].forEach(function (action) {
          var button = document.createElement('button');
          button.type = 'button';
          button.className = action[2];
          button.textContent = action[1];
          button.addEventListener('click', function () {
            if (!window.confirm(action[3])) return;
            var form = new FormData();
            form.set('action', action[0]);
            form.set('id', booking.id);
            fetch('api/bookings.php', { method: 'POST', body: form, credentials: 'same-origin' }).then(parseResponse).then(load).catch(showError);
          });
          actions.appendChild(button);
          actions.appendChild(document.createTextNode(' '));
        });
      }
      var details = document.createElement('a');
      details.href = 'booking-details.html?id=' + encodeURIComponent(booking.id);
      details.className = 'btn btn-xs btn-primary';
      details.textContent = 'Ver Detalhes';
      actions.appendChild(details);
      row.appendChild(actions);
      body.appendChild(row);
    });
    if (window.jQuery && window.jQuery.fn.DataTable) window.jQuery('#zctb').DataTable();
  }

  function parseResponse(response) {
    return response.json().then(function (data) {
      if (response.status === 401) {
        window.location.replace('index.html');
        throw new Error('Sessão administrativa necessária.');
      }
      if (!response.ok) throw new Error(data.message || 'Não foi possível carregar as reservas.');
      return data;
    });
  }

  function showError(error) {
    if (error.message !== 'Sessão administrativa necessária.') alert(error.message || 'Ocorreu um erro.');
  }

  function load() {
    fetch('api/bookings.php?status=' + encodeURIComponent(status), { credentials: 'same-origin' })
      .then(parseResponse).then(function (data) { render(data.bookings); }).catch(showError);
  }
  load();
})();