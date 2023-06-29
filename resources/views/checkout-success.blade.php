@extends('layouts.master')
@section('title', 'Thanh toán thành công')
@section('content')
    <div class="container">
        <h1>Thanh toán thành công</h1>
        @for ($i = 1; $i <= (int) $data['quantity']; $i++)
            <div class="card" style='width:18rem'>
                <div class="card-body">
                    {!! QrCode::generate($data['string_to_qr']) !!}
                    <p>{{ $data['string_to_qr'] }}</p>
                    <p>{{ $data['ticket_name'] }}</p>
                    <p>---</p>
                    <p>Ngày sử dụng: {{ $data['date_order'] }}</p>
                </div>
            </div>
        @endfor
        <form action="{{ route('save') }}" method="post">
            @csrf
            <input type="hidden" name="session_id" value="{{ $_GET['session_id'] }}">
            <input type="hidden" name="string_to_qr" value="{{ $data['string_to_qr'] }}">
            <button class="btn btn-outline-primary" name="mail" type="submit">Gửi mail</button>
            <button class="btn btn-outline-primary" name="save" type="submit">Tải vé</button>
        </form>
        <p>Số lượng vé: {{ $data['quantity'] }}</p>

    </div>
@endsection
