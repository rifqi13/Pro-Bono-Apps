/* ============================================================
   FORM DINAMIS — versi MINIMAL & AMAN
   ============================================================ */

// Helper show/hide — pakai inline style biar pasti berhasil
function _show(id) {
  var el = document.getElementById(id);
  if (!el) return;
  el.classList.remove('hidden');
  el.style.display = '';
}

function _hide(id) {
  var el = document.getElementById(id);
  if (!el) return;
  el.classList.add('hidden');
  el.style.display = 'none';
}

var counterPenerima = { litigasi: 0, pendampingan: 0 };
var counterFoto = { sp: 0, pendampingan: 0 };

document.addEventListener('DOMContentLoaded', function () {

  // -------- Toggle Jenis Layanan --------
  document.querySelectorAll('input[name="jenis_layanan"]').forEach(function (r) {
    r.addEventListener('change', function () {
      var v = this.value;
      if (v === 'litigasi') {
        _show('section-litigasi');
        _hide('section-non-litigasi');
        var c = document.getElementById('penerima-container');
        if (c && c.children.length === 0) tambahPenerima('litigasi');
      } else {
        _hide('section-litigasi');
        _show('section-non-litigasi');
        document.querySelectorAll('input[name="jenis_non_litigasi"]').forEach(function (x) { x.checked = false; });
        _hide('section-seminar-penyuluhan');
        _hide('section-pendampingan');
      }
      cekFormValid();
    });
  });

  // -------- Toggle Jenis Non-Litigasi --------
  document.querySelectorAll('input[name="jenis_non_litigasi"]').forEach(function (r) {
    r.addEventListener('change', function () {
      var v = this.value;
      if (v === 'seminar' || v === 'penyuluhan') {
        _show('section-seminar-penyuluhan');
        _hide('section-pendampingan');
        initFotoSP();
      } else if (v === 'pendampingan') {
        _show('section-pendampingan');
        _hide('section-seminar-penyuluhan');
        var c = document.getElementById('penerima-container-pendampingan');
        if (c && c.children.length === 0) tambahPenerima('pendampingan');
        initFotoPendampingan();
      }
      cekFormValid();
    });
  });

  // -------- Form events --------
  var form = document.getElementById('probonoForm');
  if (form) {
    form.addEventListener('input', cekFormValid);
    form.addEventListener('change', cekFormValid);
  }

  cekFormValid();
});

// -------- Penerima Manfaat --------
function tambahPenerima(ctx, data) {
  data = data || {};
  counterPenerima[ctx] = (counterPenerima[ctx] || 0) + 1;
  var idx = counterPenerima[ctx];
  var containerId = ctx === 'litigasi' ? 'penerima-container' : 'penerima-container-pendampingan';
  var container = document.getElementById(containerId);
  if (!container) return;

  var div = document.createElement('div');
  div.className = 'penerima-item';
  div.setAttribute('data-context', ctx);
  div.setAttribute('data-index', idx);

  div.innerHTML = ''
    + '<button type="button" class="remove-btn" onclick="hapusPenerima(this)">Hapus</button>'
    + '<div class="row g-2">'
    +   '<div class="col-md-3">'
    +     '<label class="form-label">Usia <span class="text-danger">*</span></label>'
    +     '<input type="number" class="form-control input-usia" name="' + ctx + '_usia[]" min="0" max="120" value="' + (data.usia || '') + '" onchange="toggleKategori(this)">'
    +     '<input type="hidden" name="' + ctx + '_idx[]" value="' + idx + '">'
    +   '</div>'
    +   '<div class="col-md-5 kategori-anak" style="display:none;">'
    +     '<label class="form-label">Nama Anak <span class="text-danger">*</span></label>'
    +     '<input type="text" class="form-control" name="' + ctx + '_nama_anak_' + idx + '">'
    +   '</div>'
    +   '<div class="col-md-5 kategori-dewasa" style="display:none;">'
    +     '<label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>'
    +     '<div class="d-flex gap-3">'
    +       '<div class="form-check"><input class="form-check-input" type="radio" name="' + ctx + '_jk_' + idx + '" value="L" id="' + ctx + '_jk_l_' + idx + '"><label class="form-check-label" for="' + ctx + '_jk_l_' + idx + '">L</label></div>'
    +       '<div class="form-check"><input class="form-check-input" type="radio" name="' + ctx + '_jk_' + idx + '" value="P" id="' + ctx + '_jk_p_' + idx + '"><label class="form-check-label" for="' + ctx + '_jk_p_' + idx + '">P</label></div>'
    +     '</div>'
    +   '</div>'
    +   '<div class="col-md-4 wrapper-pekerjaan" style="display:none;">'
    +     '<label class="form-label">Pekerjaan <span class="text-danger">*</span></label>'
    +     '<input type="text" class="form-control" name="' + ctx + '_pekerjaan_' + idx + '">'
    +   '</div>'
    +   '<div class="col-md-12">'
    +     '<label class="form-label">Scan KTP/KK/Akta <span class="text-danger">*</span></label>'
    +     '<input type="file" class="form-control" name="' + ctx + '_ktp_' + idx + '" accept=".pdf,.jpg,.jpeg,.png">'
    +   '</div>'
    + '</div>';

  container.appendChild(div);
  if (data.usia) toggleKategori(div.querySelector('.input-usia'));
  cekFormValid();
}

