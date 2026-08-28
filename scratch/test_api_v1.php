<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;

function testRoute($uri, $headers = [], $params = []) {
    $request = Request::create($uri, 'GET', $params, [], [], $headers);
    $response = app()->handle($request);
    return [
        'status' => $response->getStatusCode(),
        'json' => json_decode($response->getContent(), true),
    ];
}

echo "=== 1. Test Without API Key (Expecting 401) ===\n";
$res1 = testRoute('/api/v1/events');
echo "Status: " . $res1['status'] . "\n";
echo "Message: " . ($res1['json']['message'] ?? '') . "\n\n";

echo "=== 2. Test With Invalid API Key (Expecting 401) ===\n";
$res2 = testRoute('/api/v1/events', ['HTTP_X_API_KEY' => 'invalid-key']);
echo "Status: " . $res2['status'] . "\n";
echo "Message: " . ($res2['json']['message'] ?? '') . "\n\n";

echo "=== 3. Test GET /api/v1/events With Valid API Key (Expecting 200) ===\n";
$res3 = testRoute('/api/v1/events', ['HTTP_X_API_KEY' => 'silapcat-secret-key-2026']);
echo "Status: " . $res3['status'] . "\n";
echo "Success: " . ($res3['json']['success'] ? 'true' : 'false') . "\n";
echo "Total events: " . ($res3['json']['meta']['total'] ?? 0) . "\n\n";

echo "=== 4. Test GET /api/v1/events/1 ===\n";
$res4 = testRoute('/api/v1/events/1', ['HTTP_X_API_KEY' => 'silapcat-secret-key-2026']);
echo "Status: " . $res4['status'] . "\n";
echo "Event Name: " . ($res4['json']['data']['name'] ?? 'N/A') . "\n\n";

echo "=== 5. Test GET /api/v1/exam-scores ===\n";
$res5 = testRoute('/api/v1/exam-scores', ['HTTP_X_API_KEY' => 'silapcat-secret-key-2026']);
echo "Status: " . $res5['status'] . "\n";
echo "Total exam scores: " . ($res5['json']['meta']['total'] ?? 0) . "\n\n";

echo "=== 6. Test GET /api/v1/employees ===\n";
$res6 = testRoute('/api/v1/employees', ['HTTP_X_API_KEY' => 'silapcat-secret-key-2026']);
echo "Status: " . $res6['status'] . "\n";
echo "Total employees: " . ($res6['json']['meta']['total'] ?? 0) . "\n\n";

echo "=== 7. Test GET /api/v1/institutions ===\n";
$res7 = testRoute('/api/v1/institutions', ['HTTP_X_API_KEY' => 'silapcat-secret-key-2026']);
echo "Status: " . $res7['status'] . "\n";
echo "Total institutions: " . ($res7['json']['meta']['total'] ?? 0) . "\n\n";

echo "=== 8. Test GET /api/v1/documents ===\n";
$res8 = testRoute('/api/v1/documents', ['HTTP_X_API_KEY' => 'silapcat-secret-key-2026']);
echo "Status: " . $res8['status'] . "\n";
echo "Total documents: " . ($res8['json']['meta']['total'] ?? 0) . "\n\n";
