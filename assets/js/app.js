/**
 * app.js - Perilaku UI global (sidebar, dropdown, konfirmasi, alert, filter tabel).
 * Vanilla JavaScript, tanpa dependency eksternal.
 */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    initSidebarToggle();
    initUserMenu();
    initAutoHideAlerts();
    initConfirmForms();
    initTableSearch();
    initModals();
  });

  function initSidebarToggle() {
    var toggle = document.querySelector('[data-sidebar-toggle]');
    var sidebar = document.querySelector('.sidebar');
    var backdrop = document.querySelector('.sidebar-backdrop');
    if (!toggle || !sidebar) return;

    function close() {
      sidebar.classList.remove('open');
      if (backdrop) backdrop.classList.remove('open');
    }
    toggle.addEventListener('click', function () {
      sidebar.classList.toggle('open');
      if (backdrop) backdrop.classList.toggle('open');
    });
    if (backdrop) backdrop.addEventListener('click', close);
  }

  function initUserMenu() {
    var menu = document.querySelector('.user-menu');
    if (!menu) return;
    var btn = menu.querySelector('.user-menu-btn');
    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      menu.classList.toggle('open');
    });
    document.addEventListener('click', function () {
      menu.classList.remove('open');
    });
  }

  function initAutoHideAlerts() {
    document.querySelectorAll('.alert[data-autohide]').forEach(function (el) {
      setTimeout(function () {
        el.style.transition = 'opacity .4s ease';
        el.style.opacity = '0';
        setTimeout(function () { el.remove(); }, 400);
      }, 4000);
    });
  }

  /** Form dengan atribut data-confirm="pesan" akan menampilkan dialog konfirmasi sebelum submit. */
  function initConfirmForms() {
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
      el.addEventListener('submit', function (e) {
        if (!window.confirm(el.getAttribute('data-confirm'))) {
          e.preventDefault();
        }
      });
    });
    document.querySelectorAll('button[data-confirm], a[data-confirm]').forEach(function (el) {
      el.addEventListener('click', function (e) {
        if (!window.confirm(el.getAttribute('data-confirm'))) {
          e.preventDefault();
        }
      });
    });
  }

  /** Input dengan data-table-search="#idTabel" memfilter baris tabel secara live. */
  function initTableSearch() {
    document.querySelectorAll('[data-table-search]').forEach(function (input) {
      var table = document.querySelector(input.getAttribute('data-table-search'));
      if (!table) return;
      input.addEventListener('input', function () {
        var q = input.value.toLowerCase().trim();
        table.querySelectorAll('tbody tr').forEach(function (row) {
          row.style.display = row.textContent.toLowerCase().indexOf(q) > -1 ? '' : 'none';
        });
      });
    });
  }

  /** Modal generik: [data-modal-open="id"] membuka, [data-modal-close] menutup. */
  function initModals() {
    document.querySelectorAll('[data-modal-open]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var modal = document.getElementById(btn.getAttribute('data-modal-open'));
        if (modal) modal.classList.add('open');
      });
    });
    document.querySelectorAll('[data-modal-close]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        btn.closest('.modal-backdrop').classList.remove('open');
      });
    });
  }

  window.POS = window.POS || {};
  window.POS.formatRupiah = function (value) {
    var n = Math.round(Number(value) || 0);
    return 'Rp ' + n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  };
})();
