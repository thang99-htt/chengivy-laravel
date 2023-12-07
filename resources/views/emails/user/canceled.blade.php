<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Chengivy Store - Hủy đơn hàng của bạn!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 24px;
            color: #333333;
        }

        strong {
            font-weight: bold;
        }

        .message {
            margin-top: 20px;
            display: flex;
            flex-wrap: wrap;
        }

        .product {
            background-color: #f7f7f7;
            padding: 10px;
            margin-bottom: 10px;
            margin-right: 20px;
            border-radius: 10px; 
            width: 250px;
        }

        img {
            max-width: 150px;
            height: 230px;
            display: block;
            margin: 0 auto;
        }

        .name {
            font-size: 17px;
            margin-top: 10px;
            color: #4e4d47;
            margin-bottom: 5px;
            text-align: center;
        }

        .product-price {
            text-align: center;
        }

        .quantity {
            color: #e71700;
            color: #e71700;
            font-size: 17px;
            margin-right: 10px;
        }

        .price {
            color: #898d8c;
            font-size: 17px;
        }

        .cta-button {
            display: block;
            text-align: center;
            margin-top: 20px;
        }

        .cta-button a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #FF5733;
            color: #ffffff;
            text-decoration: none;
            font-size: 18px;
            border-radius: 5px;
        }

        .text-primary {
            color: #0000b7;
            font-size: 17px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Đơn hàng của bạn đã bị hủy!</h1>
        <p>Xin chào, <strong>{{ $userName }}!</strong></p>
        <p>Chengivy Store kính gửi đến Quý khách hàng thông báo đơn hàng 
            <span class="text-primary">{{ $order->id }}</span> được bạn đặt vào ngày 
            <span class="text-primary">{{ $order->ordered_at }}</span> đã bị hủy.
        </p>
        <p>Do số lượng trong kho không đủ đáp ứng cho đơn hàng của bạn, chúng tôi thành thật xin lỗi vì điều này.</p>
        <div class="message">
            @foreach ($productsCanceled as $item)
            <div class="product">
                <img src="{{ $message->embed($item->product->product_image[0]['image']) }}" alt="{{ $item->product->name }}">
                <p class="name">{{ $item->product->name }}</p>
                <p class="name">Phân loại: {{ $item->color }}, {{ $item->size }}</p>
                <p class="product-price">
                    <span class="price">{{ $item->quantity }} x </span>
                    <span class="price"> {{ number_format($item->price, 0, ',', '.') }} đ</span>
                </p>
            </div>
            @endforeach
        </div>
        <div>
            <p>Mọi thắc mắc cần giải đáp Quý khách vui lòng liên hệ Trung tâm Chăm sóc Khách hàng – Chengivy Store tại (+84) 222 666 8888 hoặc gửi mail theo địa chỉ hi@chengivy.com để được hỗ trợ.</p>
            <p>Xin trân trọng cảm ơn Quý khách hàng.</p>
            <p>Thư này được gửi từ địa chỉ mail không chấp nhận mail đến. Vui lòng không trả lời thư này./.</p>
        </div>
        <div class="cta-button">
            <a href="http://localhost:3000/products/all">Vui lòng lựa chọn sản phẩm khác!</a>
        </div>
    </div>
</body>
</html>
