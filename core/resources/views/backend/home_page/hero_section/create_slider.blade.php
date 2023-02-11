@extends('backend.layout')

{{-- this style will be applied when the direction of language is right-to-left --}}
@includeIf('backend.partials.rtl_style')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Create Slider') }}</h4>
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
        <a href="#">{{ __('Home Page') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Hero Section') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Create Slider') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">{{ __('Add Slider') }}</div>

          <a
            class="btn btn-info btn-sm float-right d-inline-block"
            href="{{ route('admin.home_page.hero.slider_version') . '?language=' . request()->input('language') }}"
          >
            <span class="btn-label">
              <i class="fas fa-backward" style="font-size: 12px;"></i>
            </span>
            {{ __('Back') }}
          </a>
        </div>

        <div class="card-body pt-5 pb-5">
          <div class="row">
            <div class="col-lg-8 offset-lg-2">
              <form
                id="sliderVersionForm"
                action="{{ route('admin.home_page.hero.store_slider_info', ['language' => request()->input('language')]) }}"
                method="POST"
              >
                @csrf
                <div class="form-group">
                  <div class="thumb-preview" id="thumbPreview1">
                    <img src="{{ asset('assets/img/noimage.jpg') }}" alt="...">
                  </div>
                  <br><br>

                  <input type="hidden" id="fileInput1" name="img">
                  <button
                    id="chooseImage1"
                    class="choose-image btn btn-primary"
                    type="button"
                    data-multiple="false"
                    data-toggle="modal"
                    data-target="#lfmModal1"
                  >{{ __('Choose Image') }}</button>
                  @if ($errors->has('img'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('img') }}</p>
                  @endif

                  {{-- lfm modal --}}
                  <div
                    class="modal fade lfm-modal"
                    id="lfmModal1"
                    tabindex="-1"
                    role="dialog"
                    aria-labelledby="lfmModalTitle"
                    aria-hidden="true"
                  >
                    <i class="fas fa-times-circle"></i>

                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                      <div class="modal-content">
                        <div class="modal-body p-0">
                          <iframe
                            src="{{ url('laravel-filemanager') }}?serial=1"
                            style="width: 100%; height: 500px; overflow: hidden; border: none;"
                          ></iframe>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label for="">{{ __('Title*') }}</label>
                      <input type="text" class="form-control" name="title" placeholder="Enter Slider Title">
                      @if ($errors->has('title'))
                        <p class="mt-2 mb-0 text-danger">{{ $errors->first('title') }}</p>
                      @endif
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label for="">{{ __('Subtitle*') }}</label>
                      <input type="text" class="form-control" name="subtitle" placeholder="Enter Slider Subtitle">
                      @if ($errors->has('subtitle'))
                        <p class="mt-2 mb-0 text-danger">{{ $errors->first('subtitle') }}</p>
                      @endif
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label for="">{{ __('Button Name*') }}</label>
                      <input type="text" class="form-control" name="btn_name" placeholder="Enter Slider Button Name">
                      @if ($errors->has('btn_name'))
                        <p class="mt-2 mb-0 text-danger">{{ $errors->first('btn_name') }}</p>
                      @endif
                    </div>
                  </div>

                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>{{ __('Button URL*') }}</label>
                      <input type="url" class="form-control ltr" name="btn_url" placeholder="Enter Slider Button URL">
                      @if ($errors->has('btn_url'))
                        <p class="mt-2 mb-0 text-danger">{{ $errors->first('btn_url') }}</p>
                      @endif
                    </div>
                  </div>

                </div>

                <div class="row">

                  <div class="col-lg-12">
                    <div class="form-group">
                      <label for="">{{ __('Serial Number*') }}</label>
                      <input type="number" class="form-control ltr" name="serial_number" placeholder="Enter Slider Serial Number">
                      @if ($errors->has('serial_number'))
                        <p class="mt-2 mb-0 text-danger">{{ $errors->first('serial_number') }}</p>
                      @endif
                      <p class="text-warning mt-2 mb-0">{{ __('The higher the serial number is, the later the slider will be shown.') }}</p>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="card-footer">
          <div class="row">
            <div class="col-12 text-center">
              <button type="submit" form="sliderVersionForm" class="btn btn-primary">
                {{ __('Save') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
