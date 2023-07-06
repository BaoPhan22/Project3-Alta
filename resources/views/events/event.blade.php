@extends('layouts.master')
@section('title', 'Sự kiện nổi bật')
@section('content')
    <img
        src="https://res.cloudinary.com/dpobeimdp/image/upload/v1688368403/Frame_2_fhqoym.svg"style="position: absolute; top: 2%; left: 0;">
    <img
        src="https://res.cloudinary.com/dpobeimdp/image/upload/v1688368400/Frame_1_dikjyb.svg"style="position: absolute; top: 2%; right: 0;">

    <p class="page-title tilte-custom">Sự kiện nổi bật</p>
    <div style="background-image: url('https://res.cloudinary.com/dpobeimdp/image/upload/v1688368433/Frame_e21asu.svg');background-position-x: center;
    background-size: 100% 100%;
    background-repeat: no-repeat;
    object-fit: cover; "
        class="d-flex justify-content-center align-items-center content-container">
        <div class="row w-75 mt-5">
            @foreach ($events as $item)
                <div class="card col-3 p-0 mx-2"
                    style="box-shadow: rgba(0, 0, 0, 0.16) 0px 10px 36px 0px, rgba(0, 0, 0, 0.06) 0px 0px 0px 1px; border: none">
                    <img src="{{ $item->imgUrl }}" class="card-img-top" width="100%">
                    <div class="card-body">
                        <h5 class="card-title fw-bold fs-4">{{ $item->name }}</h5>
                        <p class="card-text fs-6">{{ $item->location }}</p>
                        <p class="card-text" style="font-size: 1.1rem"><i
                                class="bi bi-calendar3 text-primary me-2"></i>{{ date('d/m/Y', strtotime($item->start)) }} -
                            {{ date('d/m/Y', strtotime($item->end)) }}</p>
                        <p class="card-text fs-3 text-primary fw-bold">{{ number_format($item->price, 0, ',', '.') }} VNĐ
                        </p>
                        <a href="{{ route('event.detail', $item->id) }}" class="btn btn-primary">Xem chi tiết</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
