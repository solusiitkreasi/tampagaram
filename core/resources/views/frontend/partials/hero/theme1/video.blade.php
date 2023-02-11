<!-- Hero Section Start -->
<section class="hero-section">
    <div id="hero-home-5" class="single-hero-slide bg-img-center d-flex align-items-center text-center lazy" data-bg="{{ asset('assets/img/hero_static/' . $img) }}">
        <div id="bgndVideo" data-property="{videoURL:'{{$websiteInfo->hero_video_link}}',containment:'#hero-home-5', quality:'large', autoPlay:true, loop:true, mute:true,playsinline=true, disablePictureInPicture=true, opacity:1}"></div>
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
