(function () {
  'use strict';

  function appendText(parent, tag, className, text) {
    var element = document.createElement(tag);
    if (className) element.className = className;
    element.textContent = text == null ? '' : String(text);
    parent.appendChild(element);
    return element;
  }

  function vehicleUrl(vehicle) {
    return 'vehicle-details.html?vhid=' + encodeURIComponent(vehicle.id);
  }

  function renderVehicle(vehicle) {
    var item = document.createElement('div');
    item.className = 'product-listing-m gray-bg';
    var imageBox = document.createElement('div');
    imageBox.className = 'product-listing-img';
    var imageLink = document.createElement('a');
    imageLink.href = vehicleUrl(vehicle);
    var image = document.createElement('img');
    image.src = 'admin/img/vehicleimages/' + encodeURIComponent(vehicle.Vimage1 || '');
    image.className = 'img-responsive';
    image.alt = 'Image';
    imageLink.appendChild(image);
    imageBox.appendChild(imageLink);
    item.appendChild(imageBox);

    var content = document.createElement('div');
    content.className = 'product-listing-content';
    var heading = document.createElement('h5');
    var titleLink = appendText(heading, 'a', '', vehicle.BrandName + ' , ' + vehicle.VehiclesTitle);
    titleLink.href = vehicleUrl(vehicle);
    if (vehicle.SeriesVersion) appendText(titleLink, 'small', 'text-muted', ' (' + vehicle.SeriesVersion + ')');
    content.appendChild(heading);
    appendText(content, 'p', 'list-price', vehicle.PricePerDay + ' MT / Dia');

    var specs = document.createElement('ul');
    [
      ['fa-user', vehicle.SeatingCapacity],
      ['fa-calendar', vehicle.ModelYear],
      ['fa-car', vehicle.FuelType],
      ['fa-tags', vehicle.Category],
      ['fa-cog', vehicle.Transmission],
      ['fa-check-circle-o', vehicle.StatusAvailability]
    ].forEach(function (spec, index) {
      if (!spec[1]) return;
      var listItem = document.createElement('li');
      if (index === 5) listItem.style.color = '#777';
      var icon = document.createElement('i');
      icon.className = 'fa ' + spec[0];
      icon.setAttribute('aria-hidden', 'true');
      if (index === 5) icon.style.color = '#888';
      listItem.appendChild(icon);
      listItem.appendChild(document.createTextNode(String(spec[1])));
      specs.appendChild(listItem);
    });
    content.appendChild(specs);
    var details = appendText(content, 'a', 'btn', 'Ver Detalhes ');
    details.href = vehicleUrl(vehicle);
    var arrow = document.createElement('span');
    arrow.className = 'angle_arrow';
    arrow.innerHTML = '<i class="fa fa-angle-right" aria-hidden="true"></i>';
    details.appendChild(arrow);
    item.appendChild(content);
    return item;
  }

  function renderRecent(vehicle) {
    var item = document.createElement('li');
    item.className = 'gray-bg';
    var imageBox = document.createElement('div');
    imageBox.className = 'recent_post_img';
    var imageLink = document.createElement('a');
    imageLink.href = vehicleUrl(vehicle);
    var image = document.createElement('img');
    image.src = 'admin/img/vehicleimages/' + encodeURIComponent(vehicle.Vimage1 || '');
    image.alt = 'image';
    imageLink.appendChild(image);
    imageBox.appendChild(imageLink);
    item.appendChild(imageBox);
    var title = document.createElement('div');
    title.className = 'recent_post_title';
    var link = appendText(title, 'a', '', vehicle.BrandName + ' , ' + vehicle.VehiclesTitle);
    link.href = vehicleUrl(vehicle);
    appendText(title, 'p', 'widget_price', vehicle.PricePerDay + ' MT / Dia');
    item.appendChild(title);
    return item;
  }

  var parameters = new URLSearchParams(window.location.search);
  fetch('api/catalog-data.php' + window.location.search, { credentials: 'same-origin' })
    .then(function (response) {
      if (!response.ok) throw new Error('Falha ao carregar catálogo');
      return response.json();
    })
    .then(function (data) {
      var count = document.getElementById('vehicle-count');
      var listing = document.getElementById('vehicle-list');
      var brands = document.getElementById('brand-filter');
      var recent = document.getElementById('recent-vehicles');
      var heading = document.getElementById('results-heading');
      if (count) count.textContent = data.count;
      if (heading && parameters.has('q')) heading.textContent = 'Resultado da pesquisa por: "' + parameters.get('q') + '"';
      if (listing) data.vehicles.forEach(function (vehicle) { listing.appendChild(renderVehicle(vehicle)); });
      if (brands) data.brands.forEach(function (brand) {
        var option = appendText(brands, 'option', '', brand.BrandName);
        option.value = brand.id;
      });
      if (brands && parameters.has('brand')) brands.value = parameters.get('brand');
      var category = document.getElementById('category-filter');
      var fueltype = document.getElementById('fueltype-filter');
      if (category && parameters.has('category')) category.value = parameters.get('category');
      if (fueltype && parameters.has('fueltype')) fueltype.value = parameters.get('fueltype');
      if (recent) data.recent.forEach(function (vehicle) { recent.appendChild(renderRecent(vehicle)); });
    })
    .catch(function (error) { console.error(error); });
})();