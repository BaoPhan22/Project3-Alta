<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
Chào {{ $name }},
<br><br>
Hệ thống vừa ghi nhận bạn thanh toán số tiền <strong><em>{{ $price }}vnđ</em></strong> cho
<strong>{{ $quantity }}
    {{ $ticket_name }}</strong> ngày <strong>{{ $date_order }}</strong>
<br><br>
@for ($i = 1; $i <= (int) $quantity; $i++)
    <div class="card" style='width:18rem'>
        <div class="card-body">
            {!! QrCode::generate($string_to_qr) !!}
            <p>{{ $string_to_qr }}</p>
            <p>{{ $ticket_name }}</p>
            <p>---</p>
            <p>Ngày sử dụng: {{ $date_order }}</p>
        </div>
    </div>
@endfor
<br><br>

Chúng tôi chân thành cám ơn bạn đã sử dụng dịch vụ
