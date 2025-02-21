<div id="carouselExampleInterval" class="carousel slide p-1" data-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active" data-interval="10000">
            <img src="{{ asset('storage/img/slider1.webp') }}" class="d-block w-100 rounded" alt="slider1">
            <div class="title-overlay" style="background-image: url('{{ asset('storage/img/slider1.webp') }}');"></div>
        </div>
        <div class="carousel-item" data-interval="10000">
            <img src="{{ asset('storage/img/slider2.webp') }}" class="d-block w-100 rounded" alt="slider2">
            <div class="title-overlay" style="background-image: url('{{ asset('storage/img/slider2.webp') }}');"></div>
        </div>
        <div class="carousel-item" data-interval="10000">
            <img src="{{ asset('storage/img/slider3.webp') }}" class="d-block w-100 rounded" alt="slider3">
            <div class="title-overlay" style="background-image: url('{{ asset('storage/img/slider3.webp') }}');"></div>
        </div>
        <div class="carousel-item" data-interval="10000">
            <img src="{{ asset('storage/img/slider4.webp') }}" class="d-block w-100 rounded" alt="slider4">
            <div class="title-overlay" style="background-image: url('{{ asset('storage/img/slider4.webp') }}');"></div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-slide="prev" data-target="#carouselExampleInterval">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    </button>
    <button class="carousel-control-next" type="button" data-slide="next" data-target="#carouselExampleInterval">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
    </button>
</div>

<style>
.carousel-control-prev-icon,
.carousel-control-next-icon {
    filter: invert(1);
}
</style>
