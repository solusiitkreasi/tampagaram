@extends('backend.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Booking Details') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{route('admin.dashboard')}}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Packages Management') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Package Bookings') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Booking Details') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">{{ __('Package Booking Details') }}</div>
          <a
            class="btn btn-info btn-sm float-right d-inline-block"
            href="{{ url()->previous() }}"
          >
            <span class="btn-label">
              <i class="fas fa-backward" style="font-size: 12px;"></i>
            </span>
            {{ __('Back') }}
          </a>
        </div>

        @php
          $position = $details->currency_text_position;
          $currency = $details->currency_text;
        @endphp

        <div class="card-body">
          <div class="container">
            <div class="row">
              <div class="col-lg-4">
                <strong style="text-transform: capitalize;">{{ __('booking number:') }}</strong>
              </div>
              <div class="col-lg-8">{{ '#' . $details->booking_number }}</div>
            </div>
            <hr>

            <div class="row">
              <div class="col-lg-4">
                <strong style="text-transform: capitalize;">{{ __('booking date:') }}</strong>
              </div>
              <div class="col-lg-8">
                {{ date_format($details->created_at, 'F d, Y') }}
              </div>
            </div>
            <hr>

            <div class="row">
              <div class="col-lg-4">
                <strong style="text-transform: capitalize;">{{ __('customer name:') }}</strong>
              </div>
              <div class="col-lg-8">{{ convertUtf8($details->customer_name) }}</div>
            </div>
            <hr>

            <div class="row">
              <div class="col-lg-4">
                <strong style="text-transform: capitalize;">{{ __('customer email:') }}</strong>
              </div>
              <div class="col-lg-8">{{ $details->customer_email }}</div>
            </div>
            <hr>

            <div class="row">
              <div class="col-lg-4">
                <strong style="text-transform: capitalize;">{{ __('customer phone:') }}</strong>
              </div>
              <div class="col-lg-8">{{ $details->customer_phone }}</div>
            </div>
            <hr>

            <div class="row">
              <div class="col-lg-4">
                <strong style="text-transform: capitalize;">{{ __('package name:') }}</strong>
              </div>
              <div class="col-lg-8">{{ $packageTitle }}</div>
            </div>
            <hr>

            @if ($packageCategoryName != null)
              <div class="row">
                <div class="col-lg-4">
                  <strong style="text-transform: capitalize;">{{ __('package type:') }}</strong>
                </div>
                <div class="col-lg-8">{{ $packageCategoryName }}</div>
              </div>
              <hr>
            @endif

            <div class="row">
              <div class="col-lg-4">
                <strong style="text-transform: capitalize;">{{ __('number of visitors:') }}</strong>
              </div>
              <div class="col-lg-8">{{ $details->visitors }}</div>
            </div>
            <hr>

            <div class="row">
              <div class="col-lg-4">
                <strong style="text-transform: capitalize;">{{ __('subtotal:') }}</strong>
              </div>
              <div class="col-lg-8">
                {{ $position == 'left' ? $currency . ' ' : '' }}{{ $details->subtotal }}{{ $position == 'right' ? ' ' . $currency : '' }}
              </div>
            </div>
            <hr>

            <div class="row">
              <div class="col-lg-4">
                <strong style="text-transform: capitalize;">{{ __('discount:') }}</strong>
              </div>
              <div class="col-lg-8">
                {{ $position == 'left' ? $currency . ' ' : '' }}{{ $details->discount }}{{ $position == 'right' ? ' ' . $currency : '' }}
              </div>
            </div>
            <hr>

            <div class="row">
              <div class="col-lg-4">
                <strong style="text-transform: capitalize;">{{ __('total cost:') }}</strong>
              </div>
              <div class="col-lg-8">
                {{ $position == 'left' ? $currency . ' ' : '' }}{{ $details->grand_total }}{{ $position == 'right' ? ' ' . $currency : '' }}
              </div>
            </div>
            <hr>

            <div class="row">
              <div class="col-lg-4">
                <strong style="text-transform: capitalize;">{{ __('payment method:') }}</strong>
              </div>
              <div class="col-lg-8">{{ $details->payment_method }}</div>
            </div>
            <hr>

            <div class="row">
              <div class="col-lg-4">
                <strong style="text-transform: capitalize;">{{ __('payment status:') }}</strong>
              </div>
              <div class="col-lg-8">
                {{ $details->payment_status == 1 ? 'Paid' : 'Unpaid' }}
              </div>
            </div>
          </div>
        </div>

        <div class="card-footer"></div>
      </div>
    </div>
  </div>
@endsection
