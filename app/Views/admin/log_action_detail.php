<table class="table table-sm">
  <tr><th width="30%">Waktu</th><td><?= date('d M Y H:i:s', strtotime($log['created_at'])) ?></td></tr>
  <tr><th>User ID</th><td><?= esc($log['user_id'] ?? '-') ?></td></tr>
  <tr><th>Role</th><td><span class="badge bg-secondary"><?= esc($log['user_role'] ?? 'guest') ?></span></td></tr>
  <tr><th>Action</th><td><code><?= esc($log['action']) ?></code></td></tr>
  <tr><th>Module</th><td><?= esc($log['module']) ?></td></tr>
  <tr><th>Deskripsi</th><td><?= nl2br(esc($log['description'])) ?></td></tr>
  <tr><th>Subject</th><td><?= esc($log['subject_type'] ?? '-') ?> #<?= esc($log['subject_id'] ?? '-') ?></td></tr>
  <tr><th>IP Address</th><td><code><?= esc($log['ip_address']) ?></code></td></tr>
  <tr><th>User Agent</th><td style="font-size:12px; word-break:break-all;"><?= esc($log['user_agent']) ?></td></tr>
  <?php if (!empty($log['old_values'])): ?>
    <tr><th>Data Lama</th><td><pre style="font-size:11px; background:#f8fafc; padding:8px; border-radius:4px; max-height:200px; overflow:auto;"><?= esc(json_encode(json_decode($log['old_values']), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre></td></tr>
  <?php endif; ?>
  <?php if (!empty($log['new_values'])): ?>
    <tr><th>Data Baru</th><td><pre style="font-size:11px; background:#f0fdf4; padding:8px; border-radius:4px; max-height:200px; overflow:auto;"><?= esc(json_encode(json_decode($log['new_values']), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre></td></tr>
  <?php endif; ?>
</table>