<?php
/**
 * Direct encryption test - bypasses Laravel
 */

header('Content-Type: application/json');

$results = [];

// Read .env file
$envPath = dirname(__DIR__) . '/.env';
$envContent = file_get_contents($envPath);
preg_match('/^APP_KEY=(.*)$/m', $envContent, $matches);
$appKey = trim($matches[1] ?? '');

$results['env_key'] = [
    'raw' => $appKey,
    'length' => strlen($appKey),
];

// Parse key
if (strpos($appKey, 'base64:') === 0) {
    $base64Part = substr($appKey, 7);
    $decodedKey = base64_decode($base64Part, true);

    $results['parsed_key'] = [
        'base64_part' => $base64Part,
        'base64_length' => strlen($base64Part),
        'decoded_length' => $decodedKey !== false ? strlen($decodedKey) : 'DECODE_FAILED',
        'hex' => $decodedKey !== false ? bin2hex($decodedKey) : 'N/A',
    ];

    // Test encryption directly with OpenSSL
    if ($decodedKey !== false && strlen($decodedKey) === 32) {
        $cipher = 'AES-256-CBC';
        $ivLength = openssl_cipher_iv_length($cipher);
        $iv = random_bytes($ivLength);

        $testData = 'Hello World';
        $encrypted = openssl_encrypt($testData, $cipher, $decodedKey, OPENSSL_RAW_DATA, $iv);

        if ($encrypted !== false) {
            $results['openssl_test'] = 'SUCCESS - encryption works with this key';

            // Try to decrypt
            $decrypted = openssl_decrypt($encrypted, $cipher, $decodedKey, OPENSSL_RAW_DATA, $iv);
            $results['decryption_test'] = ($decrypted === $testData) ? 'SUCCESS' : 'FAILED';
        } else {
            $results['openssl_test'] = 'FAILED: ' . openssl_error_string();
        }
    } else {
        $results['openssl_test'] = 'SKIPPED - key length is not 32 bytes';
    }
} else {
    $results['parsed_key'] = 'Key does not start with base64:';
}

// Now test Laravel's Encrypter class
require dirname(__DIR__) . '/vendor/autoload.php';

try {
    // Create encrypter directly with the key
    $encrypter = new \Illuminate\Encryption\Encrypter($decodedKey, 'AES-256-CBC');
    $results['laravel_encrypter'] = 'SUCCESS - created without error';

    // Test encrypt/decrypt
    $encrypted = $encrypter->encrypt('test');
    $decrypted = $encrypter->decrypt($encrypted);
    $results['laravel_encrypt_test'] = ($decrypted === 'test') ? 'SUCCESS' : 'FAILED';

} catch (Exception $e) {
    $results['laravel_encrypter'] = 'FAILED: ' . $e->getMessage();
}

// Now test through Laravel's config
try {
    $app = require dirname(__DIR__) . '/bootstrap/app.php';
    $configKey = $app->make('config')->get('app.key');

    $results['laravel_config_key'] = [
        'value' => $configKey,
        'length' => strlen($configKey ?? ''),
    ];

    // Parse Laravel's config key
    if (strpos($configKey ?? '', 'base64:') === 0) {
        $b64 = substr($configKey, 7);
        $dec = base64_decode($b64, true);
        $results['laravel_config_key_parsed'] = [
            'base64_length' => strlen($b64),
            'decoded_bytes' => $dec !== false ? strlen($dec) : 'DECODE_FAILED',
        ];
    }

    // Try to get the encrypter from Laravel container
    $encrypter = $app->make('encrypter');
    $results['laravel_container_encrypter'] = 'SUCCESS';

} catch (Exception $e) {
    $results['laravel_config_error'] = $e->getMessage();
}

echo json_encode($results, JSON_PRETTY_PRINT);
