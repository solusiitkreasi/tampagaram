@extends('frontend.layout')

@section('pageHeading')
  {{ __('Dashboard') }}
@endsection

@section('content')
  <main>
    <!-- Breadcrumb Section Start -->
    <section
      class="breadcrumb-area d-flex align-items-center position-relative bg-img-center"
      style="background-image: url({{ asset('assets/img/' . $breadcrumbInfo->breadcrumb) }});"
    >
      <div class="container">
        <div class="breadcrumb-content text-center">
          <h1>{{ __('Dashboard') }}</h1>
          <ul class="list-inline">
            <li><a href="{{ route('index') }}">{{ __('Home') }}</a></li>
            <li><i class="far fa-angle-double-right"></i></li>
            <li>{{ __('Dashboard') }}</li>
          </ul>
        </div>
      </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- Dashboard Area Start -->
    <section class="user-dashboard">
      <div class="container">
        <div class="row">
          @include('frontend.user.side_navbar')

          <div class="col-lg-9">
            <div class="row mb-5">
              <div class="col-lg-12">
                <div class="user-profile-details">
                  <div class="account-info">
                    <div class="title">
                      <h4>{{ __('User Information') }}</h4>
                    </div>

                    <div class="main-info">
                      <ul class="list">
                        @if (Auth::guard('web')->user()->first_name != null ||
                          Auth::guard('web')->user()->last_name != null)
                          <li><span>{{ __('Name') . ':' }}</span></li>
                        @endif

                        <li><span>{{ __('Username') . ':' }}</span></li>

                        <li><span>{{ __('Email') . ':' }}</span></li>

                        @if (Auth::guard('web')->user()->contact_number != null)
                          <li><span>{{ __('Phone') . ':' }}</span></li>
                        @endif

                        @if (Auth::guard('web')->user()->address != null)
                          <li><span>{{ __('Address') . ':' }}</span></li>
                        @endif

                        @if (Auth::guard('web')->user()->city != null)
                          <li><span>{{ __('City') . ':' }}</span></li>
                        @endif

                        @if (Auth::guard('web')->user()->state != null)
                          <li><span>{{ __('State') . ':' }}</span></li>
                        @endif

                        @if (Auth::guard('web')->user()->country != null)
                          <li><span>{{ __('Country') . ':' }}</span></li>
                        @endif
                      </ul>

                      <ul class="list">
                        <li>
                          {{ Auth::guard('web')->user()->first_name . ' ' . Auth::guard('web')->user()->last_name }}
                        </li>

                        <li>
                          {{ Auth::guard('web')->user()->username }}
                        </li>

                        <li>
                          {{ Auth::guard('web')->user()->email }}
                        </li>

                        <li>
                          {{ Auth::guard('web')->user()->contact_number }}
                        </li>

                        <li>
                          {{ Auth::guard('web')->user()->address }}
                        </li>

                        <li>
                          {{ Auth::guard('web')->user()->city }}
                        </li>

                        <li>
                          {{ Auth::guard('web')->user()->state }}
                        </li>

                        <li>
                          {{ Auth::guard('web')->user()->country }}
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-4">
                <div class="card card-box box-1">
                  <div class="card-info">
                    <h5>{{ __('Total Room Booking') }}</h5>
                    <p>{{ $totalRoomBooking }}</p>
                  </div>
                </div>
              </div>

              <div class="col-md-4">
                <div class="card card-box box-2">
                  <div class="card-info">
                    <h5>{{ __('Total Package Booking') }}</h5>
                    <p>{{ $totalPackageBooking }}</p>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- Dashboard Area End -->
  </main>
@endsection
