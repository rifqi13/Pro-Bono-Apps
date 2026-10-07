<?= $this->extend('layouts/advokat') ?>

<?= $this->section('styles') ?>
<style>
  .notif-item {
    border-left: 4px solid transparent;
    transition: all 0.2s ease;
    border-radius: 8px;
  }
  .notif-item:hover {
    background: #f8fafc;
  }
  .notif-item.unread {
    background: #eff6ff;
    border-left-color: #3b82f6;
  }
  .notif-item.unread:hover {
    background: #dbeafe;
  }
  .notif-icon {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 18px;
  }
  .notif-icon.info    { background: #dbeafe; color: #1e40af; }
  .notif-icon.success { background: #d1fae5; color: #065f46; }
  .notif-icon.warning { background: #fef3c7; color: #92400e; }
  .notif-icon.danger  { background: #fee2e2; color: #991b1b; }

  .notif-time {
    font-size: 12px;
    color: #6b7280;
  }
  .notif-title {
    font-weight: 600;
    font-size: 14px;
    color: #111827;
  }
  .notif-message {
    font-size: 13px;
    color: #4b5563;
    line-height: 1.5;
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Page Title -->
<div class="page-title light-background">
  <div class="container">
    <h1>Semua Notifikasi</h1>
    <nav class="breadcrumbs">
      <ol>
        <li><a href="<?= base_url('advokat/dashboard') ?>">Dashboard</a></li>
        <li class="current">Notifikasi</li>
      </ol>
    </nav>
  </div>
</div>

<section class="section">
  <div class="container">

    <!-- Header Actions -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
      <div>
        <h5 class="fw-bold mb-0">
          <i class="bi bi-bell-fill text-primary me-2"></i>
          Notifikasi Saya
        </h5>
        <small class="text-muted">
          Total: <?= count($notifs ?? []) ?> notifikasi
        </small>
      </div>
      <form method="post" action="<?= base_url('notifications/mark-all-read') ?>" class="m-0">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-sm btn-outline-primary"
                onclick="return confirm('Tandai semua notifikasi sudah dibaca?')">
          <i class="bi bi-check2-all"></i> Tandai Semua Dibaca
        </button>
      </form>
    </div>

    <!-- Notif List -->
    <div class="card border-0 shadow-sm">
      <div class="card-body p-2">

        <?php if (empty($notifs)): ?>
          <div class="text-center py-5 text-muted">
            <i class="bi bi-bell-slash" style="font-size: 56px; opacity: 0.4;"></i>
            <p class="mt-3 mb-0">Belum ada notifikasi.</p>
          </div>
        <?php else: ?>
          <?php foreach ($notifs as $n): ?>
            <?php
              $isUnread = empty($n['is_read']) || $n['is_read'] == 0;
              $type = $n['type'] ?? 'info';
              $icon = $n['icon'] ?? 'bi-info-circle';
              $link = $n['link'] ?: '#';
            ?>
            <a href="<?= esc($link) ?>"
               class="notif-item <?= $isUnread ? 'unread' : '' ?> d-flex align-items-start gap-3 p-3 text-decoration-none notif-link"
               data-notif-id="<?= esc($n['id']) ?>">

              <!-- Icon -->
              <div class="notif-icon <?= esc($type) ?>">
                <i class="bi <?= esc($icon) ?>"></i>
              </div>

              <!-- Content -->
              <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                  <div class="notif-title">
                    <?php if ($isUnread): ?>
                      <span class="badge bg-primary me-2" style="font-size: 9px;">BARU</span>
                    <?php endif; ?>
                    <?= esc($n['title']) ?>
                  </div>
                  <div class="notif-time">
                    <i class="bi bi-clock me-1"></i>
                    <?= date('d M Y H:i', strtotime($n['created_at'])) ?>
                  </div>
                </div>
                <div class="notif-message mt-1">
                  <?= esc($n['message']) ?>
                </div>
              </div>

              <!-- Arrow -->
              <div class="text-muted align-self-center">
                <i class="bi bi-chevron-right"></i>
              </div>
            </a>
            <hr class="my-1 mx-3">
          <?php endforeach; ?>
        <?php endif; ?>

      </div>
    </div>

    <!-- Pagination -->
    <?php if (!empty($pager)): ?>
      <div class="mt-3">
        <?= $pager->links() ?>
      </div>
    <?php endif; ?>

  </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Mark as read saat notif diklik (event delegation)
document.addEventListener('click', function (e) {
  const link = e.target.closest('.notif-link');
  if (!link) return;

  const notifId = link.dataset.notifId;
  if (!notifId) return;

  // Kalau sudah read, tidak perlu mark lagi
  if (!link.classList.contains('unread')) return;

  const targetUrl = link.href;
  e.preventDefault();

  fetch('<?= base_url('notifications') ?>/' + notifId + '/read', {
    method: 'POST',
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
    }
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      link.classList.remove('unread');
      const badge = link.querySelector('.badge');
      if (badge) badge.remove();
    }
  })
  .catch(err => console.error('Error:', err))
  .finally(() => {
    if (targetUrl && targetUrl !== '#' && !targetUrl.endsWith('#')) {
      window.location.href = targetUrl;
    }
  });
});
</script>
<?= $this->endSection() ?>