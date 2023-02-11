@extends('backend.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Maintenance Mode') }}</h4>
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
        <a href="#">{{ __('Maintenance Mode') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-lg-12">
              <div class="card-title">{{ __('Update Maintenance Mode') }}</div>
            </div>
          </div>
        </div>

        <div class="card-body pt-5 pb-5">
          <div class="row">
            <div class="col-lg-6 offset-lg-3">
              <form id="maintenanceForm" action="{{ route('admin.basic_settings.update_maintenance') }}" method="POST">
                @csrf
                <div class="form-group">
                  <div class="thumb-preview" id="thumbPreview1">
                    @if (!empty($data->maintenance_img))
                      <img src="{{ asset('assets/img/' . $data->maintenance_img) }}" alt="maintenance image">
                    @else
                      <img src="{{ asset('assets/img/noimage.jpg') }}" alt="...">
                    @endif
                  </div>
                  <br><br>

                  <input type="hidden" id="fileInput1" name="maintenance_img">
                  <button
                    id="chooseImage1"
                    class="choose-image btn btn-primary"
                    type="button"
                    data-multiple="false"
                    data-toggle="modal"
                    data-target="#lfmModal1"
                  >{{ __('Choose Image') }}</button>
                  @if ($errors->has('maintenance_img'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('maintenance_img') }}</p>
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

                <div class="form-group">
                  <label>{{ __('Maintenance Status*') }}</label>
                  <div class="selectgroup w-100">
                    <label class="selectgroup-item">
                      <input
                        type="radio"
                        name="maintenance_status"
                        value="1"
                        class="selectgroup-input"
                        {{ $data->maintenance_status == 1 ? 'checked' : '' }}
                      >
                      <span class="selectgroup-button">{{ __('Active') }}</span>
                    </label>
                    <label class="selectgroup-item">
                      <input
                        type="radio"
                        name="maintenance_status"
                        value="0"
                        class="selectgroup-input"
                        {{ $data->maintenance_status == 0 ? 'checked' : '' }}
                      >
                      <span class="selectgroup-button">{{ __('Deactive') }}</span>
                    </label>
                  </div>
                  @if ($errors->has('maintenance_status'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('maintenance_status') }}</p>
                  @endif
                </div>

                <div class="form-group">
                  <label>{{ __('Maintenance Message*') }}</label>
                  <textarea
                    class="form-control"
                    name="maintenance_msg"
                    rows="3"
                    cols="80"
                  >{{ $data->maintenance_msg }}</textarea>
                  @if ($errors->has('maintenance_msg'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('maintenance_msg') }}</p>
                  @endif
                </div>

                <div class="form-group">
                  <label>Secret Path</label>
                  <input name="secret_path" type="text" class="form-control" value="{{$data->secret_path}}">
                  <p class="text-warning">After activating maintenance mode, You can access the website via <strong class="text-danger">{{url('{secret_path}')}}</strong></p>
                  <p class="text-warning">Try to avoid using special characters</p>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="card-footer">
          <div class="row">
            <div class="col-12 text-center">
              <button type="submit" form="maintenanceForm" class="btn btn-success">
                {{ __('Update') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
