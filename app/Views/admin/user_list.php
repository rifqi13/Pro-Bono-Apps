<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<section class="contact section light-background">
  <div class="container" data-aos="fade-up">
    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success alert-dismissible fade show"><?= session()->getFlashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger alert-dismissible fade show"><?= session()->getFlashdata('error') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
  </div>
</section>

<div class="page-title light-background">
  <div class="container">
    <h1>Manajemen User</h1>
    <nav class="breadcrumbs">
      <ol>
        <li><a href="<?= base_url('advokat/dashboard') ?>">Dashboard</a></li>
        <li class="current">Manajemen User</li>
      </ol>
    </nav>
  </div>
</div>

<section class="contact section light-background">
  <div class="container" data-aos="fade-up">
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <form method="get" class="row g-2">
          <div class="col-md-3"><input type="text" name="q" class="form-control form-control-sm" placeholder="Cari nama/email/NIA" value="<?= esc($filters['q']) ?>"></div>
          <div class="col-md-2">
            <select name="role" class="form-select form-select-sm">
              <option value="">Semua Role</option>
              <?php foreach (['advokat', 'cabang', 'admin', 'super_admin'] as $r): ?>
                <option value="<?= $r ?>" <?= $filters['role'] === $r ? 'selected' : '' ?>><?= ucfirst($r) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2">
            <select name="status" class="form-select form-select-sm">
              <option value="">Semua Status</option>
              <?php foreach (['pending', 'aktif', 'nonaktif', 'suspend'] as $s): ?>
                <option value="<?= $s ?>" <?= $filters['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2">
            <select name="cabang_id" class="form-select form-select-sm">
              <option value="">Semua Cabang</option>
              <?php foreach ($cabangList as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $filters['cabang_id'] == $c['id'] ? 'selected' : '' ?>><?= esc($c['nama']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2"><button class="btn btn-primary btn-sm w-100">Filter</button></div>
        </form>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>Nama</th>
                <th>NIA</th>
                <th>Email</th>
                <th>Role</th>
                <th>Cabang</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($list as $u): ?>
                <tr>
                  <td><?= esc($u['nama_lengkap']) ?><?= $u['gelar'] ? ', ' . esc($u['gelar']) : '' ?></td>
                  <td><?= esc($u['nia'] ?? '-') ?></td>
                  <td><?= esc($u['email']) ?></td>
                  <td><span class="badge bg-secondary"><?= esc($u['role']) ?></span></td>
                  <td><?= esc($u['cabang_nama'] ?? '-') ?></td>
                  <td>
                    <?php $badge = match ($u['status']) {
                      'aktif' => 'success',
                      'pending' => 'warning',
                      'suspend' => 'danger',
                      default => 'secondary'
                    }; ?>
                    <span class="badge bg-<?= $badge ?>"><?= esc($u['status']) ?></span>
                  </td>
                  <td>
                    <a href="<?= base_url('admin/users/' . $u['id']) ?>" class="btn btn-sm btn-outline-primary">Detail</a>
                    <?php if ($u['status'] === 'pending'): ?>
                      <form method="post" action="<?= base_url('admin/users/' . $u['id'] . '/activate') ?>" class="d-inline"><?= csrf_field() ?>
                        <button class="btn btn-sm btn-success">Aktifkan</button>
                      </form>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?= $pager->links() ?>
      </div>
    </div>
  </div>
</section>



<?= $this->endSection() ?>