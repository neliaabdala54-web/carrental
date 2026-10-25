(function () {
  'use strict';

  function loadPartial(selector, path) {
    var mount = document.querySelector(selector);
    if (mount && mount.children.length) return Promise.resolve();

    return fetch(path, { credentials: 'same-origin' })
      .then(function (response) {
        if (!response.ok) throw new Error('Não foi possível carregar ' + path);
        return response.text();
      })
      .then(function (html) {
        mount = document.querySelector(selector);
        if (mount) mount.innerHTML = html;
      });
  }

  function addContact(container, icon, label, value, href) {
    if (!value) return;
    var paragraph = document.createElement('p');
    paragraph.style.cssText = 'color: #ccc; margin-bottom: 12px;';
    var iconElement = document.createElement('i');
    iconElement.className = 'fa ' + icon;
    iconElement.style.cssText = 'color: #fa2823; margin-right: 8px;';
    paragraph.appendChild(iconElement);
    var title = document.createElement('strong');
    title.textContent = label + ':';
    paragraph.appendChild(title);
    paragraph.appendChild(document.createElement('br'));
    var content = document.createElement(href ? 'a' : 'span');
    content.textContent = value;
    content.style.color = '#fff';
    if (href) content.href = href + encodeURIComponent(value);
    paragraph.appendChild(content);
    container.appendChild(paragraph);
  }

  function renderUserMenu(user) {
    var container = document.getElementById('user-login');
    if (!container || !user) return;
    container.replaceChildren();
    var dropdown = document.createElement('div');
    dropdown.className = 'dropdown';
    dropdown.style.cssText = 'height: 36px; display: flex; align-items: center;';
    var toggle = document.createElement('a');
    toggle.href = '#';
    toggle.setAttribute('data-toggle', 'dropdown');
    toggle.setAttribute('aria-haspopup', 'true');
    toggle.setAttribute('aria-expanded', 'false');
    toggle.style.cssText = 'color: #0f172a; font-weight: 800; font-size: 13.5px; display: flex; align-items: center; gap: 6px; text-decoration: none; height: 36px; padding: 0 14px; background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 6px; text-transform: none; box-shadow: 0 2px 8px rgba(0,0,0,0.08);';
    toggle.innerHTML = '<i class="fa fa-user-circle" aria-hidden="true" style="font-size:17px;color:#fa2823"></i>';
    var name = document.createElement('span');
    name.textContent = user.FullName || 'A minha conta';
    toggle.appendChild(name);
    toggle.insertAdjacentHTML('beforeend', '<i class="fa fa-angle-down" aria-hidden="true" style="color:#334155"></i>');
    dropdown.appendChild(toggle);
    var menu = document.createElement('ul');
    menu.className = 'dropdown-menu dropdown-menu-right';
    menu.style.cssText = 'margin-top:5px;border-radius:6px;border:1px solid #cbd5e1;box-shadow:0 10px 25px rgba(0,0,0,0.12);padding:8px 0;';
    [
      ['account.html?view=profile', 'user', 'Definições do Perfil'],
      ['account.html?view=password', 'key', 'Atualizar Senha'],
      ['account.html?view=bookings', 'car', 'Minhas Reservas'],
      ['account.html?view=post-testimonial', 'pencil', 'Publicar Depoimento'],
      ['account.html?view=testimonials', 'comments', 'Meus Depoimentos']
    ].forEach(function (entry) {
      var item = document.createElement('li');
      var link = document.createElement('a');
      link.href = entry[0];
      link.style.cssText = 'text-transform:none;color:#0f172a;font-weight:700;padding:8px 18px;';
      link.innerHTML = '<i class="fa fa-' + entry[1] + '" style="color:#fa2823;margin-right:8px"></i> ' + entry[2];
      item.appendChild(link);
      menu.appendChild(item);
    });
    menu.insertAdjacentHTML('beforeend', '<li class="divider"></li><li><a href="logout.php" style="color:#dc2626;font-weight:800;text-transform:none;padding:8px 18px"><i class="fa fa-power-off" style="margin-right:8px"></i> Sair</a></li>');
    dropdown.appendChild(menu);
    container.appendChild(dropdown);
  }

  Promise.all([
    loadPartial('#site-switcher', 'partials/switcher.html'),
    loadPartial('#site-header', 'partials/header.html'),
    loadPartial('#site-footer', 'partials/footer.html'),
    loadPartial('#auth-modals', 'partials/auth-modals.html')
  ]).then(function () {
    return fetch('api/site-context.php', { credentials: 'same-origin' });
  }).then(function (response) {
    if (!response.ok) throw new Error('Não foi possível carregar os dados de sessão.');
    return response.json();
  }).then(function (context) {
    if (window.location.hash === '#loginform' && window.jQuery) {
      window.jQuery('#loginform').modal('show');
    }
    renderUserMenu(context.user);
    var contacts = document.getElementById('site-contact');
    if (contacts && context.contacts.length) {
      context.contacts.forEach(function (contact) {
        addContact(contacts, 'fa-map-marker', 'Localização', contact.Address);
        addContact(contacts, 'fa-envelope', 'Suporte por E-mail', contact.EmailId, 'mailto:');
        addContact(contacts, 'fa-phone', 'Linha de Apoio', contact.ContactNo, 'tel:');
      });
    }
    var year = document.getElementById('site-year');
    if (year) year.textContent = context.year;
  }).catch(function (error) {
    console.error(error);
  });

  document.addEventListener('submit', function (event) {
    if (event.target.id !== 'newsletter-form') return;
    event.preventDefault();
    fetch('api/subscribe.php', {
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
      event.target.reset();
    }).catch(function (error) {
      alert(error.message || 'Ocorreu um erro. Por favor tente novamente.');
    });
  });
})();