@extends('backend.layout')

@section('content')
<div class="page-header">
  <h4 class="page-title">{{ __('Footer Logo') }}</h4>
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
      <a href="#">{{ __('Basic Settings') }}</a>
    </li>
    <li class="separator">
      <i class="flaticon-right-arrow"></i>
    </li>
    <li class="nav-item">
      <a href="#">{{ __('Footer Logo') }}</a>
    </li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-lg-4">
            <div class="card-title">{{ __('Update Footer Logo') }}</div>
          </div>
        </div>
      </div>

      <div class="card-body pt-5 pb-4">
        <div class="row">
          <div class="col-lg-6 offset-lg-3">
            <form id="imageForm" action="{{ route('admin.basic_settings.update_footer_logo') }}" method="POST">
              @csrf
              <div class="form-group">
                <div class="thumb-preview" id="thumbPreview1">
                  @if (!empty($data->footer_logo))
                    <img src="{{ asset('assets/img/' . $data->footer_logo) }}" alt="footer logo">
                  @else
                    <img src="{{ asset('assets/img/noimage.jpg') }}" alt="...">
                  @endif
                </div>
                <br><br>

                <input type="hidden" id="fileInput1" name="footer_logo">
                <button 
                  id="chooseImage1" 
                  class="choose-image btn btn-primary" 
                  type="button" 
                  data-multiple="false" 
                  data-toggle="modal" 
                  data-target="#lfmModal1"
                >{{ __('Choose Image') }}</button>
                @if ($errors->has('footer_logo'))
                  <p class="mt-2 mb-0 text-danger">{{ $errors->first('footer_logo') }}</p>
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
            </form>
          </div>
        </div>
      </div>

      <div class="card-footer">
        <div class="row">
          <div class="col-12 text-center">
            <button type="submit" form="imageForm" class="btn btn-success">
              {{ __('Update') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
