(function () {
  'use strict';

  function text(parent, tag, value, className) {
    var element = document.createElement(tag);
    if (className) element.className = className;
    element.textContent = value == null ? '' : String(value);
    parent.appendChild(element);
    return element;
  }

  function icon(parent, name) {
    var element = document.createElement('i');
    element.className = 'fa ' + name;
    element.setAttribute('aria-hidden', 'true');
    parent.appendChild(element);
    return element;
  }

  function vehicleUrl(id) {
    return 'vehicle-details.html?vhid=' + encodeURIComponent(id);
  }

  function render(data) {
    var vehicle = data.vehicle;
    var slider = document.getElementById('listing_img_slider');
    ['Vimage1', 'Vimage2', 'Vimage3', 'Vimage4', 'Vimage5'].forEach(function (field) {
      if (!vehicle[field] || !vehicle[field].trim()) return;
      var frame = document.createElement('div');
      var image = document.createElement('img');
      image.src = 'admin/img/vehicleimages/' + encodeURIComponent(vehicle[field]);
      image.className = 'img-responsive';
      image.alt = vehicle.BrandName + ' ' + vehicle.VehiclesTitle;
      image.width = 900;
      image.height = 560;
      frame.appendChild(image);
      slider.appendChild(frame);
    });
    if (window.jQuery && window.jQuery.fn.owlCarousel) {
      window.jQuery(slider).owlCarousel({
        itemsCustom: [[0, 1], [450, 1], [700, 2], [1024, 3], [1200, 3]],
        loop: true,
        nav: true,
        navigation: true,
        navigationText: ['Anterior', 'Próximo'],
        pagination: false,
        autoPlay: 3000
      });
    }

    var title = document.getElementById('vehicle-title');
    text(title, 'span', vehicle.BrandName + ' , ' + vehicle.VehiclesTitle);
    if (vehicle.SeriesVersion) text(title, 'small', ' (' + vehicle.SeriesVersion + ')');
    document.getElementById('vehicle-price').textContent = vehicle.PricePerDay;

    var features = document.getElementById('vehicle-features');
    [
      ['fa-calendar', vehicle.ModelYear], ['fa-cogs', vehicle.FuelType],
      ['fa-user-plus', vehicle.SeatingCapacity], ['fa-tags', vehicle.Category],
      ['fa-tachometer', vehicle.Transmission], ['fa-id-card-o', vehicle.LicensePlate],
      ['fa-check-circle-o', vehicle.StatusAvailability]
    ].forEach(function (feature, index) {
      if (!feature[1]) return;
      var item = document.createElement('li');
      icon(item, feature[0]);
      var value = text(item, 'h5', feature[1]);
      if (index === 6) {
        value.style.color = '#666';
        item.querySelector('i').style.color = '#888';
      }
      features.appendChild(item);
    });
    text(document.getElementById('vehicle-overview'), 'p', vehicle.VehiclesOverview);

    [
      ['Ar Condicionado', 'AirConditioner'], ['Sistema de Travões ABS', 'AntiLockBrakingSystem'],
      ['Direção Assistida', 'PowerSteering'], ['Vidros Elétricos', 'PowerWindows'],
      ['Leitor de CD / Audio', 'CDPlayer'], ['Estofos em Couro', 'LeatherSeats'],
      ['Fecho Centralizado', 'CentralLocking'], ['Bloqueio de Portas Elétrico', 'PowerDoorLocks'],
      ['Assistência de Travagem', 'BrakeAssist'], ['Airbag do Condutor', 'DriverAirbag'],
      ['Airbag do Passageiro', 'PassengerAirbag'], ['Sensor de Impacto / Colisão', 'CrashSensor']
    ].forEach(function (accessory) {
      var row = document.createElement('tr');
      text(row, 'td', accessory[0]);
      var value = document.createElement('td');
      icon(value, Number(vehicle[accessory[1]]) === 1 ? 'fa-check' : 'fa-close');
      row.appendChild(value);
      document.getElementById('vehicle-accessories').appendChild(row);
    });

    var today = new Date().toISOString().slice(0, 10);
    document.getElementById('fromdate').min = today;
    document.getElementById('fromdate').addEventListener('change', function () {
      document.getElementById('todate').min = this.value || today;
    });
    data.similar.forEach(renderSimilar);

    if (data.user) {
      document.getElementById('booking-form').hidden = false;
      document.getElementById('vehicle-id').value = vehicle.id;
    } else {
      document.getElementById('booking-login').hidden = false;
    }
  }

  function renderSimilar(vehicle) {
    var column = document.createElement('div');
    column.className = 'col-md-3 grid_listing';
    var card = document.createElement('div');
    card.className = 'product-listing-m gray-bg';
    var imageBox = document.createElement('div');
    imageBox.className = 'product-listing-img';
    var link = document.createElement('a');
    link.href = vehicleUrl(vehicle.id);
    var image = document.createElement('img');
    image.src = 'admin/img/vehicleimages/' + encodeURIComponent(vehicle.Vimage1 || '');
    image.className = 'img-responsive';
    image.alt = 'image';
    link.appendChild(image);
    imageBox.appendChild(link);
    card.appendChild(imageBox);
    var content = document.createElement('div');
    content.className = 'product-listing-content';
    var heading = document.createElement('h5');
    var title = text(heading, 'a', vehicle.BrandName + ' , ' + vehicle.VehiclesTitle);
    title.href = link.href;
    content.appendChild(heading);
    text(content, 'p', vehicle.PricePerDay + ' MT / Dia', 'list-price');
    var list = document.createElement('ul');
    list.className = 'features_list';
    [['fa-user', vehicle.SeatingCapacity], ['fa-calendar', vehicle.ModelYear], ['fa-car', vehicle.FuelType]].forEach(function (feature) {
      var item = document.createElement('li');
      icon(item, feature[0]);
      item.appendChild(document.createTextNode(String(feature[1] || '')));
      list.appendChild(item);
    });
    content.appendChild(list);
    card.appendChild(content);
    column.appendChild(card);
    document.getElementById('similar-vehicles').appendChild(column);
  }

  var vehicleId = new URLSearchParams(window.location.search).get('vhid');
  if (!vehicleId) {
    window.location.replace('car-listing.html');
    return;
  }

  fetch('api/vehicle-details-data.php?id=' + encodeURIComponent(vehicleId), { credentials: 'same-origin' })
    .then(function (response) {
      if (!response.ok) throw new Error('Veículo não encontrado.');
      return response.json();
    })
    .then(render)
    .catch(function () { window.location.replace('car-listing.html'); });

  document.addEventListener('submit', function (event) {
    if (event.target.id !== 'booking-form') return;
    event.preventDefault();
    fetch('api/booking-create.php', {
      method: 'POST',
      body: new FormData(event.target),
      credentials: 'same-origin'
    }).then(function (response) {
      return response.json().then(function (result) {
        if (!response.ok) throw new Error(result.message);
        return result;
      });
    }).then(function (result) {
      alert(result.message);
      window.location.href = 'account.html?view=bookings';
    }).catch(function (error) {
      alert(error.message || 'Ocorreu um erro. Por favor tente novamente.');
    });
  });
})();