    <!-- Hero Section Start -->
    <section class="hero-section">
        <div id="heroHome4" class="single-hero-slide bg-img-center d-flex align-items-center text-center lazy" data-bg="{{ asset('assets/img/hero_static/' . $img) }}">
            <div class="container">
            <div class="slider-text">
                <span class="small-text" data-animation="fadeInDown" data-delay=".3s">{{ convertUtf8($title) }}</span>
                <h1 data-animation="fadeInLeft" data-delay=".6s">{{ convertUtf8($subtitle) }}</h1>
                <a class="btn filled-btn" href="{{ $btnUrl }}" data-animation="fadeInUp" data-delay=".9s">
                {{ convertUtf8($btnName) }} <i class="far fa-long-arrow-right"></i>
                </a>
            </div>
            </div>
        </div>
      </section>
      <!-- Hero Section End -->
