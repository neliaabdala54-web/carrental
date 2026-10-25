(function () {
  'use strict';

  if (window.loadAdminShell) window.loadAdminShell().catch(function (error) { console.error(error); });
  var parameters = new URLSearchParams(window.location.search);
  var setting = parameters.get('setting') || 'pages';
  var pageType = parameters.get('type') || '';
  var form = document.getElementById('settings-form');
  var status = document.getElementById('settings-status');
  var pageNames = { terms: 'Termos e Condições', privacy: 'Política de Privacidade', aboutus: 'Sobre Nós', faqs: 'Perguntas Frequentes (FAQs)' };

  function parseResponse(response) {
    return response.json().then(function (data) {
      if (response.status === 401) { window.location.replace('index.html'); throw new Error('Sessão administrativa necessária.'); }
      if (!response.ok) throw new Error(data.message || 'Não foi possível carregar as configurações.');
      return data;
    });
  }

  function showStatus(message, success) {
    status.className = success ? 'succWrap' : 'errorWrap';
    status.textContent = (success ? 'SUCESSO: ' : 'ERRO: ') + message;
    status.hidden = false;
  }

  if (setting === 'contact') {
    document.title = 'Portal de Aluguer de Carros | Atualizar Contactos';
    document.getElementById('settings-title').textContent = 'Atualizar Informações de Contacto';
    document.getElementById('settings-panel-title').textContent = 'Dados de Contacto (Moçambique)';
    document.querySelectorAll('.contact-field').forEach(function (field) { field.hidden = false; });
    document.querySelectorAll('#page-select-group, #selected-page-group, #page-detail-group').forEach(function (field) { field.hidden = true; });
    fetch('api/settings.php?setting=contact', { credentials: 'same-origin' }).then(parseResponse).then(function (data) {
      document.getElementById('contact-address').value = data.contact.Address || '';
      document.getElementById('contact-email').value = data.contact.EmailId || '';
      document.getElementById('contact-phone').value = data.contact.ContactNo || '';
      form.hidden = false;
    }).catch(function (error) { if (error.message !== 'Sessão administrativa necessária.') showStatus(error.message, false); });
  } else if (setting === 'pages') {
    document.title = 'Portal de Aluguer de Carros | Gerir Páginas';
    document.getElementById('settings-title').textContent = 'Gerir Páginas Institucionais';
    document.getElementById('settings-panel-title').textContent = 'Editar Conteúdo de Páginas';
    document.querySelectorAll('.contact-field').forEach(function (field) { field.hidden = true; });
    document.getElementById('page-type').value = pageType;
    document.getElementById('page-type').addEventListener('change', function () {
      if (this.value) window.location.href = 'settings.html?setting=pages&type=' + encodeURIComponent(this.value);
    });
    if (pageNames[pageType]) {
      fetch('api/settings.php?setting=pages&type=' + encodeURIComponent(pageType), { credentials: 'same-origin' }).then(parseResponse).then(function (data) {
        document.getElementById('selected-page-name').textContent = pageNames[pageType];
        document.getElementById('page-detail').value = data.page.detail || '';
        form.hidden = false;
      }).catch(function (error) { if (error.message !== 'Sessão administrativa necessária.') showStatus(error.message, false); });
    } else {
      form.hidden = false;
    }
  } else {
    window.location.replace('dashboard.html');
    return;
  }

  form.addEventListener('submit', function (event) {
    event.preventDefault();
    var data = new FormData(form);
    data.set('setting', setting);
    if (setting === 'pages') data.set('type', pageType);
    fetch('api/settings.php', { method: 'POST', body: data, credentials: 'same-origin' }).then(parseResponse).then(function (result) {
      showStatus(result.message, true);
    }).catch(function (error) { showStatus(error.message, false); });
  });
})();