// config/paypay.php
<?php
return [
    'api_key'       => env('PAYPAY_API_KEY'),
    'api_secret'    => env('PAYPAY_API_SECRET'),
    'merchant_id'   => env('PAYPAY_MERCHANT_ID'),
    'is_sandbox'    => env('PAYPAY_SANDBOX_MODE', true),
];