<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function createPayment(Request $request)
    {
        $response = Http::post('https://test-payment.momo.vn/v2/gateway/api/create', [
            'partnerCode' => 'MOMO_PARTNER_CODE',
            'accessKey' => 'MOMO_ACCESS_KEY',
            'requestId' => 'ORDER_ID',
            'amount' => $request->total_price,
            'orderInfo' => 'Order description',
            'notifyUrl' => 'YOUR_NOTIFY_URL',
            'returnUrl' => 'YOUR_RETURN_URL',
            'extraData' => '',
        ]);

        return response()->json($response->json());
    }

    public function notify(Request $request)
    {
        // Xử lý phản hồi từ Momo
        $response = $request->all();

        // Kiểm tra thông tin thanh toán và cập nhật trạng thái đơn hàng
        if ($response['resultCode'] == '0') {
            // Thành công
        } else {
            // Thất bại
        }

        return response()->json(['message' => 'Payment status received']);
    }
}