<nav class="row m-0">
    <div class="col-3 d-flex flex-row-reverse">
        <img src="https://res.cloudinary.com/dpobeimdp/image/upload/v1688358524/Little_Little_Logo_ngang_1_tpifr4.svg"
            alt="logo" width="50%">
    </div>
    <div class="col-6 d-flex justify-content-evenly align-items-center">
        <a href="/" class="link-custom {{ Route::currentRouteNamed('index') ? 'active' : '' }}">Trang chủ</a>
        <a href="{{ route('event') }}"
            class="link-custom {{ Route::currentRouteNamed('event', 'event.detail') ? 'active' : '' }}">Sự
            kiện</a>
        <a href="{{ route('contact') }}"
            class="link-custom {{ Route::currentRouteNamed('contact') ? 'active' : '' }}">Liên hệ</a>
    </div>
    <div class="col-3 d-flex align-items-center">
        <p class="mb-0 fw-bold">
            <i class="bi bi-telephone me-1"></i>0933366163
        </p>
    </div>
</nav>
