(function () {
  'use strict';

  if (window.loadAdminShell) window.loadAdminShell().catch(function (error) { console.error(error); });
  var parameters = new URLSearchParams(window.location.search);
  var id = parameters.get('id') || parameters.get('imgid');
  var form = document.getElementById('vehicle-form');
  var imageNames = ['Vimage1', 'Vimage2', 'Vimage3', 'Vimage4', 'Vimage5'];
  var fields = {
    vehicletitle: 'VehiclesTitle', brandname: 'VehiclesBrand', vehicalorcview: 'VehiclesOverview',
    priceperday: 'PricePerDay', fueltype: 'FuelType', modelyear: 'ModelYear', seatingcapacity: 'SeatingCapacity',
    licenseplate: 'LicensePlate', seriesversion: 'SeriesVersion', category: 'Category', transmission: 'Transmission',
    statusavailability: 'StatusAvailability'
  };
  var features = {
    airconditioner: 'AirConditioner', powerdoorlocks: 'PowerDoorLocks', antilockbrakingsys: 'AntiLockBrakingSystem',
    brakeassist: 'BrakeAssist', powersteering: 'PowerSteering', driverairbag: 'DriverAirbag',
    passengerairbag: 'PassengerAirbag', powerwindow: 'PowerWindows', cdplayer: 'CDPlayer',
    centrallocking: 'CentralLocking', crashcensor: 'CrashSensor', leatherseats: 'LeatherSeats'
  };

  function parseResponse(response) {
    return response.json().then(function (data) {
      if (response.status === 401) { window.location.replace('index.html'); throw new Error('Sessão administrativa necessária.'); }
      if (!response.ok) throw new Error(data.message || 'Não foi possível carregar o veículo.');
      return data;
    });
  }

  function populate(data) {
    var brandSelect = form.elements.brandname;
    data.brands.forEach(function (brand) {
      var option = document.createElement('option');
      option.value = brand.id;
      option.textContent = brand.BrandName;
      brandSelect.appendChild(option);
    });

    if (data.vehicle) {
      document.getElementById('vehicle-form-title').textContent = 'Editar Veículo';
      document.getElementById('vehicle-submit').textContent = 'Guardar Alterações';
      document.title = 'Portal de Aluguer de Carros | Editar Veículo';
      Object.keys(fields).forEach(function (input) { form.elements[input].value = data.vehicle[fields[input]] || ''; });
      Object.keys(features).forEach(function (input) { form.elements[input].checked = Number(data.vehicle[features[input]]) === 1; });
      for (var number = 1; number <= 4; number++) form.elements['img' + number].required = false;
      var preview = document.getElementById('vehicle-current-images');
      imageNames.forEach(function (field, index) {
        if (!data.vehicle[field]) return;
        var column = document.createElement('div');
        column.className = 'col-sm-4';
        var title = document.createElement('p');
        title.textContent = 'Imagem ' + (index + 1);
        var image = document.createElement('img');
        image.src = 'img/vehicleimages/' + encodeURIComponent(data.vehicle[field]);
        image.alt = 'Imagem atual ' + (index + 1);
        image.style.cssText = 'width:100%;max-width:300px;height:200px;object-fit:cover;border:1px solid #000;margin-bottom:5px;';
        column.appendChild(title);
        column.appendChild(image);
        preview.appendChild(column);
      });
    }

    if (window.jQuery && window.jQuery.fn.selectpicker) window.jQuery('.selectpicker').selectpicker('refresh');
  }

  fetch('api/vehicles.php' + (id ? '?id=' + encodeURIComponent(id) : ''), { credentials: 'same-origin' })
    .then(parseResponse).then(populate).catch(function (error) {
      if (error.message !== 'Sessão administrativa necessária.') { alert(error.message); window.location.replace('vehicles.html'); }
    });

  form.addEventListener('submit', function (event) {
    event.preventDefault();
    var data = new FormData(form);
    data.set('action', 'save');
    if (id) data.set('id', id);
    fetch('api/vehicles.php', { method: 'POST', body: data, credentials: 'same-origin' })
      .then(parseResponse).then(function (result) {
        alert(result.message);
        window.location.href = 'vehicles.html';
      }).catch(function (error) { alert(error.message || 'Não foi possível guardar o veículo.'); });
  });
})();