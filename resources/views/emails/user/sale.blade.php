<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Chengivy Store - Chào mừng bạn đến với đội ngũ!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            width: auto;
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
            font-size: 18px;
            margin-top: 10px;
            color: #4e4d47;
            margin-bottom: 5px;
            text-align: center;
        }

        .product-price {
            display: flex;
            text-align: center;
        }

        .price-final {
            color: #4e943e;
            font-size: 17px;
            margin-right: 10px;
        }

        .price {
            color: #898d8c;
            font-size: 17px;
            text-decoration: line-through;
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Ưu đãi sản phẩm trong giỏ hàng của bạn!</h1>
        <p>Xin chào, <strong>{{ $userName }}!</strong></p>
        <div class="message">
            @foreach ($productsWithDiscount as $product)
                <div class="product">
                    <img src="{{ $product->image }}" alt="{{ $product->name }}">
                    <p class="name">{{ $product->name }}</p>
                    <p class="product-price">
                        <span class="price-final">{{ number_format($product->price_final, 0, ',', '.') }} VNĐ</span>
                        <span class="price">{{ number_format($product->price, 0, ',', '.') }} VNĐ</span>
                    </p>
                </div>
            @endforeach

        </div>
        <div class="cta-button">
            <a href="http://localhost:3000/customer/favorites">Mua ngay để không bỏ lỡ cơ hội tiết kiệm!</a>
        </div>
    </div>
</body>
</html>
