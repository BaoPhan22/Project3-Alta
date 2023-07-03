@extends('layouts.master')
@section('title', 'Thanh toán thành công')
@section('content')
    <style>
        #bfp-context {
            width: 12%;
            position: absolute;
            bottom: 60px;
            z-index: 1000;
            left: 0;
        }
    </style>
    <p class="page-title tilte-custom">Thanh toán thành công</p>
    <img src="https://res.cloudinary.com/dpobeimdp/image/upload/v1688357585/Alvin_Arnold_Votay1_1_jxd9rr.svg"
        id="bfp-context">

    <div class="row infomation-form-container infomation-form-container1">
        <div id="index-content">
            <div class="row form-item">
                @for ($i = 1; $i <= ((int) $data['quantity'] > 4 ? 4 : (int) $data['quantity']); $i++)
                    <div class='col-3'>
                        <div class="card">
                            <div class="card-body text-center">
                                {!! QrCode::generate($data['string_to_qr']) !!}
                                <p class="fw-bold fs-4 mt-2">{{ $data['string_to_qr'] }}</p>
                                <p class="fw-bold fs-5">{{ $data['ticket_name'] }}</p>
                                <p>---</p>
                                <p class="fs-6">Ngày sử dụng: {{ $data['date_order'] }}</p>
                                <p>✅</p>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>

        </div>
        <p>Số lượng vé: {{ $data['quantity'] }}</p>
        <form action="{{ route('save') }}" method="post">
            @csrf
            <input type="hidden" name="session_id" value="{{ $_GET['session_id'] }}">
            <input type="hidden" name="string_to_qr" value="{{ $data['string_to_qr'] }}">
            <div class="d-flex align-items-center justify-content-center">
                <div class="w-25 row">
                    <button class="btn btn-primary col me-1" name="mail" type="submit">Gửi mail</button>
                    <button class="btn btn-primary col" name="save" type="submit">Tải vé</button>
                </div>
            </div>
        </form>
    </div>

@endsection
