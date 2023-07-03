@extends('layouts.master')
@section('title', $event->name)
@section('content')
    <img
        src="https://res.cloudinary.com/dpobeimdp/image/upload/v1688368403/Frame_2_fhqoym.svg"style="position: absolute; top: 2%; left: 0;">
    <img
        src="https://res.cloudinary.com/dpobeimdp/image/upload/v1688368400/Frame_1_dikjyb.svg"style="position: absolute; top: 2%; right: 0;">

    <p class="page-title tilte-custom">{{ $event->name }}</p>
    <div style="background-image: url('https://res.cloudinary.com/dpobeimdp/image/upload/v1688368433/Frame_e21asu.svg');background-position-x: center;
    background-size: 100% 100%;
    background-repeat: no-repeat;
    object-fit: cover; "
        class="d-flex justify-content-center align-items-center content-container">
        <div class="row w-75 mt-5">
            <div id="index-content">
                <div class="row">
                    <div class="col-4 card p-0" style="background: none; border: none">
                        <img src="{{ $event->imgUrl }}" class="card-img-top rounded-3" width="100%">
                        <p class="card-text mt-3 mb-0" style="font-size: 1.1rem"><i
                                class="bi bi-calendar3 text-primary me-2"></i>
                            {{ date('d/m/Y', strtotime($event->start)) }}-{{ date('d/m/Y', strtotime($event->end)) }}</p>
                        <p class="card-text fs-6 my-0">{{ $event->location }}</p>
                        <p class="card-text fs-3 mt-0 text-primary fw-bold">{{ number_format($event->price, 0, ',', '.') }}
                            VNĐ
                        </p>
                    </div>
                    <div class="col-8">{{ $event->detail }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
