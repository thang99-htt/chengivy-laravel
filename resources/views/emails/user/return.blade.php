<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Chengivy Store - Hoàn trả đơn hàng của bạn!</title>
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
            text-align: center;
        }

        h1 {
            font-size: 24px;
            color: #333333;
        }

        strong {
            font-weight: bold;
        }

        .message {
            margin-top: 10px;
        }

        .product {
            background-color: #f7f7f7;
            padding: 10px;
            margin-bottom: 10px;
            margin-right: 20px;
            border-radius: 10px; 
            width: 250px;
            margin: 0 auto;
        }

        img {
            max-width: 150px;
            height: 230px;
            display: block;
            margin: 0 auto;
        }

        .name {
            font-size: 18px;
            margin-top: 10px;
            color: #4e4d47;
            margin-bottom: 5px;
            text-align: center;
        }

        .product-price {
            text-align: center;
        }

        .price-final {
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
        .text-success {
            color: rgb(0, 173, 0);
        }
        .text-primary {
            color: rgb(0, 63, 173);
        }
        .text-danger {
            color: rgb(206, 2, 2);
            font-size: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Hướng dẫn trả hàng sau khi yêu cầu Trả hàng/Hoàn tiền của bạn được chấp nhận!</h1>
        <p>Xin chào, <strong>{{ $userName }}!</strong></p>
        <p>Chengivy Store kính gửi đến Quý khách hàng thông báo sản phẩm được khách hàng yêu cầu Trả hàng/Hoàn tiền đã được chấp nhận.</p>
        <p>Thông tin Trả hàng/Hoàn tiền:</p>
        <div class="message">
            <div>
                <h3>Đơn hàng #{{ $returnProduct->order_id }}</h3>
                <p>Ngày nhận hàng: {{ $returnProduct->order->receipted_at }}</p>
                <p class="text-success">Ngày yêu cầu hoàn trả: {{ $returnProduct->requested_at }}</p>
                <p class="text-primary">Lý do: {{ $returnProduct->reason }}</p>
                <p>Mô tả: {{ $returnProduct->description }}</p>
            </div>
            <div>
                <h3>Sản phẩm hoàn trả:</h3>
                @foreach ($returnProduct->return_product as $item)
                    <div class="product">
                        <img src="{{ $message->embed($item->product->product_image[0]->image) }}" alt="{{ $item->product->name }}">
                        <p class="name">{{ $item->product->name }}</p>
                        <p class="name">Phân loại: {{ $item->color }}, {{ $item->size }}</p>
                        <p class="product-price">
                            <span class="price">{{ $item->quantity }} x {{ number_format($item->price, 0, ',', '.') }} VNĐ</span>
                        </p>
                    </div>
                @endforeach
            </div>
            <div>
                <p class="text-danger">Số tiền hoàn trả: {{ number_format($returnProduct->total_price, 0, ',', '.') }} VNĐ</p>
                <p>Phương thức thanh toán: {{ $returnProduct->method }}</p>
            </div>
            
        </div>
        <p>Quý khách hàng cần thực hiện vui lòng truy cập đường link bên dưới và làm theo hướng dẫn:</p>
        <div class="cta-button">
            <a href="http://localhost:3000/returns/guide">Chengivy - Trả hàng/Hoàn tiền</a>
        </div>
        <div>
            <p>Mọi thắc mắc cần giải đáp Quý khách vui lòng liên hệ Trung tâm Chăm sóc Khách hàng – Chengivy Store tại (+84) 222 666 8888 hoặc gửi mail theo địa chỉ hi@chengivy.comđể được hỗ trợ.</p>
            <p>Xin trân trọng cảm ơn Quý khách hàng.</p>
            <p>Thư này được gửi từ địa chỉ mail không chấp nhận mail đến. Vui lòng không trả lời thư này./.</p>
        </div>
    </div>
</body>
</html>