function hapusPenerima(btn) {
  var item = btn.closest('.penerima-item');
  var ctx = item.getAttribute('data-context');
  var containerId = ctx === 'litigasi' ? 'penerima-container' : 'penerima-container-pendampingan';
  var container = document.getElementById(containerId);
  if (container.children.length <= 1) { alert('Minimal 1 penerima manfaat.'); return; }
  item.remove();
  cekFormValid();
}

function toggleKategori(input) {
  var item = input.closest('.penerima-item');
  var usia = parseInt(input.value, 10);
  var anak = item.querySelector('.kategori-anak');
  var dewasa = item.querySelector('.kategori-dewasa');
  var kerja = item.querySelector('.wrapper-pekerjaan');

  if (anak) anak.style.display = 'none';
  if (dewasa) dewasa.style.display = 'none';
  if (kerja) kerja.style.display = 'none';

  if (isNaN(usia)) return;
  if (usia < 18) {
    if (anak) anak.style.display = '';
  } else {
    if (dewasa) dewasa.style.display = '';
    if (kerja) kerja.style.display = '';
  }
  cekFormValid();
}

function tambahFoto(ctx) {
  counterFoto[ctx] = (counterFoto[ctx] || 0) + 1;
  var idx = counterFoto[ctx];
  var containerId = ctx === 'sp' ? 'foto-container-sp' : 'foto-container-pendampingan';
  var container = document.getElementById(containerId);
  if (!container) return;

  var div = document.createElement('div');
  div.className = 'foto-item';
  div.innerHTML = ''
    + '<button type="button" class="remove-btn" onclick="hapusFoto(this)">Hapus</button>'
    + '<label class="form-label">Foto ' + idx + ' <span class="text-danger">*</span></label>'
    + '<input type="file" class="form-control" name="bukti_foto_' + ctx + '_' + idx + '" accept="image/*">';

  container.appendChild(div);
  cekFormValid();
}

function hapusFoto(btn) {
  var item = btn.closest('.foto-item');
  var container = item.parentElement;
  var ctx = container.id.indexOf('sp') !== -1 ? 'sp' : 'pendampingan';
  var min = ctx === 'sp' ? 3 : 1;
  if (container.children.length <= min) { alert('Minimal ' + min + ' foto.'); return; }
  item.remove();
  cekFormValid();
}

function initFotoSP() {
  var c = document.getElementById('foto-container-sp');
  if (c && c.children.length === 0) for (var i = 0; i < 3; i++) tambahFoto('sp');
}

function initFotoPendampingan() {
  var c = document.getElementById('foto-container-pendampingan');
  if (c && c.children.length === 0) tambahFoto('pendampingan');
}

function cekFormValid() {
  var btn = document.getElementById('btnSubmit');
  var btnPrev = document.getElementById('btnPreview');
  if (!btn) return false;

  var jenis = document.querySelector('input[name="jenis_layanan"]:checked');
  if (!jenis) { btn.disabled = true; if (btnPrev) btnPrev.disabled = true; return false; }

  var valid = true;

  if (jenis.value === 'litigasi') {
    var items = document.querySelectorAll('#penerima-container .penerima-item');
    if (items.length === 0) valid = false;
    items.forEach(function (it) {
      var u = it.querySelector('.input-usia');
      if (!u.value) valid = false;
      var n = parseInt(u.value);
      if (n < 18) {
        var nama = it.querySelector('.kategori-anak input');
        if (!nama || !nama.value.trim()) valid = false;
      } else {
        var jk = it.querySelector('.kategori-dewasa input[type="radio"]:checked');
        if (!jk) valid = false;
        var kerja = it.querySelector('.wrapper-pekerjaan input');
        if (!kerja || !kerja.value.trim()) valid = false;
      }
      var ktp = it.querySelector('input[type="file"]');
      if (!ktp || !ktp.files.length) valid = false;
    });
    var jp = document.querySelector('[name="jenis_perkara"]');
    if (!jp || !jp.value) valid = false;
  } else {
    var nl = document.querySelector('input[name="jenis_non_litigasi"]:checked');
    if (!nl) valid = false;
  }

  btn.disabled = !valid;
  if (btnPrev) btnPrev.disabled = !valid;
  return valid;
}