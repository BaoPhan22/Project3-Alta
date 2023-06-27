@extends('layouts.master')
@section('title', 'Trang chủ')
@section('content')
    <div class="container">
        {{-- @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif --}}
        <form action="{{ route('beforepay') }}" method="post">
            @csrf
            <select name="ticket" id="ticket" class="form-control mt-3">
                @foreach ($tickets as $item)
                    <option value="{{ $item->id }}"> {{ $item->name }} </option>
                @endforeach
            </select>
            <input class="form-control mt-3" placeholder="Số lượng vé" type="number" name="quantity" id="quantity" required>
            @error('quantity')
                <em class="text-danger">
                    {{ $message }}
                </em>
            @enderror
            <input class="form-control mt-3" placeholder="Ngày đặt vé" type="date" name="date_order" id="date_order"
                required>
            @error('date_order')
                <em class="text-danger">
                    {{ $message }}
                </em>
            @enderror
            <input class="form-control mt-3" placeholder="Họ và tên" type="text" name="name" id="name" required>
            <input class="form-control mt-3" placeholder="Số điện thoại" type="text" name="phone" id="phone"
                required>
            @error('phone')
                <em class="text-danger">
                    {{ $message }}
                </em>
            @enderror
            <input class="form-control mt-3" placeholder="Email" type="email" name="email" id="email" required>
            <button class="btn btn-primary mt-3" type="submit">Đặt vé</button>
        </form>
    </div>
    <script src="{{ asset('js/checkDateIndex.js') }}"></script>
@endsection
