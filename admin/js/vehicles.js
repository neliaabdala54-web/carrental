(function () {
  'use strict';

  if (window.loadAdminShell) window.loadAdminShell().catch(function (error) { console.error(error); });
  var body = document.querySelector('#zctb tbody');

  function parseResponse(response) {
    return response.json().then(function (data) {
      if (response.status === 401) { window.location.replace('index.html'); throw new Error('Sessão administrativa necessária.'); }
      if (!response.ok) throw new Error(data.message || 'Não foi possível carregar os veículos.');
      return data;
    });
  }

  function load() {
    fetch('api/vehicles.php', { credentials: 'same-origin' }).then(parseResponse).then(function (data) {
      if (window.jQuery && window.jQuery.fn.DataTable && window.jQuery.fn.DataTable.isDataTable('#zctb')) window.jQuery('#zctb').DataTable().destroy();
      body.replaceChildren();
      data.vehicles.forEach(function (vehicle, index) {
        var row = document.createElement('tr');
        var title = document.createElement('strong');
        title.textContent = vehicle.VehiclesTitle || '';
        if (vehicle.SeriesVersion) {
          var version = document.createElement('small');
          version.className = 'text-muted';
          version.textContent = ' (' + vehicle.SeriesVersion + ')';
          title.appendChild(version);
        }
        var cells = [index + 1, title, vehicle.BrandName, vehicle.LicensePlate || 'N/D', vehicle.Category || 'Geral', vehicle.Transmission || 'Manual', vehicle.FuelType, (vehicle.PricePerDay || '') + ' MT'];
        cells.forEach(function (value) {
          var cell = document.createElement('td');
          if (value instanceof Node) cell.appendChild(value);
          else cell.textContent = value == null ? '' : String(value);
          row.appendChild(cell);
        });
        var statusCell = document.createElement('td');
        var status = document.createElement('span');
        var unavailable = vehicle.StatusAvailability === 'Não Disponível';
        status.className = unavailable ? 'label label-danger' : 'label label-success';
        status.textContent = unavailable ? 'Indisponível' : 'Disponível';
        statusCell.appendChild(status);
        row.appendChild(statusCell);
        var actions = document.createElement('td');
        var edit = document.createElement('a');
        edit.href = 'vehicle-form.html?id=' + encodeURIComponent(vehicle.id);
        edit.title = 'Editar';
        edit.className = 'btn btn-xs btn-primary';
        edit.innerHTML = '<i class="fa fa-edit"></i> Editar';
        actions.appendChild(edit);
        var remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'btn btn-xs btn-danger';
        remove.title = 'Eliminar';
        remove.innerHTML = '<i class="fa fa-close"></i>';
        remove.addEventListener('click', function () {
          if (!window.confirm('Deseja realmente eliminar este veículo?')) return;
          var form = new FormData();
          form.set('action', 'delete');
          form.set('id', vehicle.id);
          fetch('api/vehicles.php', { method: 'POST', body: form, credentials: 'same-origin' }).then(parseResponse).then(function (result) { alert(result.message); load(); }).catch(function (error) { alert(error.message); });
        });
        actions.appendChild(document.createTextNode(' '));
        actions.appendChild(remove);
        row.appendChild(actions);
        body.appendChild(row);
      });
      if (window.jQuery && window.jQuery.fn.DataTable) window.jQuery('#zctb').DataTable();
    }).catch(function (error) { if (error.message !== 'Sessão administrativa necessária.') alert(error.message); });
  }

  load();
})();