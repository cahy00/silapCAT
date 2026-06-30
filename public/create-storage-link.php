<?php
/**
 * Script untuk membuat Storage Link di Shared Hosting (cPanel)
 * yang memblokir fungsi exec() dan symlink().
 *
 * CARA PAKAI:
 * 1. Upload file ini ke folder public/ di hosting Anda
 * 2. Akses via browser: https://silapcat2.bkn14.com/create-storage-link.php
 * 3. HAPUS file ini setelah berhasil (demi keamanan)
 */

echo '<h1>🔗 Storage Link Creator</h1>';
echo '<pre>';

$publicStoragePath = __DIR__ . '/storage';
$targetPath = __DIR__ . '/../storage/app/public';

// Cek apakah folder target ada
if (!is_dir($targetPath)) {
    echo "❌ ERROR: Folder storage/app/public tidak ditemukan.\n";
    echo "   Path yang dicek: $targetPath\n";
    die('</pre>');
}

echo "📂 Target: $targetPath\n";
echo "📂 Link  : $publicStoragePath\n\n";

// Jika sudah ada symlink atau folder storage di public
if (file_exists($publicStoragePath)) {
    if (is_link($publicStoragePath)) {
        echo "✅ Symlink sudah ada dan aktif!\n";
        echo "   Mengarah ke: " . readlink($publicStoragePath) . "\n";
        die('</pre>');
    } else {
        echo "⚠️ Folder/file 'storage' sudah ada di public/ tapi bukan symlink.\n";
        echo "   Menghapus terlebih dahulu...\n";
        if (is_dir($publicStoragePath)) {
            // Hapus folder kosong
            @rmdir($publicStoragePath);
            if (is_dir($publicStoragePath)) {
                echo "❌ Gagal menghapus folder storage yang sudah ada. Hapus manual lewat File Manager cPanel.\n";
                die('</pre>');
            }
        } else {
            @unlink($publicStoragePath);
        }
        echo "   ✅ Berhasil dihapus.\n\n";
    }
}

// === METODE 1: Coba symlink() langsung ===
echo "🔧 Metode 1: Mencoba symlink()...\n";
if (function_exists('symlink')) {
    $absoluteTarget = realpath($targetPath);
    if ($absoluteTarget && @symlink($absoluteTarget, $publicStoragePath)) {
        echo "✅ BERHASIL! Symlink dibuat dengan symlink().\n";
        echo "\n🎉 Storage link aktif! Dokumen sekarang bisa diakses.\n";
        echo "\n⚠️ PENTING: Hapus file create-storage-link.php ini dari hosting Anda!\n";
        die('</pre>');
    }
    echo "   ❌ symlink() gagal (mungkin diblokir hosting).\n\n";
} else {
    echo "   ❌ Fungsi symlink() tidak tersedia.\n\n";
}

// === METODE 2: Coba link() (hard link - hanya untuk file, bukan folder) ===
// Hard link tidak bisa untuk folder, jadi skip ke metode 3.

// === METODE 3: Buat .htaccess redirect ===
echo "🔧 Metode 2: Membuat folder storage/ dengan .htaccess redirect...\n";

// Buat folder public/storage
if (@mkdir($publicStoragePath, 0755, true)) {
    // Buat .htaccess di dalamnya yang meredirect ke route fallback Laravel
    $htaccessContent = <<<'HTACCESS'
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /storage/

    # Redirect semua request ke Laravel route handler
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ /index.php?storage_path=$1 [QSA,L]
</IfModule>
HTACCESS;

    if (file_put_contents($publicStoragePath . '/.htaccess', $htaccessContent)) {
        echo "   ✅ Folder storage/ dan .htaccess berhasil dibuat.\n\n";
    }
}

// === METODE 3: Copy seluruh file dari storage/app/public ke public/storage ===
echo "🔧 Metode 3: Menyalin file dari storage/app/public ke public/storage...\n";

function copyDir($src, $dst) {
    $count = 0;
    if (!is_dir($dst)) {
        @mkdir($dst, 0755, true);
    }
    $dir = opendir($src);
    while (($file = readdir($dir)) !== false) {
        if ($file === '.' || $file === '..' || $file === '.gitignore') continue;
        $srcPath = $src . '/' . $file;
        $dstPath = $dst . '/' . $file;
        if (is_dir($srcPath)) {
            $count += copyDir($srcPath, $dstPath);
        } else {
            if (copy($srcPath, $dstPath)) {
                $count++;
                echo "   📄 Copied: $file\n";
            }
        }
    }
    closedir($dir);
    return $count;
}

$copied = copyDir($targetPath, $publicStoragePath);

if ($copied > 0) {
    echo "\n✅ BERHASIL! $copied file berhasil disalin ke public/storage/.\n";
    echo "\n🎉 Dokumen sekarang bisa diakses langsung!\n";
    echo "\n⚠️ CATATAN PENTING:\n";
    echo "   1. HAPUS file create-storage-link.php ini dari hosting Anda!\n";
    echo "   2. Setiap kali Anda mengupload dokumen baru lewat admin panel,\n";
    echo "      Anda perlu menjalankan script ini lagi ATAU gunakan metode\n";
    echo "      fallback route yang sudah ada di routes/web.php.\n";
} else {
    echo "\n⚠️ Tidak ada file untuk disalin (folder storage/app/public mungkin kosong).\n";
    echo "   Tapi folder public/storage/ sudah dibuat.\n";
    echo "   File yang diupload lewat admin panel langsung ke storage/app/public/.\n";
    echo "   Gunakan fallback route di routes/web.php untuk mengaksesnya.\n";
}

echo "\n⚠️ PENTING: Hapus file create-storage-link.php ini dari hosting Anda setelah selesai!\n";
echo '</pre>';
