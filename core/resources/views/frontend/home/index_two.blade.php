@extends('frontend.layout')

@section('pageHeading')
  {{ __('Home') }}
@endsection

@php
    $metaKeywords = !empty($seo->meta_keyword_home) ? $seo->meta_keyword_home : '';
    $metaDescription = !empty($seo->meta_description_home) ? $seo->meta_description_home : '';
@endphp
@section('meta-keywords', "{{$metaKeywords}}")
@section('meta-description', "$metaDescription")

@section('content')
  <main>
    @php
        if(!empty($hero)) {
            $img = $hero->img;
            $title = $hero->title;
            $subtitle = $hero->subtitle;
            $btnUrl = $hero->btn_url;
            $btnName = $hero->btn_name;
        } else {
            $img = '';
            $title = '';
            $subtitle = '';
            $btnUrl = '';
            $btnName = '';
        }
    @endphp
    @if ($websiteInfo->home_version == 'static')
        @includeIf('frontend.partials.hero.theme2.static')
    @elseif ($websiteInfo->home_version == 'slider')
        @includeIf('frontend.partials.hero.theme2.slider')
    @elseif ($websiteInfo->home_version == 'video')
        @includeIf('frontend.partials.hero.theme2.video')
    @elseif ($websiteInfo->home_version == 'particles')
        @includeIf('frontend.partials.hero.theme2.particles')
    @elseif ($websiteInfo->home_version == 'water')
        @includeIf('frontend.partials.hero.theme2.water')
    @elseif ($websiteInfo->home_version == 'parallax')
        @includeIf('frontend.partials.hero.theme2.parallax')
    @endif

    @if ($sections->search_section == 1)
    <!-- Booking Search Form Start -->
    <section class="booking-section style-two primary-bg">
      <div class="container-fluid">
        <div class="row no-gutters justify-content-center">
          <div class="col-xl-10">
            <div class="booking-form-wrap">
              <form action="{{ route('rooms') }}" method="GET">
                <div class="bookIng-inner-wrap">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="input-wrap">
                              <input type="text" placeholder="{{ __('Check In / Out Date') }}" id="date-range" name="dates" readonly>
                              <i class="far fa-calendar-alt"></i>
                            </div>
                        </div>

                        <div class="col-lg-2">
                            <div class="input-wrap">
                              <select name="beds" class="nice-select">
                                <option selected disabled>{{ __('Beds') }}</option>

                                @for ($i = 1; $i <= $numOfBed; $i++)
                                  <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                              </select>
                            </div>
                        </div>

                        <div class="col-lg-2">
                          <div class="input-wrap">
                            <select name="baths" class="nice-select">
                              <option selected disabled>{{ __('Baths') }}</option>

                              @for ($i = 1; $i <= $numOfBath; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                              @endfor
                            </select>
                          </div>
                        </div>

                        <div class="col-lg-2">
                          <div class="input-wrap">
                            <select name="guests" class="nice-select">
                              <option selected disabled>{{ __('Guests') }}</option>

                              @for ($i = 1; $i <= $numOfGuest; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                              @endfor
                            </select>
                          </div>
                        </div>

                        <div class="col-lg-3">
                          <div class="input-wrap">
                            <button type="submit" class="btn filled-btn btn-block btn-black">
                              {{ __('search') }} <i class="far fa-long-arrow-right"></i>
                            </button>
                          </div>
                        </div>
                    </div>
                </div>
              </form>

              <div class="booking-shape-1">
                <img class="lazy" data-src="{{ asset('assets/img/shape/01.png') }}" alt="shape">
              </div>
              <div class="booking-shape-2">
                <img class="lazy" data-src="{{ asset('assets/img/shape/06.png') }}" alt="shape">
              </div>
              <div class="booking-shape-3">
                <img class="lazy" data-src="{{ asset('assets/img/shape/07.png') }}" alt="shape">
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- Booking Search Form End -->
    @endif

    <section class="feature-section-two">
        @if ($sections->intro_section == 1)
        <!-- Intro Section Start -->
        <div class="featured-slider position-relative section-padding">
          <div class="container-fluid">
            <div class="row no-gutters">
              <div class="col-xl-10">
                <div class="feature-slide-wrap" id="featureSlideActive">
                  <div class="single-feature-slide">
                    @if (!empty($intro))
                      <img class="lazy f-big-image" data-src="{{ asset('assets/img/intro_section/' . $intro->intro_img) }}" alt="Image">
                    @endif

                    <div class="row no-gutters justify-content-end">
                      <div class="col-xl-5 col-lg-8 col-md-8">
                        <div class="f-desc">
                          <h1>{{ !empty($intro->intro_secondary_title) ? $intro->intro_secondary_title : '' }}</h1>
                          <p>{{ !empty($intro->intro_text) ? $intro->intro_text : '' }}</p>
                          <div class="line"></div>
                        </div>
                      </div>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Intro Section End -->
        @endif

        @if ($sections->faq_section == 1)
        <!-- Why Choose US/FAQ Start -->
        <div class="wcu-section">
          <div class="container">
            <div class="row align-items-center">
              <div class="col-lg-6">
                <!-- Section Title -->
                <div class="section-title">
                  @if (!empty($secHeading))
                    <span class="title-top">{{ convertUtf8($secHeading->faq_section_title) }}</span>
                    <h1>{{ convertUtf8($secHeading->faq_section_subtitle) }}</h1>
                  @endif
                </div>

                @if (count($faqs) > 0)
                  <div class="feature-accordion accordion" id="faqAccordion">
                    @foreach ($faqs as $faq)
                      <div class="card">
                        <div class="card-header ">
                          <button
                            type="button"
                            class="{{ $loop->first ? 'active-accordion' : '' }}"
                            data-toggle="collapse"
                            data-target="{{ '#faq' . $faq->id }}"
                          >
                            {{ $faq->question }}
                            <span class="open-icon"><i class="far fa-eye-slash"></i></span>
                            <span class="close-icon"><i class="far fa-eye"></i></span>
                          </button>
                        </div>

                        <div
                          id="{{ 'faq' . $faq->id }}"
                          class="collapse {{ $loop->first ? 'show' : '' }}"
                          data-parent="#faqAccordion"
                        >
                          <div class="card-body">{{ $faq->answer }}</div>
                        </div>
                      </div>
                    @endforeach
                  </div>
                @endif
              </div>

              <div class="col-lg-6">
                <div class="feature-accordion-img text-right">
                  @if (!empty($secHeading->faq_section_image))
                    <img class="lazy" data-src="{{ asset('assets/img/faq_section/' . $secHeading->faq_section_image) }}" alt="image">
                  @endif

                  <div class="degin-shape">
                    <div class="shape-one">
                      <img class="lazy" data-src="{{ asset('assets/img/shape/11.png') }}" alt="shape">
                    </div>
                    <div class="shape-two">
                      <img class="lazy" data-src="{{ asset('assets/img/shape/12.png') }}" alt="shape">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Why Choose US/FAQ End -->
        @endif
    </section>

    @if ($sections->featured_services_section == 1)
    <!-- Feature Service Section Start -->
    <section class="feature-section section-padding">
      <div class="container">
        <!-- Section Title -->
        <div class="section-title text-center">
          @if (!empty($secHeading))
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <span class="title-top">{{ convertUtf8($secHeading->service_section_title) }}</span>
                    <h1>{{ convertUtf8($secHeading->service_section_subtitle) }}</h1>
                </div>
            </div>
          @endif
        </div>

        <!-- Single Service Box -->
        @if (count($serviceInfos) == 0 || $serviceFlag == 0)
          <div class="row text-center">
            <div class="col">
              <h3>{{ __('No Featured Service Found!') }}</h3>
            </div>
          </div>
        @else
          <div class="row">
            @foreach ($serviceInfos as $serviceInfo)
              @if (!empty($serviceInfo->service))
                <div class="col-lg-4 col-md-6">
                  <div
                    class="single-feature-box text-center wow fadeIn animated"
                    data-wow-duration="1500ms"
                    data-wow-delay="400ms"
                  >
                    <div class="feature-icon">
                      <i class="{{ $serviceInfo->service->service_icon }}"></i>
                    </div>
                    <h4>{{ convertUtf8($serviceInfo->title) }}</h4>
                    <p>{{ $serviceInfo->summary }}</p>
                    @if ($serviceInfo->service->details_page_status == 1)
                      <a href="{{ route('service_details', ['id' => $serviceInfo->service_id, 'slug' => $serviceInfo->slug]) }}" class="read-more">
                        {{ __('read more') }} <i class="far fa-long-arrow-right"></i>
                      </a>
                    @endif
                  </div>
                </div>
              @endif
            @endforeach
          </div>
        @endif
      </div>
    </section>
    <!-- Feature Service Section Start -->
    @endif

    @if ($sections->featured_rooms_section == 1)
    <!-- Latest Room Section Start -->
    <section class="latest-room section-bg section-padding">
      <div class="container-fluid">
        <div class="row align-items-center no-gutters">
          <div class="col-lg-3">
            <!-- Section Title -->
            <div class="section-title">
              @if (!is_null($secHeading))
                <span class="title-top with-border">{{ convertUtf8($secHeading->room_section_title) }}</span>
                <h1>{{ convertUtf8($secHeading->room_section_subtitle) }}</h1>
                <p>{{ $secHeading->room_section_text }}</p>
              @endif
              <!-- Page Info -->
              <div class="page-Info"></div>
              <!-- Room Arrow -->
              <div class="room-arrows"></div>
            </div>
          </div>

          <div class="col-lg-8 offset-lg-1">
            @if (count($roomInfos) == 0 || $roomFlag == 0)
              <h3 class="text-center text-white">{{ __('No Featured Room Found!') }}</h3>
            @else
              <div class="latest-room-slider" id="roomSliderActive">
                @foreach ($roomInfos as $roomInfo)
                  @if (!is_null($roomInfo->room))
                      <div class="single-room">
                        <a class="room-thumb d-block" href="{{route('room_details', [$roomInfo->room_id, $roomInfo->slug])}}">
                          <img class="lazy" data-src="{{ asset('assets/img/rooms/' . $roomInfo->room->featured_img) }}" alt="">
                            <!-- <div class="room-price">
                                <p>{{ $currencyInfo->base_currency_symbol_position == 'left' ? $currencyInfo->base_currency_symbol : '' }} {{ $roomInfo->room->rent }} {{ $currencyInfo->base_currency_symbol_position == 'right' ? $currencyInfo->base_currency_symbol : '' }} / {{__('Night')}}</p>
                            </div> -->
                        </a>
                        <div class="room-desc">
                            @if ($websiteInfo->room_category_status == 1)
                            <div class="room-cat">
                              <a class="d-block p-0" href="{{route('rooms', ['category' => $roomInfo->roomCategory->id])}}">{{ $roomInfo->roomCategory->name }}</a>
                            </div>
                            @endif
                          <h4>
                            <a href="{{ route('room_details', ['id' => $roomInfo->room_id, 'slug' => $roomInfo->slug]) }}">{{ convertUtf8($roomInfo->title) }}</a>
                          </h4>
                          <!-- <p>{{ $roomInfo->summary }}</p> -->
                          <ul class="room-info">
                            <li><i class="far fa-bed"></i>{{ $roomInfo->room->bed }} {{$roomInfo->room->bed == 1 ? __('Bed') : __('Beds')}}</li>
                            <li><i class="far fa-bath"></i>{{ $roomInfo->room->bath }} {{$roomInfo->room->bath == 1 ? __('Bath') : __('Baths')}}</li>
                            @if (!empty($roomInfo->room->max_guests))
                            <li><i class="far fa-users"></i>{{ $roomInfo->room->max_guests }} {{$roomInfo->room->max_guests == 1 ? __('Guest') : __('Guests')}}</li>
                            @endif
                          </ul>
                        </div>
                      </div>
                  @endif
                @endforeach
              </div>
            @endif
          </div>
        </div>
      </div>
    </section>
    <!-- Latest Room Section End -->
    @endif


    @if ($sections->featured_package_section == 1)
    <!-- Package Section Start -->
    <section class="ma-package-section section-padding featured-packages">
      <div class="container">
        <!-- Section Title -->
        <div class="section-title text-center">
          @if (!empty($secHeading))
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <span class="title-top">{{ convertUtf8($secHeading->package_section_title) }}</span>
                    <h1>{{ convertUtf8($secHeading->package_section_subtitle) }}</h1>
                </div>
            </div>
          @endif
        </div>

        <!-- Package Boxes -->
        @if (count($packageInfos) == 0 || $packageFlag == 0)
          <div class="row text-center">
            <div class="col">
              <h3>{{ __('No Featured Package Found!') }}</h3>
            </div>
          </div>
        @else
          <div class="row">
            @foreach ($packageInfos as $packageInfo)
              @if (!empty($packageInfo->package))
                <div class="col-lg-6">
                  <div class="packages-post-item">
                    <a class="post-thumbnail d-block" href="{{ route('package_details', ['id' => $packageInfo->package_id, 'slug' => $packageInfo->slug]) }}">
                      <img class="lazy" data-src="{{ asset('assets/img/packages/' . $packageInfo->package->featured_img) }}" alt="package img">
                    </a>

                    <div class="entry-content">
                      <h3 class="title">
                        <a href="{{ route('package_details', ['id' => $packageInfo->package_id, 'slug' => $packageInfo->slug]) }}">{{ strlen($packageInfo->title) > 50 ? mb_substr($packageInfo->title, 0, 50, 'utf-8') . '...' : $packageInfo->title }}</a>
                      </h3>
                      <div class="post-meta">
                        <ul>

                          @if ($packageInfo->package->pricing_type != 'negotiable')
                            <li><span><i class="fas fa-comment-dollar"></i><strong>{{ __('Package Price' . ':') }}</strong> {{ $currencyInfo->base_currency_symbol_position == 'left' ? $currencyInfo->base_currency_symbol : '' }} {{ $packageInfo->package->package_price }} {{ $currencyInfo->base_currency_symbol_position == 'right' ? $currencyInfo->base_currency_symbol : '' }} {{ '(' . strtoupper($packageInfo->package->pricing_type) . ')' }}</span></li>
                          @else
                            <li><span><i class="fas fa-comment-dollar"></i><strong>{{ __('Package Price' . ':') }}</strong> {{__('Negotiable')}}</span></li>
                          @endif

                          <li><span><i class="fas fa-users"></i><strong>{{ __('Number of Days' . ':') }}</strong> {{ $packageInfo->package->number_of_days }}</span></li>

                          <li><span><i class="fas fa-users"></i><strong>{{ __('Maximum Persons' . ':') }}</strong> {{ $packageInfo->package->max_persons != null ? $packageInfo->package->max_persons : '-' }}</span></li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              @endif
            @endforeach
          </div>
        @endif
      </div>
    </section>
    <!-- Package Section End -->
    @endif


    @if ($sections->statistics_section == 1)
    <!-- CounterUp Start -->
    <section
      class="counter-up primary-bg lazy"
      data-bg="{{ asset('assets/img/counter-bg.jpg') }}"
    >
      <div class="container">
        @if (count($counterInfos) == 0)
          <div class="row text-center">
            <div class="col">
              <h3>{{ __('No Counter Information Found!') }}</h3>
            </div>
          </div>
        @else
          <div class="row">
            @foreach ($counterInfos as $counterInfo)
              <div class="col-lg-3 col-md-6">
                <div class="counter-box style-two">
                  <div class="fact-icon">
                    <i class="{{ $counterInfo->icon }}"></i>
                  </div>
                  <p class="fact-num"><span class="counter-number">{{ $counterInfo->amount }}</span></p>
                  <p>{{ $counterInfo->title }}</p>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </section>
    <!-- CounterUp End -->
    @endif

    @if ($sections->video_section == 1)
    <!-- Call To Action Start -->
    <section class="cta-section bg-img-center lazy {{$websiteInfo->home_version == 'parallax' ? 'parallax' : ''}}" data-bg="{{ asset('assets/lfm/files/1/Newfoto/IMG-20230221-WA0182.jpg') }}">
    <div class="container">
        <div class="row align-items-center">
        <div class="col-md-10">
            <div class="cta-left-content">
            @if (!empty($secHeading))
                <span>{{ convertUtf8($secHeading->booking_section_title) }}</span>
                <h1>{{ convertUtf8($secHeading->booking_section_subtitle) }}</h1>
                <a href="{{ $secHeading->booking_section_button_url }}" class="btn filled-btn">
                {{ $secHeading->booking_section_button }} <i class="far fa-long-arrow-right"></i>
                </a>
            @endif
            </div>
        </div>

        <div class="col-md-2">
            @if (!empty($secHeading))
            <div class="video-icon text-right">
                <a href="{{ $secHeading->booking_section_video_url }}" class="video-popup"> <i class="fas fa-play"></i></a>
            </div>
            @endif
        </div>
        </div>
    </div>
    </section>
    <!-- Call To Action End -->
    @endif

    @if ($sections->facilities_section == 1)
    <!-- Why Choose Us/Facility Section Start -->
    <section class="wcu-section section-bg section-padding">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-5 offset-lg-1">
            <!-- Section Title -->
            <div class="feature-left">
              <div class="section-title">
                @if (!is_null($secHeading))
                  <span class="title-top with-border">{{ convertUtf8($secHeading->facility_section_title) }}</span>
                  <h1>{{ convertUtf8($secHeading->facility_section_subtitle) }}</h1>
                @endif
              </div>

              @if (count($facilities) > 0)
                <ul class="feature-list">
                  @foreach ($facilities as $facility)
                    <li class="wow fadeInUp animated" data-wow-duration="1000ms" data-wow-delay="{{$loop->iteration * 100}}ms">
                      <div class="feature-icon"><i class="{{ $facility->facility_icon }}"></i></div>
                      <h4>{{ convertUtf8($facility->facility_title) }}</h4>
                      <p>{{ $facility->facility_text }}</p>
                    </li>
                  @endforeach
                </ul>
              @endif
            </div>
          </div>

          <div class="col-lg-6">
            @if (!is_null($secHeading))
              <div class="feature-img">
                <div class="feature-abs-con">
                  <div class="f-inner">
                    <i class="far fa-stars"></i>
                    <p>{{ __('Popular Features') }}</p>
                  </div>
                </div>
                <img class="lazy" data-src="{{ asset('assets/img/facility_section/' . $secHeading->facility_section_image) }}" alt="image">
              </div>
            @endif
          </div>
        </div>
      </div>
    </section>
    <!-- Why Choose Us/Facility Section End -->
    @endif

    <!-- Gallery Start -->
    <section class="gallery-wrap section-padding">
        <div class="container">
            <div class="section-title text-center">
                <div class="row justify-content-center">
                    <div class="col-lg-7">
                        <span class="title-top">Gallery</span>
                        <h1>{{ $websiteInfo->website_title }}</h1>
                    </div>
                </div>
            </div>
            <!-- if category is null then no gallery is available -->
            @if (count($categories) == 0 || count($galleryInfos) == 0)
            <div class="row text-center">
                <div class="col">
                <h3>{{ __('No Gallery Found!') }}</h3>
                </div>
            </div>
            @else
            <div class="gallery-filter text-center">
                <ul class="list-inline">
                <li class="active" data-filter="*">{{ __('Show All') }}</li>
                @foreach ($categories as $category)
                    @php
                    $filterValue = '.' . strtolower($category->name);

                    if (str_contains($filterValue, ' ')) {
                        $filterValue = str_replace(' ', '-', $filterValue);
                    }
                    @endphp

                    <li data-filter="{{ $filterValue }}">{{ convertUtf8($category->name) }}</li>
                @endforeach
                </ul>
            </div>

            <div class="gallery-items">
                <div class="row gallery-filter-items">
                @foreach ($galleryInfos as $galleryInfo)
                    <!-- Single Item -->
                    @php
                    $galleryCategory = $galleryInfo->galleryCategory()->first();
                    $categoryName = strtolower($galleryCategory->name);

                    if (str_contains($categoryName, ' ')) {
                        $categoryName = str_replace(' ', '-', $categoryName);
                    }
                    @endphp

                    <div class="col-lg-4 col-md-6 col-sm-6 {{ $categoryName }}">
                    <a class="gallery-item lazy bg-light d-block" href="{{ asset('assets/img/gallery/' . $galleryInfo->gallery_img) }}" data-bg="{{ asset('assets/img/gallery/' . $galleryInfo->gallery_img) }}">
                        <div class="gallery-content">
                        <h3>{{ convertUtf8($galleryInfo->title) }}</h3>
                        </div>
                    </a>
                    </div>
                @endforeach
                </div>
            </div>
            @endif
        </div>
    </section>
    <!-- Gallery End -->


    @if ($sections->testimonials_section == 1)
    <!-- Feedback/Testimonial Section Start -->
    <section class="feedback-section-two section-padding">
      <div class="container">
        <!-- Section Title -->
        <div class="section-title text-center">
          @if (!empty($secHeading))
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <span class="title-top">{{ $secHeading->testimonial_section_title }}</span>
                    <h1>{{ $secHeading->testimonial_section_subtitle }}</h1>
                </div>
            </div>
          @endif
        </div>

        @if (count($testimonials) == 0)
          <div class="row text-center">
            <div class="col">
              <h3>{{ __('No Testimonial Found!') }}</h3>
            </div>
          </div>
        @else
          <div class="feedback-slider-two" id="feedSliderTwo">
            @foreach ($testimonials as $testimonial)
              {{-- show only those testimonials which has client image and designation --}}
                <div class="single-feedback-slide">
                  <div class="row align-items-center">
                    <div class="col-lg-6">
                      <div class="client-big-img">
                        @if (!empty($secHeading->testimonial_section_image))
                          <img class="lazy" data-src="{{ asset('assets/img/testimonial_section/' . $secHeading->testimonial_section_image) }}" alt="">
                        @endif
                      </div>
                    </div>

                    <div class="col-lg-5 offset-lg-1">
                      <div class="feedback-desc">
                        <div class="feedback-client-desc d-flex align-items-center">
                          @if (!empty($testimonial->client_image))
                            <div class="client-img">
                                <img class="lazy" data-src="{{ asset('assets/img/testimonial_section/' . $testimonial->client_image) }}" alt="">
                            </div>
                          @endif
                          <div class="client-name">
                            <h3>{{ convertUtf8($testimonial->client_name) }}</h3>
                            @if (!empty($testimonial->client_designation))
                            <span class="client-job">{{ convertUtf8($testimonial->client_designation) }}</span>
                            @endif
                          </div>
                        </div>
                        <p>{{ $testimonial->comment }}</p>
                        <span class="quote-icon"><img class="lazy" data-src="{{ asset('assets/img/icons/quote.png') }}" alt="quote"></span>
                      </div>
                    </div>
                  </div>
                </div>
            @endforeach
          </div>
        @endif
      </div>
    </section>
    <!-- Feedback/Testimonial Section End -->
    @endif

    @if ($sections->blogs_section == 1)
    <!-- Latest Blog Start -->
    <section class="latest-blog section-padding section-bg">
      <div class="container">
        <!-- Section Title -->
        <div class="section-title text-center">
          @if (!empty($secHeading))
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <span class="title-top">{{ convertUtf8($secHeading->blog_section_title) }}</span>
                    <h1>{{ convertUtf8($secHeading->blog_section_subtitle) }}</h1>
                </div>
            </div>
          @endif
        </div>

        @if (count($blogInfos) == 0)
          <div class="row text-center">
            <div class="col">
              <h3>{{ __('No Latest Blog Found!') }}</h3>
            </div>
          </div>
        @else
          <div class="row">
            @foreach ($blogInfos as $blogInfo)
              <div class="col-lg-4 col-md-6 col-sm-6 order-lg-1 order-sm-2">
                <div
                  class="single-latest-blog wow @if ($loop->iteration == 1) fadeIn
                  @elseif ($loop->iteration == 2) fadeInUp
                  @elseif ($loop->iteration == 3) fadeIn @endif animated"
                  data-wow-duration="1500ms"
                  data-wow-delay="@if ($loop->iteration == 1) 400ms
                  @elseif ($loop->iteration == 2) 600ms
                  @elseif ($loop->iteration == 3) 800ms @endif"
                >
                  <div class="blog-img">
                    <img class="lazy" data-src="{{ asset('assets/img/blogs/' . $blogInfo->blog->blog_img) }}" alt="blog image">
                  </div>
                  <div class="latest-blog-desc">
                    <span class="post-date"><i class="far fa-calendar-alt"></i>{{ date_format($blogInfo->blog->created_at, 'd M Y') }}</span>
                    <h6>
                      {{ convertUtf8($blogInfo->title) }}
                    </h6>
                    <a href="{{ route('blog_details', ['id' => $blogInfo->blog_id, 'slug' => $blogInfo->slug]) }}" class="read-more">
                      {{ __('read more') }} <i class="far fa-long-arrow-right"></i>
                    </a>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </section>
    <!-- Latest Blog End -->
    @endif

    @if ($sections->brand_section == 1)
    <!-- Brands Section Start -->
    <section class="brands-section primary-bg">
      <div class="container">
        @if (count($brands) == 0)
          <div class="row text-center">
            <div class="col">
              <h3>{{ __('No Brand Found!') }}</h3>
            </div>
          </div>
        @else
          <div id="brandsSlideActive" class="row">
            @foreach ($brands as $brand)
                <a class="brand-item text-center d-block" href="{{$brand->brand_url}}" target="_blank">
                  <img class="lazy" data-src="{{ asset('assets/img/brands/' . $brand->brand_img) }}" alt="brand image">
                </a>
            @endforeach
          </div>
        @endif
      </div>
    </section>
    <!-- Brands Section End -->
    @endif
  </main>
@endsection

@section('script')
  <script src="{{asset('assets/js/home.js')}}"></script>
@endsection
