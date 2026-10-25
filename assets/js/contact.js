(function () {
  'use strict';

  var form = document.getElementById('contact-form');
  var status = document.getElementById('contact-status');
  if (!form || !status) return;

  form.addEventListener('submit', function (event) {
    event.preventDefault();
    fetch('api/contact-submit.php', {
      method: 'POST',
      body: new FormData(form),
      credentials: 'same-origin'
    }).then(function (response) {
      return response.json().then(function (result) {
        if (!response.ok) throw new Error(result.message);
        return result;
      });
    }).then(function (result) {
      status.className = 'succWrap';
      status.textContent = 'SUCESSO: ' + result.message;
      status.hidden = false;
      form.reset();
    }).catch(function (error) {
      status.className = 'errorWrap';
      status.textContent = 'ERRO: ' + (error.message || 'Ocorreu um erro ao enviar a mensagem.');
      status.hidden = false;
    });
  });

  fetch('api/site-context.php', { credentials: 'same-origin' })
    .then(function (response) { return response.json(); })
    .then(function (context) {
      var container = document.getElementById('contact-information');
      context.contacts.forEach(function (contact) {
        [
          ['fa-map-marker', 'Localização', contact.Address, false],
          ['fa-envelope-o', '', contact.EmailId, 'mailto:'],
          ['fa-phone', '', contact.ContactNo, 'tel:']
        ].forEach(function (entry) {
          if (!entry[2]) return;
          var item = document.createElement('li');
          var iconBox = document.createElement('div');
          iconBox.className = 'icon_wrap';
          var icon = document.createElement('i');
          icon.className = 'fa ' + entry[0];
          icon.setAttribute('aria-hidden', 'true');
          iconBox.appendChild(icon);
          item.appendChild(iconBox);
          var detail = document.createElement('div');
          detail.className = 'contact_info_m';
          if (entry[3]) {
            var link = document.createElement('a');
            link.href = entry[3] + encodeURIComponent(entry[2]);
            link.textContent = entry[2];
            detail.appendChild(link);
          } else {
            var label = document.createElement('strong');
            label.textContent = entry[1] + ': ';
            detail.appendChild(label);
            detail.appendChild(document.createTextNode(entry[2]));
          }
          item.appendChild(detail);
          container.appendChild(item);
        });
      });
    })
    .catch(function (error) { console.error(error); });
})();