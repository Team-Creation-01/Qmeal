<?php

namespace App\Services;

use PayPay\OpenPaymentAPI\Client;
use PayPay\OpenPaymentAPI\Models\CreateQrCodePayload;
use PayPay\OpenPaymentAPI\Models\OrderItem;
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
                'API_KEY' => config('paypay.api_key'),
                'API_SECRET' => config('paypay.api_secret'),
                'MERCHANT_ID' => config('paypay.merchant_id')
            ],
            config('paypay.production_mode', false)
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

            $client = new Client(
                [
                    'API_KEY' => config('paypay.api_key'),
                    'API_SECRET' => config('paypay.api_secret'),
                    'MERCHANT_ID' => config('paypay.merchant_id')
                ],
                config('paypay.production_mode', false)
            );

            //$items = new OrderItem()->setName("orderName")
            //->setQuantity(1)
            //->setUnitPrice(['amount' => $price, 'currency' => 'JPY']);


            $payload = new CreateQrCodePayload();
            //$payload->setOrderItems($items);
            $payload->setMerchantPaymentId($merchantPaymentId);
            $payload->setAmount(['amount' => $amount, 'currency' => 'JPY']);
            $payload->setCodeType('ORDER_QR');
            $payload->setOrderDescription($orderDescription);
            $payload->setRequestedAt();
            $payload->setIsAuthorization(false);

            $response = $client->code->createQRCode($payload);

            //var_dump($response);
            //exit;


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