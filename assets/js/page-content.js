(function () {
  'use strict';

  function safeContent(markup) {
    var source = new DOMParser().parseFromString(markup || '', 'text/html').body;
    var allowedTags = new Set(['A', 'B', 'BLOCKQUOTE', 'BR', 'DIV', 'EM', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6', 'I', 'LI', 'OL', 'P', 'SPAN', 'STRONG', 'U', 'UL']);

    function appendSafe(sourceNode, target) {
      if (sourceNode.nodeType === Node.TEXT_NODE) {
        target.appendChild(document.createTextNode(sourceNode.nodeValue));
        return;
      }
      if (sourceNode.nodeType !== Node.ELEMENT_NODE) return;
      if (['SCRIPT', 'STYLE', 'IFRAME', 'OBJECT', 'EMBED', 'SVG', 'MATH'].indexOf(sourceNode.tagName) !== -1) return;

      if (!allowedTags.has(sourceNode.tagName)) {
        Array.prototype.forEach.call(sourceNode.childNodes, function (child) { appendSafe(child, target); });
        return;
      }

      var cleanNode = document.createElement(sourceNode.tagName.toLowerCase());
      if (sourceNode.tagName === 'A') {
        var href = sourceNode.getAttribute('href');
        if (href) {
          try {
            var parsed = new URL(href, window.location.href);
            if (['http:', 'https:', 'mailto:', 'tel:'].indexOf(parsed.protocol) !== -1) {
              cleanNode.setAttribute('href', href);
              if (parsed.origin !== window.location.origin) cleanNode.rel = 'noopener noreferrer';
            }
          } catch (error) {
            cleanNode.removeAttribute('href');
          }
        }
      }
      Array.prototype.forEach.call(sourceNode.childNodes, function (child) { appendSafe(child, cleanNode); });
      target.appendChild(cleanNode);
    }

    var fragment = document.createDocumentFragment();
    Array.prototype.forEach.call(source.childNodes, function (child) { appendSafe(child, fragment); });
    return fragment;
  }

  var type = new URLSearchParams(window.location.search).get('type') || 'aboutus';
  fetch('api/page-data.php?type=' + encodeURIComponent(type), { credentials: 'same-origin' })
    .then(function (response) {
      if (!response.ok) throw new Error('Página não encontrada.');
      return response.json();
    })
    .then(function (page) {
      document.title = 'Portal de Aluguer de Carros | ' + page.title;
      document.querySelectorAll('[data-page-title]').forEach(function (element) {
        element.textContent = page.title;
      });
      var details = document.getElementById('page-detail');
      details.replaceChildren(safeContent(page.detail));
    })
    .catch(function () { window.location.replace('index.html'); });
})();