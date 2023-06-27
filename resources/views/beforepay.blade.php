@extends('layouts.master')
@section('title', 'Thanh toán')
@section('content')
    <div class="container">
        <form action="{{ route('checkout') }}" method="post">
            @csrf
            <input type="hidden" name="id_user" value="{{ $data['id_user'] }}">
            <div class="row">
                <div class="col-8">
                    <div class="mb-3">
                        <label for="price" class="form-label">Loại vé</label>
                        <input readonly type="text" class="form-control" name="ticket" id="ticket"
                            value="{{ $data['ticket'] }}">
                    </div>
                    <div class="mb-3">
                        <label for="total_price" class="form-label">Số tiền thanh toán</label>
                        <input readonly type="text" class="form-control" name="total_price" id="total_price"
                            value="{{ $data['total'] }}">
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Số lượng vé</label>
                        <input readonly type="text" class="form-control" name="quantity" id="quantity"
                            value="{{ $data['quantity'] }}">
                    </div>
                    <div class="mb-3">
                        <label for="date_order" class="form-label">Ngày sử dụng</label>
                        <input readonly type="date" class="form-control" name="date_order" id="date_order"
                            value="{{ $data['date_order'] }}">
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Thông tin liên hệ</label>
                        <input readonly type="text" class="form-control" name="name" id="name"
                            value="{{ $data['name'] }}">
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Điện thoại</label>
                        <input readonly type="text" class="form-control" name="phone" id="phone"
                            value="{{ $data['phone'] }}">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input readonly type="email" class="form-control" name="email" id="email"
                            value="{{ $data['email'] }}">
                    </div>
                </div>
                <div class="col-4">
                    <div class="mb-3">
                        <label for="card_id" class="form-label">Số thẻ</label>
                        <input type="text" class="form-control" name="" id="card_id">
                    </div>
                    <div class="mb-3">
                        <label for="card_owner_name" class="form-label">Họ tên chủ thẻ</label>
                        <input type="text" class="form-control" name="" id="card_owner_name">
                    </div>
                    <div class="mb-3">
                        <label for="card_end_date" class="form-label">Ngày hết hạn</label>
                        <input type="date" class="form-control" name="" id="card_end_date">
                    </div>
                    <div class="mb-3">
                        <label for="card_cvv_cvc" class="form-label">CVV/CVC</label>
                        <input type="password" class="form-control" name="" id="card_cvv_cvc">
                    </div>
                    <button class="btn btn-primary" type="submit">Thanh toán</button>
                </div>
            </div>

        </form>
    </div>
@endsection