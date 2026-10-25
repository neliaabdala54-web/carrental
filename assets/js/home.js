(function () {
  'use strict';

  function addText(parent, tagName, className, value) {
    var element = document.createElement(tagName);
    if (className) element.className = className;
    element.textContent = value == null ? '' : String(value);
    parent.appendChild(element);
    return element;
  }

  function addIconItem(list, iconName, value) {
    var item = document.createElement('li');
    var icon = document.createElement('i');
    icon.className = 'fa ' + iconName;
    icon.setAttribute('aria-hidden', 'true');
    item.appendChild(icon);
    item.appendChild(document.createTextNode(value == null ? '' : String(value)));
    list.appendChild(item);
  }

  function renderVehicle(vehicle) {
    var column = document.createElement('div');
    column.className = 'col-list-3';

    var card = document.createElement('div');
    card.className = 'recent-car-list';
    card.style.borderRadius = '0';
    column.appendChild(card);

    var imageBox = document.createElement('div');
    imageBox.className = 'car-info-box';
    card.appendChild(imageBox);

    var detailsUrl = 'vehicle-details.html?vhid=' + encodeURIComponent(vehicle.id);
    var imageLink = document.createElement('a');
    imageLink.href = detailsUrl;
    imageBox.appendChild(imageLink);

    var image = document.createElement('img');
    image.src = 'admin/img/vehicleimages/' + encodeURIComponent(vehicle.Vimage1 || '');
    image.className = 'img-responsive';
    image.alt = 'image';
    imageLink.appendChild(image);

    var specifications = document.createElement('ul');
    imageBox.appendChild(specifications);
    addIconItem(specifications, 'fa-car', vehicle.FuelType);
    addIconItem(specifications, 'fa-calendar', vehicle.ModelYear);
    addIconItem(specifications, 'fa-user', vehicle.SeatingCapacity);

    var titleBlock = document.createElement('div');
    titleBlock.className = 'car-title-m';
    card.appendChild(titleBlock);
    var heading = document.createElement('h6');
    titleBlock.appendChild(heading);
    var titleLink = addText(heading, 'a', '', vehicle.BrandName + ' , ' + vehicle.VehiclesTitle);
    titleLink.href = detailsUrl;
    if (vehicle.SeriesVersion) {
      addText(titleLink, 'small', 'text-muted', ' (' + vehicle.SeriesVersion + ')');
    }
    addText(titleBlock, 'span', 'price', vehicle.PricePerDay + ' MT / Dia');

    var information = document.createElement('div');
    information.className = 'inventory_info_m';
    card.appendChild(information);
    var labels = document.createElement('p');
    labels.style.marginBottom = '5px';
    information.appendChild(labels);

    if (vehicle.Category) addLabel(labels, vehicle.Category, 'label label-default');
    if (vehicle.Transmission) addLabel(labels, vehicle.Transmission, 'label label-info');
    if (vehicle.StatusAvailability) {
      var isUnavailable = vehicle.StatusAvailability === 'Não Disponível';
      var status = addLabel(labels, isUnavailable ? 'Indisponível' : 'Disponível', 'label label-default');
      status.style.backgroundColor = '#888888';
      status.style.color = '#ffffff';
      status.style.fontWeight = 'normal';
      var statusIcon = document.createElement('i');
      statusIcon.className = isUnavailable ? 'fa fa-times-circle' : 'fa fa-check-circle-o';
      statusIcon.setAttribute('aria-hidden', 'true');
      statusIcon.style.fontSize = '10px';
      statusIcon.style.marginRight = '3px';
      status.insertBefore(statusIcon, status.firstChild);
    }
    addText(information, 'p', '', String(vehicle.VehiclesOverview || '').substring(0, 70));
    return column;
  }

  function addLabel(parent, text, className) {
    var label = addText(parent, 'span', className, text);
    label.style.marginRight = '3px';
    return label;
  }

  function renderTestimonial(testimonial) {
    var item = document.createElement('div');
    item.className = 'testimonial-m';
    var content = document.createElement('div');
    content.className = 'testimonial-content';
    item.appendChild(content);
    var heading = document.createElement('div');
    heading.className = 'testimonial-heading';
    content.appendChild(heading);
    addText(heading, 'h5', '', testimonial.FullName);
    addText(heading, 'p', '', testimonial.Testimonial);
    return item;
  }

  fetch('api/home-data.php', { credentials: 'same-origin' })
    .then(function (response) {
      if (!response.ok) throw new Error('Falha ao carregar página inicial');
      return response.json();
    })
    .then(function (data) {
      var vehiclesContainer = document.getElementById('recent-cars');
      var testimonialsContainer = document.getElementById('testimonial-slider');
      if (vehiclesContainer) {
        data.vehicles.forEach(function (vehicle) {
          vehiclesContainer.appendChild(renderVehicle(vehicle));
        });
      }
      if (testimonialsContainer) {
        data.testimonials.forEach(function (testimonial) {
          testimonialsContainer.appendChild(renderTestimonial(testimonial));
        });
        if (data.testimonials.length && window.jQuery && window.jQuery.fn.owlCarousel
          && !window.jQuery(testimonialsContainer).data('owl-init')) {
          window.jQuery(testimonialsContainer).owlCarousel({
            itemsCustom: [[0, 1], [450, 1], [550, 1], [767, 1], [991, 2]],
            loop: true,
            nav: true,
            navigation: false,
            autoPlay: 3000
          });
        }
      }
    })
    .catch(function (error) {
      console.error(error);
    });
})();
