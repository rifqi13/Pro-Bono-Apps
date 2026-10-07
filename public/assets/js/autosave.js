/* AUTO-SAVE — versi guard untuk cegah duplikasi */
window.initAutoSave = window.initAutoSave || function (opts) {
  const form = document.getElementById(opts.formId);
  if (!form) return;

  const statusEl = document.getElementById('autosaveStatus');
  const textEl = document.getElementById('autosaveText');
  const pengajuanInput = document.getElementById('pengajuanId');

  const STORAGE_KEY = 'probono_draft_' + (pengajuanInput ? pengajuanInput.value : 'new') || 'new';
  let lastPayload = null;
  let saving = false;

  function setStatus(cls, text) {
    if (statusEl) {
      statusEl.className = 'autosave-status ' + cls;
      if (textEl) textEl.textContent = text;
    }
  }

  function collectPayload() {
    const fd = new FormData(form);
    const payload = {};
    for (const [k, v] of fd.entries()) {
      if (v instanceof File) continue;
      payload[k] = v;
    }
    return payload;
  }

  async function save() {
    if (saving) return;
    const payload = collectPayload();
    const json = JSON.stringify(payload);
    if (json === lastPayload) return;
    lastPayload = json;

    localStorage.setItem(STORAGE_KEY, json);
    saving = true;
    setStatus('saving', 'Menyimpan...');

    try {
      const body = new FormData();
      body.append('pengajuan_id', pengajuanInput ? pengajuanInput.value : '');
      body.append('payload', json);
      body.append(opts.csrfName, opts.csrfHash);

      const res = await fetch(opts.endpoint, { method: 'POST', body });
      const data = await res.json();

      if (data.success) {
        if (pengajuanInput) pengajuanInput.value = data.pengajuan_id;
        localStorage.removeItem(STORAGE_KEY);
        setStatus('saved', 'Tersimpan ' + (data.saved_at || ''));
      } else {
        setStatus('error', 'Gagal simpan: ' + (data.message || ''));
      }
    } catch (e) {
      setStatus('error', 'Gagal simpan (offline?)');
    } finally {
      saving = false;
    }
  }

  setInterval(save, opts.intervalMs || 30000);
  setTimeout(save, 3000);
};