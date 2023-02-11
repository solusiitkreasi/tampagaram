@extends('backend.layout')

{{-- this style will be applied when the direction of language is right-to-left --}}
@includeIf('backend.partials.rtl_style')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Add Testimonial') }}</h4>
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
        <a href="#">{{ __('Add Testimonial') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-lg-10">
              <div class="card-title">{{ __('Create New Testimonial') }}</div>
            </div>

            <div class="col-lg-2">
              <a
                class="btn btn-info btn-sm float-right d-inline-block"
                href="{{ route('admin.home_page.testimonial_section') . '?language=' . request()->input('language') }}"
              >
                <span class="btn-label">
                  <i class="fas fa-backward" style="font-size: 12px;"></i>
                </span>
                {{ __('Back') }}
              </a>
            </div>
          </div>
        </div>

        <div class="card-body pt-5 pb-5">
          <div class="row">
            <div class="col-lg-6 offset-lg-3">
              <form
                id="testimonialForm"
                action="{{ route('admin.home_page.testimonial_section.store_testimonial', ['language' => request()->input('language')]) }}"
                method="POST"
              >
                @csrf
                @if ($websiteInfo->theme_version == 'theme_two')
                  <div class="form-group">
                    <div class="thumb-preview" id="thumbPreview1">
                      <img src="{{ asset('assets/img/noimage.jpg') }}" alt="...">
                    </div>
                    <br><br>

                    <input type="hidden" id="fileInput1" name="client_image">
                    <button
                      id="chooseImage1"
                      class="choose-image btn btn-primary"
                      type="button"
                      data-multiple="false"
                      data-toggle="modal"
                      data-target="#lfmModal1"
                    >{{ __('Choose Image') }}</button>
                    @if ($errors->has('client_image'))
                      <p class="mt-2 mb-0 text-danger">{{ $errors->first('client_image') }}</p>
                    @endif
                    <p class="text-warning mt-2 mb-0">{{ __('Upload 70X70 pixel size image or squre size image for best quality.') }}</p>

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
                @endif

                <div class="form-group">
                  <label for="">{{ __('Client\'s Name*') }}</label>
                  <input type="text" class="form-control" name="client_name" placeholder="Enter Client Name">
                  @if ($errors->has('client_name'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('client_name') }}</p>
                  @endif
                </div>

                @if ($websiteInfo->theme_version == 'theme_two')
                  <div class="form-group">
                    <label for="">{{ __('Client\'s Designation*') }}</label>
                    <input type="text" class="form-control" name="client_designation" placeholder="Enter Client Designation">
                    @if ($errors->has('client_designation'))
                      <p class="mt-2 mb-0 text-danger">{{ $errors->first('client_designation') }}</p>
                    @endif
                  </div>
                @endif

                <div class="form-group">
                  <label for="">{{ __('Comment*') }}</label>
                  <textarea class="form-control" name="comment" rows="5" cols="80" placeholder="Enter Comment"></textarea>
                  @if ($errors->has('comment'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('comment') }}</p>
                  @endif
                </div>

                <div class="form-group">
                  <label for="">{{ __('Serial Number*') }}</label>
                  <input type="number" class="form-control ltr" name="serial_number" placeholder="Enter Serial Number">
                  @if ($errors->has('serial_number'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('serial_number') }}</p>
                  @endif
                  <p class="text-warning mt-2">
                    <small>{{ __('The higher the serial number is, the later the testimonial will be shown.') }}</small>
                  </p>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="card-footer">
          <div class="row">
            <div class="col-12 text-center">
              <button type="submit" form="testimonialForm" class="btn btn-success">
                {{ __('Save') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
