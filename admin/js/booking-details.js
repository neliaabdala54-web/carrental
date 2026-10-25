(function () {
  'use strict';

  var parameters = new URLSearchParams(window.location.search);
  var id = parameters.get('id') || parameters.get('bid');
  if (window.loadAdminShell) window.loadAdminShell().catch(function (error) { console.error(error); });
  if (!id) { window.location.replace('bookings.html?status=all'); return; }

  function cell(row, label, value, colspan) {
    var heading = document.createElement('th');
    heading.textContent = label;
    row.appendChild(heading);
    var data = document.createElement('td');
    data.textContent = value == null ? '' : String(value);
    if (colspan) data.colSpan = colspan;
    row.appendChild(data);
  }

  function section(body, title) {
    var row = document.createElement('tr');
    var heading = document.createElement('th');
    heading.colSpan = 4;
    heading.textContent = title;
    heading.style.cssText = 'text-align:center;color:#2b6cb0;background:#edf2f7;';
    row.appendChild(heading);
    body.appendChild(row);
  }

  function parseResponse(response) {
    return response.json().then(function (data) {
      if (response.status === 401) { window.location.replace('index.html'); throw new Error('Sessão administrativa necessária.'); }
      if (!response.ok) throw new Error(data.message || 'Não foi possível carregar a reserva.');
      return data;
    });
  }

  fetch('api/bookings.php?id=' + encodeURIComponent(id), { credentials: 'same-origin' })
    .then(parseResponse)
    .then(function (data) {
      var booking = data.booking;
      document.getElementById('booking-number').textContent = 'Detalhes da Reserva #' + booking.BookingNumber;
      var body = document.getElementById('booking-details');
      section(body, 'Dados do Cliente');
      var row = document.createElement('tr'); cell(row, 'Nº da Reserva', '#' + booking.BookingNumber); cell(row, 'Nome Completo', booking.FullName); body.appendChild(row);
      row = document.createElement('tr'); cell(row, 'E-mail', booking.EmailId); cell(row, 'Contacto', booking.ContactNo); body.appendChild(row);
      row = document.createElement('tr'); cell(row, 'Endereço', booking.Address); cell(row, 'Cidade', booking.City); body.appendChild(row);
      row = document.createElement('tr'); cell(row, 'País', booking.Country || 'Moçambique', 3); body.appendChild(row);
      section(body, 'Detalhes do Aluguer');
      row = document.createElement('tr'); cell(row, 'Veículo', booking.BrandName + ' , ' + booking.VehiclesTitle); cell(row, 'Data da Solicitação', booking.PostingDate); body.appendChild(row);
      row = document.createElement('tr'); cell(row, 'Data Início', booking.FromDate); cell(row, 'Data Fim', booking.ToDate); body.appendChild(row);
      var days = Number(booking.totaldays) > 0 ? Number(booking.totaldays) : 1;
      row = document.createElement('tr'); cell(row, 'Total de Dias', days + ' Dia(s)'); cell(row, 'Valor por Dia', booking.PricePerDay + ' MT'); body.appendChild(row);
      row = document.createElement('tr');
      var totalLabel = document.createElement('th'); totalLabel.colSpan = 3; totalLabel.style.cssText = 'text-align:right;font-weight:bold;font-size:15px;'; totalLabel.textContent = 'VALOR TOTAL DA RESERVA:'; row.appendChild(totalLabel);
      var total = document.createElement('td'); total.style.cssText = 'font-weight:bold;font-size:16px;color:#fa2823;'; total.textContent = (days * Number(booking.PricePerDay)) + ' MT'; row.appendChild(total); body.appendChild(row);
      row = document.createElement('tr'); cell(row, 'Estado da Reserva', ''); row.lastChild.appendChild(statusLabel(booking.Status)); cell(row, 'Última Atualização', booking.LastUpdationDate); body.appendChild(row);
      if (Number(booking.Status) === 0) {
        [['confirm', 'Confirmar Reserva', 'btn btn-success', 'Deseja realmente confirmar esta reserva?'], ['cancel', 'Cancelar Reserva', 'btn btn-danger', 'Deseja realmente cancelar esta reserva?']].forEach(function (action) {
          var button = document.createElement('button'); button.type = 'button'; button.className = action[2]; button.textContent = action[1];
          button.addEventListener('click', function () {
            if (!window.confirm(action[3])) return;
            var form = new FormData(); form.set('action', action[0]); form.set('id', id);
            fetch('api/bookings.php', { method: 'POST', body: form, credentials: 'same-origin' }).then(parseResponse).then(function (result) { alert(result.message); window.location.reload(); }).catch(function (error) { alert(error.message); });
          });
          document.getElementById('booking-actions').appendChild(button);
          document.getElementById('booking-actions').appendChild(document.createTextNode(' '));
        });
      }
    }).catch(function (error) { if (error.message !== 'Sessão administrativa necessária.') { alert(error.message); window.location.replace('bookings.html?status=all'); } });

  function statusLabel(value) {
    var label = document.createElement('span');
    var state = { 0: ['Pendente', 'label label-warning'], 1: ['Confirmada', 'label label-success'], 2: ['Cancelada', 'label label-danger'] }[Number(value)] || ['Pendente', 'label label-warning'];
    label.className = state[1]; label.textContent = state[0]; return label;
  }
})();