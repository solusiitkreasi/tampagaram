@extends('frontend.layout')

@section('pageHeading')
  {{ __('Package Booking Details') }}
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
          <h1>{{ __('Package Booking Details') }}</h1>
          <ul class="list-inline">
            <li><a href="{{ route('index') }}">{{ __('Home') }}</a></li>
            <li><i class="far fa-angle-double-right"></i></li>
            <li>{{ __('Package Booking Details') }}</li>
          </ul>
        </div>
      </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- Package Booking Details Area Start -->
    <section class="user-dashboard">
      <div class="container">
        <div class="row">
          @include('frontend.user.side_navbar')

          <div class="col-lg-9">
            <div class="row">
              <div class="col-lg-12">
                <div class="user-profile-details">
                  <div class="order-details">
                    <div class="title">
                      <h4>{{ __('Package Booking Details') }}</h4>
                    </div>

                    <div class="view-order-page">
                      <div class="order-info-area">
                        <div class="row align-items-center">
                          <div class="col-lg-8">
                            <div class="order-info">
                              <h3>{{ __('Booking') . ': ' . '#' . $details->booking_number }}</h3>

                              <p>{{ __('Booking Date') . ': ' . date_format($details->created_at, 'M d, Y') }}</p>
                            </div>
                          </div>

                          <div class="col-lg-4">
                            <div class="print">
                              <a href="{{ asset('assets/invoices/packages/' . $details->invoice) }}" download class="btn">
                                <i class="fas fa-download"></i>{{ __('Invoice') }}
                              </a>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="billing-add-area">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="main-info">
                            <h5>{{ __('User Information') }}</h5>
                            <ul class="list">
                              <li>
                                <p><span>{{ __('Name') . ':' }}</span>{{ $userInfo->first_name . ' ' . $userInfo->last_name }}</p>
                              </li>

                              <li>
                                <p><span>{{ __('Email') . ':' }}</span>{{ $userInfo->email }}</p>
                              </li>

                              <li>
                                <p><span>{{ __('Phone') . ':' }}</span>{{ $userInfo->contact_number }}</p>
                              </li>

                              <li>
                                <p><span>{{ __('Address') . ':' }}</span>{{ $userInfo->address }}</p>
                              </li>

                              <li>
                                <p><span>{{ __('City') . ':' }}</span>{{ $userInfo->city }}</p>
                              </li>

                              <li>
                                <p><span>{{ __('State') . ':' }}</span>{{ $userInfo->state }}</p>
                              </li>

                              <li>
                                <p><span>{{ __('Country') . ':' }}</span>{{ $userInfo->country }}</p>
                              </li>
                            </ul>
                          </div>
                        </div>

                        @php
                          $position = $details->currency_symbol_position;
                          $symbol = $details->currency_symbol;
                        @endphp

                        <div class="col-md-6">
                          <div class="main-info">
                            <h5>{{ __('Payment Information') }}</h5>
                            <ul class="list">
                              <li>
                                <p><span>{{ __('Subtotal') . ':' }}</span><span class="amount">{{ $position == 'left' ? $symbol : '' }}{{ $details->subtotal }}{{ $position == 'right' ? $symbol : '' }}</span></p>
                              </li>

                              <li>
                                <p><span>{{ __('Discount') }} (<i class="far fa-minus text-success"></i>):</span><span class="amount">{{ $position == 'left' ? $symbol : '' }}{{ $details->discount }}{{ $position == 'right' ? $symbol : '' }}</span></p>
                              </li>

                              <li>
                                <p><span>{{ __('Total' . ':') }}</span><span class="amount">{{ $position == 'left' ? $symbol : '' }}{{ $details->grand_total }}{{ $position == 'right' ? $symbol : '' }}</span></p>
                              </li>

                              <li>
                                <p><span>{{ __('Paid via' . ':') }}</span>{{ $details->payment_method }}</p>
                              </li>

                              <li>
                                @if ($details->payment_status == 1)
                                  <p><span>{{ __('Payment Status' . ':') }}</span><span class="badge badge-success px-2 py-1">{{ __('Complete') }}</span></p>
                                @else
                                  <p><span>{{ __('Payment Status' . ':') }}</span><span class="badge badge-warning px-2 py-1">{{ __('Pending') }}</span></p>
                                @endif
                              </li>
                            </ul>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="edit-account-info">
                      <a href="{{ url()->previous() }}" class="btn">{{ __('back') }}</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- Package Booking Details Area End -->
  </main>
@endsection
