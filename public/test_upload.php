<?php
$folders = ['ktp', 'dokumen', 'foto', 'surat_kuasa'];
$base = __DIR__ . '/../writable/uploads/';

echo "<h2>Cek Folder Upload</h2>";
echo "<pre>";

foreach ($folders as $f) {
    $path = $base . $f . '/';
    echo "$f:\n";
    
    // Cek ada
    if (is_dir($path)) {
        echo "  ✅ Ada\n";
    } else {
        echo "  ❌ Tidak ada\n";
        continue;
    }
    
    // Cek writable
    $test = $path . 'test.txt';
    if (@file_put_contents($test, 'test')) {
        echo "  ✅ Bisa tulis\n";
        @unlink($test);
    } else {
        echo "  ❌ Tidak bisa tulis (permission!)\n";
    }
    
    // Cek sub-folder
    $sub = $path . date('Y/m/');
    if (!is_dir($sub)) {
        if (@mkdir($sub, 0775, true)) {
            echo "  ✅ Sub-folder dibuat: $sub\n";
        } else {
            echo "  ❌ Gagal bikin sub-folder\n";
        }
    } else {
        echo "  ✅ Sub-folder ada\n";
    }
    echo "\n";
}

echo "</pre>";