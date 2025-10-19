<?php

namespace App\Services;

use PayPay\OpenPaymentAPI\Client;
use PayPay\OpenPaymentAPI\Models\CreateQrCodePayload;
use PayPay\OpenPaymentAPI\Models\MoneyAmount;
use Illuminate\Support\Facades\Log;
use Exception;

class PayPayService
{
    protected $client;

    /**
     * PayPayServiceのコンストラクタ
     */
    public function __construct()
    {
        $this->client = new Client(
            [
                'API_KEY' => config('services.paypay.api_key'),
                'API_SECRET' => config('services.paypay.api_secret'),
                'MERCHANT_ID' => config('services.paypay.merchant_id')
            ],
            config('services.paypay.production_mode', false)
        );
    }

    /**
     * QRコード決済を作成する
     *
     * @param string $merchantPaymentId
     * @param int $amount
     * @param string $orderDescription
     * @return array|null
     */
    public function createQrCode(string $merchantPaymentId, int $amount, string $orderDescription = 'qmealでのご注文'): ?array
    {
        try {
            $payload = new CreateQrCodePayload();
            $payload->setMerchantPaymentId($merchantPaymentId)
                ->setAmount((new MoneyAmount())->setAmount($amount)->setCurrency('JPY'))
                ->setCodeType('ORDER_QR')
                ->setOrderDescription($orderDescription)
                ->setIsAuthorization(false);

            $response = $this->client->code->createQRCode($payload);

            if (isset($response['resultInfo']['code']) && $response['resultInfo']['code'] === 'SUCCESS') {
                return $response['data'];
            } else {
                Log::error('PayPay QR Code creation failed.', ['response' => $response]);
                return null;
            }
        } catch (Exception $e) {
            Log::error('PayPay API exception: ' . $e->getMessage());
            return null;
        }
    }
}