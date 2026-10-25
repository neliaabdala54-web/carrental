(function () {
  'use strict';

  var views = {
    profile: 'Configurações Gerais',
    password: 'Atualizar senha',
    bookings: 'Minhas Reservas',
    'post-testimonial': 'Publicar Depoimento',
    testimonials: 'Meus Depoimentos'
  };
  var view = new URLSearchParams(window.location.search).get('view') || 'profile';
  if (!views[view]) view = 'profile';
  var content = document.getElementById('account-content');
  var status = document.getElementById('account-status');

  function element(parent, tag, value, className) {
    var node = document.createElement(tag);
    if (className) node.className = className;
    if (value !== undefined && value !== null) node.textContent = String(value);
    parent.appendChild(node);
    return node;
  }

  function field(form, labelText, name, value, type) {
    var group = element(form, 'div', null, 'form-group');
    element(group, 'label', labelText, 'control-label');
    var input = document.createElement(type === 'textarea' ? 'textarea' : 'input');
    input.className = 'form-control white_bg';
    input.name = name;
    input.value = value || '';
    if (name === 'newpassword') input.minLength = 8;
    input.required = ['fullname', 'mobilenumber', 'password', 'newpassword', 'confirmpassword', 'testimonial'].indexOf(name) !== -1;
    if (type === 'textarea') input.rows = 4;
    else input.type = type || 'text';
    group.appendChild(input);
    return input;
  }

  function button(form, label) {
    var group = element(form, 'div', null, 'form-group');
    var submit = element(group, 'button', label + ' ');
    submit.type = 'submit';
    submit.className = 'btn';
    var arrow = element(submit, 'span', null, 'angle_arrow');
    arrow.innerHTML = '<i class="fa fa-angle-right" aria-hidden="true"></i>';
  }

  function showStatus(message, success) {
    status.className = success ? 'succWrap' : 'errorWrap';
    status.textContent = (success ? 'SUCESSO: ' : 'ERRO: ') + message;
    status.hidden = false;
  }

  function renderProfile(user) {
    element(content, 'div', 'Data de Registo - ' + (user.RegDate || ''), 'form-group');
    if (user.UpdationDate) element(content, 'div', 'Última Atualização em - ' + user.UpdationDate, 'form-group');
    var form = document.createElement('form');
    form.dataset.accountAction = 'update-profile';
    field(form, 'Nome Completo', 'fullname', user.FullName);
    var email = field(form, 'Endereço de E-mail', 'emailid', user.EmailId, 'email');
    email.readOnly = true;
    field(form, 'Número de Telefone', 'mobilenumber', user.ContactNo);
    field(form, 'Data de Nascimento (dd/mm/aaaa)', 'dob', user.dob);
    field(form, 'Endereço', 'address', user.Address, 'textarea').required = false;
    field(form, 'País', 'country', user.Country).required = false;
    field(form, 'Cidade', 'city', user.City).required = false;
    button(form, 'Salvar Alterações');
    content.appendChild(form);
  }

  function renderPassword() {
    var form = document.createElement('form');
    form.dataset.accountAction = 'update-password';
    field(form, 'Senha Atual', 'password', '', 'password');
    field(form, 'Nova Senha', 'newpassword', '', 'password');
    field(form, 'Confirmar Nova Senha', 'confirmpassword', '', 'password');
    button(form, 'Atualizar Senha');
    content.appendChild(form);
  }

  function renderTestimonialForm() {
    var form = document.createElement('form');
    form.dataset.accountAction = 'testimonial';
    field(form, 'Escreva seu depoimento', 'testimonial', '', 'textarea');
    button(form, 'Enviar');
    content.appendChild(form);
  }

  function renderBookings(bookings) {
    var list = element(content, 'ul', null, 'vehicle_listing');
    if (!bookings.length) {
      element(list, 'h5', 'Nenhuma reserva encontrada', '').style.cssText = 'text-align:center;color:red;';
      return;
    }
    bookings.forEach(function (booking) {
      var item = element(list, 'li');
      element(item, 'h4', 'Reserva N.º #' + booking.BookingNumber).style.color = 'red';
      var imageBox = element(item, 'div', null, 'vehicle_img');
      var imageLink = element(imageBox, 'a');
      imageLink.href = 'vehicle-details.html?vhid=' + encodeURIComponent(booking.vid);
      var image = document.createElement('img');
      image.src = 'admin/img/vehicleimages/' + encodeURIComponent(booking.Vimage1 || '');
      image.alt = 'image';
      imageLink.appendChild(image);
      var title = element(item, 'div', null, 'vehicle_title');
      var heading = element(title, 'h6');
      var link = element(heading, 'a', booking.BrandName + ' , ' + booking.VehiclesTitle);
      link.href = imageLink.href;
      var dates = element(title, 'p');
      element(dates, 'b', 'De: ');
      dates.appendChild(document.createTextNode(' ' + booking.FromDate + ' '));
      element(dates, 'b', 'Até: ');
      dates.appendChild(document.createTextNode(' ' + booking.ToDate));
      var message = element(title, 'p');
      element(message, 'b', 'Mensagem: ');
      message.appendChild(document.createTextNode(' ' + (booking.message || '')));
      var stateText = Number(booking.Status) === 1 ? 'Confirmada' : Number(booking.Status) === 2 ? 'Cancelada' : 'Pendente';
      var state = element(item, 'div', null, 'vehicle_status');
      element(state, 'a', stateText, 'btn outline btn-xs' + (Number(booking.Status) === 1 ? ' active-btn' : ''));
      var headingInvoice = element(content, 'h5', 'Fatura / Resumo da Reserva');
      headingInvoice.style.color = 'blue';
      var table = element(content, 'table', null, 'table table-bordered');
      table.innerHTML = '<thead><tr><th>Carro</th><th>Data Início</th><th>Data Fim</th><th>Total de Dias</th><th>Preço / Dia</th></tr></thead>';
      var body = document.createElement('tbody');
      var row = document.createElement('tr');
      [booking.VehiclesTitle + ', ' + booking.BrandName, booking.FromDate, booking.ToDate, booking.totaldays + ' dia(s)', booking.PricePerDay + ' MT'].forEach(function (value) { element(row, 'td', value); });
      body.appendChild(row);
      row = document.createElement('tr');
      var totalLabel = element(row, 'th', 'Total a Pagar:');
      totalLabel.colSpan = 4;
      totalLabel.style.textAlign = 'right';
      element(row, 'th', (Number(booking.totaldays) * Number(booking.PricePerDay)) + ' MT');
      body.appendChild(row);
      table.appendChild(body);
      element(content, 'hr');
    });
  }

  function renderTestimonials(testimonials) {
    var list = element(content, 'ul', null, 'vehicle_listing');
    testimonials.forEach(function (testimonial) {
      var item = element(list, 'li');
      var body = element(item, 'div');
      element(body, 'p', testimonial.Testimonial);
      var date = element(body, 'p');
      element(date, 'b', 'Data de Publicação: ');
      date.appendChild(document.createTextNode(' ' + (testimonial.PostingDate || '')));
      var state = element(item, 'div', null, 'vehicle_status');
      element(state, 'a', Number(testimonial.status) === 1 ? 'Aprovado' : 'Aguardando Aprovação', 'btn outline btn-xs' + (Number(testimonial.status) === 1 ? ' active-btn' : ''));
    });
  }

  document.getElementById('account-page-title').textContent = views[view];
  document.getElementById('account-breadcrumb').textContent = views[view];
  document.getElementById('account-content-title').textContent = views[view];
  document.title = 'Portal de Aluguer de Carros | ' + views[view];
  document.querySelectorAll('.profile_nav a').forEach(function (link) {
    if (new URL(link.href).searchParams.get('view') === view) link.parentElement.classList.add('active');
  });

  fetch('api/account.php?view=' + encodeURIComponent(view), { credentials: 'same-origin' })
    .then(function (response) {
      if (response.status === 401) {
        window.location.replace('index.html#loginform');
        throw new Error('Autenticação necessária.');
      }
      if (!response.ok) throw new Error('Não foi possível carregar sua conta.');
      return response.json();
    }).then(function (data) {
      var user = data.user;
      document.getElementById('account-name').textContent = user.FullName || '';
      document.getElementById('account-address').textContent = user.Address || '';
      document.getElementById('account-city').textContent = user.City || '';
      document.getElementById('account-country').textContent = user.Country || '';
      if (view === 'profile') renderProfile(user);
      if (view === 'password') renderPassword();
      if (view === 'post-testimonial') renderTestimonialForm();
      if (view === 'bookings') renderBookings(data.bookings || []);
      if (view === 'testimonials') renderTestimonials(data.testimonials || []);
    }).catch(function (error) {
      if (error.message !== 'Autenticação necessária.') console.error(error);
    });

  document.addEventListener('submit', function (event) {
    var form = event.target.closest('[data-account-action]');
    if (!form) return;
    event.preventDefault();
    var action = form.dataset.accountAction;
    var data = new FormData(form);
    if (action === 'update-password' && data.get('newpassword') !== data.get('confirmpassword')) {
      showStatus('A nova senha e a confirmação de senha não coincidem.', false);
      return;
    }
    data.set('action', action);
    fetch('api/account.php', { method: 'POST', body: data, credentials: 'same-origin' })
      .then(function (response) {
        return response.json().then(function (result) {
          if (!response.ok) throw new Error(result.message);
          return result;
        });
      }).then(function (result) {
        showStatus(result.message, true);
        form.reset();
        if (action === 'update-profile') window.location.reload();
      }).catch(function (error) { showStatus(error.message || 'Não foi possível salvar.', false); });
  });
})();