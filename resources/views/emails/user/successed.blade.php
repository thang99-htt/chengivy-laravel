<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Chengivy Store - Đặt hàng thành công!</title>
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

        .header img {
            max-width: 300px;
            height: auto;
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
        .address {
            margin-top: 30px;
        }
        .address p {
            text-align: center;
        }
        .header {
            margin: 0 auto;
        }
        .header h1 {
            text-align: center;
        }
        .date {
            font-size: 20px;
            color: #117bbd;
            font-weight: bold;
        }
        .delivery {
            margin-top: 10px;
            margin-bottom: 30px;
        }
        .delivery h2 {
            color: #117bbd;
            margin-bottom: 0;
        }
        .delivery p {
            margin: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse; /* Để đảm bảo viền nằm chặt vào nếu có các ô giảm độ rộng */
        }

        td, th {
            border: 1px solid #ddd;; /* Đường viền có độ rộng 2px, kiểu solid, màu đen */
            padding: 8px; 
        }
        /* Thiết lập độ rộng cho các cột */
        td:nth-child(1),
        th:nth-child(1) {
            width: 10%; /* Độ rộng của cột đầu tiên là 20% */
        }

        td:nth-child(2),
        th:nth-child(2) {
            width: 30%; /* Độ rộng của cột thứ hai là 40% */
        }

        td:nth-child(3),
        th:nth-child(3) {
            width: 20%; /* Độ rộng của cột thứ ba là 30% */
        }

        td:nth-child(4),
        th:nth-child(4) {
            width: 15%; /* Độ rộng của cột thứ ba là 30% */
        }

        td:nth-child(5),
        th:nth-child(5) {
            width: 10%; /* Độ rộng của cột thứ ba là 30% */
        }

        td:nth-child(6),
        th:nth-child(6) {
            width: 15%; /* Độ rộng của cột thứ ba là 30% */
        }
        .img-product {
            width: 100px;
        }
        .text-danger {
            color: red
        }
        .text--through {
            text-decoration: line-through
        }
        .d-flex {
            display: flex;
            align-items: center;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ $message->embed('http://localhost:8000/storage/images/hero/logo.jpg') }}" alt="Thank you">
            <h1>Cảm ơn bạn đã đặt hàng!</h1>
            <img src="{{ $message->embed('http://localhost:8000/storage/images/hero/unnamed.png') }}" alt="Thank you">
        </div>
        <p>Xin chào, <strong>{{ $userName }}!</strong></p>
        <p>Đơn hàng #{{ $orderSuccessed->id }} đã được đặt thành công và chúng tôi đang xử lý
        </p>
        <p>{{ $orderSuccessed->payment_method }}</p>
        <p class="date">[Đơn hàng #{{ $orderSuccessed->id }}] ({{ \Carbon\Carbon::parse($orderSuccessed->ordered_at)->format('F j, Y') }})            )</p>
        <div class="message">
            <table class="table table-bordered">
                <thead>
                    <tr role="row">
                        <th>#</th>
                        <th>Sản phẩm</th>
                        <th>Phân loại</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $start = 1; ?>
                    @foreach ($orderSuccessed->order_product as $item)
                    <tr>
                        <td><?= $start++; ?></td>
                        <td class="d-flex">
                            <img src="{{ $message->embed($item->product->product_image[0]['image']) }}" alt="{{ $item->product->name }}" class="img-product">
                            {{ $item->product->name }}
                        </td>
                        <td>{{ $item->color }}, {{ $item->size }}</td>
                        <?php if ($item->price_discount > 0): ?>
                            <span class="text--through">{{ number_format($item->price, 0, ',', '.') }} đ</span>
                            <br>
                            <span class="text-danger">{{ number_format($item->price-$item->price_discount, 0, ',', '.') }} đ</span>
                        <?php else: ?>
                            <span>{{ number_format($item->price, 0, ',', '.') }} đ</span>
                        <?php endif; ?>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td>{{ number_format($item->price*$item->quantity, 0, ',', '.') }} đ</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" class="text-bold">Tổng giá trị</th>
                        <th>{{ number_format($orderSuccessed->total_value, 0, ',', '.') }}</th>
                    </tr>
                    <tr>
                        <th colspan="5" class="text-bold">Tổng giảm giá</th>
                        <th>{{ number_format($orderSuccessed->total_discount, 0, ',', '.') }}</th>
                    </tr>
                    <tr>
                        <th colspan="5" class="text-bold">Phí vận chuyển</th>
                        <th>{{ number_format($orderSuccessed->fee, 0, ',', '.') }}</th>
                    </tr>
                    <tr>
                        <th colspan="5" class="text-bold">Tổng đơn đặt hàng</th>
                        <th class="text-primary">{{ number_format($orderSuccessed->total_price, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="delivery">
            <h2>Địa chỉ nhận hàng</h2>
            <p>{{ $orderSuccessed->name_receiver }}</p>
            <p>{{ $orderSuccessed->phone_receiver }}</p>
            <p>{{ $orderSuccessed->address_receiver }}</p>
        </div>
        <div>
            <p>Mọi thắc mắc cần giải đáp Quý khách vui lòng liên hệ Trung tâm Chăm sóc Khách hàng – Chengivy Store tại (+84) 222 666 8888 hoặc gửi mail theo địa chỉ hi@chengivy.com để được hỗ trợ.</p>
            <p>Xin trân trọng cảm ơn Quý khách hàng.</p>
            <p>Thư này được gửi từ địa chỉ mail không chấp nhận mail đến. Vui lòng không trả lời thư này./.</p>
        </div>
        <div class="cta-button">
            <a href="http://localhost:3000/customer/purchases">Theo dõi đơn hàng của bạn tại đây</a>
        </div>
        <div class="address">
            <hr>
            <p>CHENGIVY- Nhà phân phối thương hiệu thời trang quốc tế hàng đầu Việt Nam</p>
            <p>Thành viên Tập đoàn Imex Pan Pacific Group (IPPG)</p>
            <p>Tầng 12, Tòa nhà Sonatus, 15 Lê Thánh Tôn, Phường Bến Nghé, Quận 1, Tp.HCM, Việt Nam</p>
        </div>
    </div>
</body>
</html>
