<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            PayPayでお支払い
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8 text-center">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-10">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    PayPayアプリでスキャンしてください
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mt-2">
                    合計金額: {{ number_format($order->total_price) }}円
                </p>

                <div id="qrcode" class="mt-6 flex justify-center"></div>

                <p class="text-sm text-gray-500 mt-6">
                    お支払いが完了すると、自動的に画面が切り替わります...
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script type="text/javascript">
        // (C) コントローラから渡されたQRコード文字列を使ってQR画像を描画
        new QRCode(document.getElementById("qrcode"), {
            text: "{{ $qrCodeString }}",
            width: 256,
            height: 256,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H // エラー訂正レベル
        });

        // (D) 5秒ごとに支払い状況をサーバーに確認（ポーリング）
        const orderId = {{ $order->id }};
        const paymentStatusCheckInterval = setInterval(() => {
            // (E) ポーリング用のURLをたたく
            fetch(`/order/${orderId}/payment-status`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // (F) 支払いステータスが「支払い待ち」以外になったら遷移
                    if (data.status !== '支払い待ち') {
                        clearInterval(paymentStatusCheckInterval); // ポーリングを停止
                        // 引換券ページに自動で遷移
                        window.location.href = "{{ route('vouchers.index') }}";
                    }
                })
                .catch(error => {
                    console.error('Error fetching payment status:', error);
                    // 必要であればポーリング停止などのエラー処理
                    // clearInterval(paymentStatusCheckInterval);
                });
        }, 5000); // 5秒ごと (5000ミリ秒)
    </script>
</x-app-layout>