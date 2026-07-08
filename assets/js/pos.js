/**
 * pos.js - Logika keranjang kasir (POS) untuk modul Penjualan Aksesoris HP.
 * Berkomunikasi dengan endpoint internal /api/product_search (lihat ApiController)
 * lalu mengirim keranjang sebagai JSON melalui hidden input saat form disubmit.
 */
(function () {
  'use strict';

  var cart = [];
  var els = {};
  var searchTimer = null;

  document.addEventListener('DOMContentLoaded', function () {
    var root = document.getElementById('pos-app');
    if (!root) return;

    els.searchInput = document.getElementById('pos-search');
    els.productGrid = document.getElementById('pos-product-grid');
    els.cartItems = document.getElementById('pos-cart-items');
    els.subtotal = document.getElementById('pos-subtotal');
    els.discount = document.getElementById('pos-discount');
    els.total = document.getElementById('pos-total');
    els.paid = document.getElementById('pos-paid');
    els.change = document.getElementById('pos-change');
    els.itemsJson = document.getElementById('pos-items-json');
    els.form = document.getElementById('pos-form');
    els.emptyCartMsg = document.getElementById('pos-empty-cart');
    els.submitBtn = document.getElementById('pos-submit');

    els.searchInput.addEventListener('input', function () {
      clearTimeout(searchTimer);
      var q = els.searchInput.value.trim();
      searchTimer = setTimeout(function () { searchProducts(q); }, 250);
    });
    els.searchInput.addEventListener('keydown', function (e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        var exact = els.productGrid.querySelector('.pos-product-card');
        if (exact) exact.click();
      }
    });

    els.discount.addEventListener('input', renderSummary);
    els.paid.addEventListener('input', renderSummary);

    els.form.addEventListener('submit', function (e) {
      if (cart.length === 0) {
        e.preventDefault();
        alert('Keranjang masih kosong.');
        return;
      }
      var total = calcTotal();
      var paid = parseFloat(els.paid.value || '0');
      if (paid < total) {
        e.preventDefault();
        alert('Jumlah bayar kurang dari total belanja.');
        return;
      }
      els.itemsJson.value = JSON.stringify(cart.map(function (c) {
        return { product_id: c.id, qty: c.qty, price: c.price, cost_price: c.cost_price };
      }));
    });

    searchProducts('');
    renderCart();
  });

  function searchProducts(q) {
    fetch(window.BASE_URL + '/api/product_search?q=' + encodeURIComponent(q), { credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (res) { renderProductGrid(res.data || []); })
      .catch(function () { els.productGrid.innerHTML = '<p class="text-muted">Gagal memuat produk.</p>'; });
  }

  function renderProductGrid(products) {
    if (products.length === 0) {
      els.productGrid.innerHTML = '<p class="text-muted">Produk tidak ditemukan.</p>';
      return;
    }
    els.productGrid.innerHTML = products.map(function (p) {
      return '<button type="button" class="pos-product-card" data-id="' + p.id + '">' +
        '<div class="p-name">' + escapeHtml(p.name) + '</div>' +
        '<div class="p-price">' + window.POS.formatRupiah(p.sale_price) + '</div>' +
        '<div class="p-stock">Stok: ' + p.stock_qty + ' ' + escapeHtml(p.unit) + '</div>' +
        '</button>';
    }).join('');

    els.productGrid.querySelectorAll('.pos-product-card').forEach(function (card, idx) {
      card.addEventListener('click', function () { addToCart(products[idx]); });
    });
  }

  function addToCart(product) {
    if (product.stock_qty <= 0) {
      alert('Stok produk habis.');
      return;
    }
    var existing = cart.find(function (c) { return c.id === product.id; });
    if (existing) {
      if (existing.qty + 1 > product.stock_qty) {
        alert('Jumlah melebihi stok tersedia (' + product.stock_qty + ').');
        return;
      }
      existing.qty += 1;
    } else {
      cart.push({
        id: product.id,
        name: product.name,
        price: parseFloat(product.sale_price),
        cost_price: parseFloat(product.purchase_price || 0),
        stock_qty: product.stock_qty,
        unit: product.unit,
        qty: 1,
      });
    }
    renderCart();
  }

  function changeQty(index, delta) {
    var item = cart[index];
    if (!item) return;
    var newQty = item.qty + delta;
    if (newQty <= 0) {
      cart.splice(index, 1);
    } else if (newQty > item.stock_qty) {
      alert('Jumlah melebihi stok tersedia (' + item.stock_qty + ').');
      return;
    } else {
      item.qty = newQty;
    }
    renderCart();
  }

  function removeItem(index) {
    cart.splice(index, 1);
    renderCart();
  }

  function renderCart() {
    if (cart.length === 0) {
      els.cartItems.innerHTML = '';
      els.emptyCartMsg.style.display = 'block';
      els.submitBtn.disabled = true;
    } else {
      els.emptyCartMsg.style.display = 'none';
      els.submitBtn.disabled = false;
      els.cartItems.innerHTML = cart.map(function (c, i) {
        return '<div class="pos-cart-row">' +
          '<div class="name">' + escapeHtml(c.name) + '<br><span class="text-muted">' + window.POS.formatRupiah(c.price) + '</span></div>' +
          '<button type="button" class="qty-btn" data-act="dec" data-i="' + i + '">-</button>' +
          '<span class="qty-input">' + c.qty + '</span>' +
          '<button type="button" class="qty-btn" data-act="inc" data-i="' + i + '">+</button>' +
          '<button type="button" class="btn btn-sm btn-danger btn-icon" data-act="del" data-i="' + i + '">&times;</button>' +
          '</div>';
      }).join('');

      els.cartItems.querySelectorAll('[data-act]').forEach(function (btn) {
        var i = parseInt(btn.getAttribute('data-i'), 10);
        var act = btn.getAttribute('data-act');
        btn.addEventListener('click', function () {
          if (act === 'inc') changeQty(i, 1);
          else if (act === 'dec') changeQty(i, -1);
          else if (act === 'del') removeItem(i);
        });
      });
    }
    renderSummary();
  }

  function calcSubtotal() {
    return cart.reduce(function (sum, c) { return sum + c.price * c.qty; }, 0);
  }

  function calcTotal() {
    var subtotal = calcSubtotal();
    var discount = parseFloat(els.discount.value || '0');
    return Math.max(subtotal - discount, 0);
  }

  function renderSummary() {
    var subtotal = calcSubtotal();
    var total = calcTotal();
    var paid = parseFloat(els.paid.value || '0');
    els.subtotal.textContent = window.POS.formatRupiah(subtotal);
    els.total.textContent = window.POS.formatRupiah(total);
    els.change.textContent = window.POS.formatRupiah(Math.max(paid - total, 0));
  }

  function escapeHtml(str) {
    var div = document.createElement('div');
    div.textContent = String(str);
    return div.innerHTML;
  }
})();
