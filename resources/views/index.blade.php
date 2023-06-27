@extends('layouts.master')
@section('title', 'Trang chủ')
@section('content')
    <div class="container">
        <form action="{{ route('beforepay') }}" method="post">
            @csrf
            <select name="ticket" id="ticket" class="form-control mb-3">
                @foreach ($tickets as $item)
                    <option value="{{ $item->id }}"> {{ $item->name }} </option>
                @endforeach
            </select>
            <input class="form-control mb-3" placeholder="Số lượng vé" type="number" name="quantity" id="quantity" required>
            <input class="form-control mb-3" placeholder="Ngày đặt vé" type="date" name="date_order" id="date_order"
                required>
            <input class="form-control mb-3" placeholder="Họ và tên" type="text" name="name" id="name" required>
            <input class="form-control mb-3" placeholder="Số điện thoại" type="text" name="phone" id="phone"
                required>
            <input class="form-control mb-3" placeholder="Email" type="email" name="email" id="email" required>
            <button class="btn btn-primary" type="submit">Đặt vé</button>
        </form>
    </div>
    <script src="{{ asset('js/checkDateIndex.js') }}"></script>
@endsection
