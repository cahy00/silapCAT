<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\UploadedFile;
use Livewire\Features\SupportFileUploads\FileUploadConfiguration;
use Livewire\Features\SupportFileUploads\FileUploadController;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

$file = UploadedFile::fake()->create('test_document.pdf', 100);
$disk = FileUploadConfiguration::disk();

echo "Disk: " . $disk . "\n";
echo "FileUploadConfiguration::path('/'): " . FileUploadConfiguration::path('/') . "\n";

$storedPath = FileUploadConfiguration::storeTemporaryFile($file, $disk);
echo "Stored Path: " . $storedPath . "\n";

$controller = new FileUploadController();
$result = $controller->validateAndStore([$file], $disk);
echo "Validated and stored result:\n";
var_dump($result);

foreach ($result as $signedPath) {
    echo "Signed path: " . $signedPath . "\n";
    $extracted = TemporaryUploadedFile::extractPathFromSignedPath(str_replace('livewire-file:', '', $signedPath));
    echo "Extracted path: " . var_export($extracted, true) . "\n";
    
    if ($extracted !== false) {
        $tempFile = TemporaryUploadedFile::createFromLivewire($extracted);
        echo "TemporaryUploadedFile path property: " . $tempFile->getFilename() . "\n";
        echo "TemporaryUploadedFile getRealPath: " . $tempFile->getRealPath() . "\n";
        echo "TemporaryUploadedFile exists: " . ($tempFile->exists() ? 'YES' : 'NO') . "\n";
        try {
            echo "TemporaryUploadedFile getSize: " . $tempFile->getSize() . "\n";
        } catch (\Throwable $e) {
            echo "ERROR getting size: " . $e->getMessage() . "\n";
        }
    }
}

echo "\n--- Testing createFromLivewire('livewire-tmp') ---\n";
$badFile = TemporaryUploadedFile::createFromLivewire('livewire-tmp');
echo "badFile result: " . var_export($badFile, true) . "\n";



