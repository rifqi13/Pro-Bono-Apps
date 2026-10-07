<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  @page { margin: 140px 25px 60px 25px; }
  body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #222; }

  header { position: fixed; top: -120px; left: 0; right: 0; height: 110px; border-bottom: 2px solid #0a1f3c; padding-bottom: 8px; }
  header table { width: 100%; }
  header td { vertical-align: middle; }
  header .logo { width: 70px; }
  header .title { text-align: center; }
  header .title h1 { margin: 0; font-size: 13px; }
  header .title h2 { margin: 2px 0; font-size: 11px; font-weight: normal; }
  header .title p { margin: 2px 0; font-size: 9px; color: #555; }

  footer { position: fixed; bottom: -40px; left: 0; right: 0; height: 30px; text-align: center; font-size: 8px; color: #777; border-top: 1px solid #ccc; padding-top: 5px; }

  h3.judul { text-align: center; font-size: 12px; margin: 0 0 4px 0; }
  p.periode { text-align: center; margin: 0 0 10px 0; font-size: 10px; color: #555; }

  table.data { width: 100%; border-collapse: collapse; }
  table.data th { background: #0a1f3c; color: #fff; padding: 5px 4px; font-size: 8px; text-align: center; border: 1px solid #0a1f3c; }
  table.data td { padding: 4px; border: 1px solid #ccc; font-size: 8px; vertical-align: top; }
  table.data tr:nth-child(even) td { background: #f9fafb; }

  .page-number:after { content: "Halaman " counter(page) " dari " counter(pages); }
</style>
</head>
<body>

<header>
  <table>
    <tr>
      <td class="logo"><img src="<?= base_url('assets/img/Peradi_logo_v3.png') ?>" style="height:60px;"></td>
      <td class="title">
        <h1><?= esc($header1) ?></h1>
        <h2><?= esc($header2) ?></h2>
        <h2><?= esc($header3) ?></h2>
        <p><?= esc($alamat) ?></p>
      </td>
      <td style="width:70px;"></td>
    </tr>
  </table>
</header>

<footer>
  <?= esc($footer) ?> — <span class="page-number"></span>
</footer>

<h3 class="judul"><?= esc($judul) ?></h3>
<p class="periode">Periode: <?= esc($periode) ?></p>

<table class="data">
  <thead>
    <tr>
      <th width="8%">No. Registrasi</th>
      <th width="6%">Tanggal</th>
      <th width="4%">Tahun</th>
      <th width="7%">NIA Advokat</th>
      <th width="10%">Nama Advokat</th>
      <th width="10%">PBH Cabang</th>
      <th width="7%">Jenis Layanan</th>
      <th width="7%">Jenis Perkara</th>
      <th width="14%">Nama Penerima / Peserta</th>
      <th width="4%">Jml</th>
      <th width="7%">Surat Kuasa</th>
      <th width="8%">Tgl Verifikasi</th>
      <th width="8%">Verifikator</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($pengajuan as $p): ?>
    <?php
      $namaPm = '-';
      if (in_array($p['jenis_non_litigasi'] ?? null, ['seminar','penyuluhan'])) {
          $namaPm = ($p['detail']['nama_acara'] ?? '-') . ' (' . ($p['detail']['peserta'] ?? '-') . ')';
      } elseif (!empty($p['penerima_manfaat'])) {
          $names = [];
          foreach ($p['penerima_manfaat'] as $pm) {
              $names[] = $pm['kategori'] === 'anak' ? $pm['nama'] : ($pm['jenis_kelamin'] === 'L' ? 'Tn.' : 'Ny.');
          }
          $namaPm = implode(', ', array_slice($names, 0, 3)) . (count($names) > 3 ? '...' : '');
      }

      $linkSk = '-';
      foreach ($p['dokumen'] ?? [] as $d) {
          if ($d['jenis_dokumen'] === 'surat_kuasa') { $linkSk = base_url('file/dokumen/' . $d['id']); break; }
      }
    ?>
    <tr>
      <td><?= esc($p['no_registrasi']) ?></td>
      <td><?= date('d/m/Y', strtotime($p['created_at'])) ?></td>
      <td><?= esc($p['tahun']) ?></td>
      <td><?= esc($p['advokat_nia'] ?? '-') ?></td>
      <td><?= esc(($p['advokat_nama'] ?? '-') . (!empty($p['advokat_gelar']) ? ', ' . $p['advokat_gelar'] : '')) ?></td>
      <td><?= esc($p['cabang_nama'] ?? '-') ?></td>
      <td><?= esc(ucfirst($p['jenis_layanan']) . ($p['jenis_non_litigasi'] ? ' - ' . ucfirst($p['jenis_non_litigasi']) : '')) ?></td>
      <td><?= esc($p['detail']['jenis_perkara'] ?? '-') ?></td>
      <td><?= esc($namaPm) ?></td>
      <td style="text-align:center;"><?= count($p['penerima_manfaat'] ?? []) ?></td>
      <td><?= $linkSk !== '-' ? '<a href="' . esc($linkSk) . '">Link</a>' : '-' ?></td>
      <td><?= $p['verified_at'] ? date('d/m/Y H:i', strtotime($p['verified_at'])) : '-' ?></td>
      <td><?= esc($p['verifikator_nama'] ?? '-') ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

</body>
</html>