@extends('backend.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Admin Profile') }}</h4>
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
        <a href="#">{{ __('Profile Settings') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-lg-12">
              <div class="card-title">{{ __('Update Profile') }}</div>
            </div>
          </div>
        </div>

        <div class="card-body pt-5 pb-5">
          <div class="row">
            <div class="col-lg-6 offset-lg-3">
              <form id="editProfileForm" action="{{ route('admin.update_profile') }}" method="POST">
                @csrf
                <div class="form-group">
                  <div class="thumb-preview" id="thumbPreview1">
                    @if (!empty($adminInfo->image))
                      <img src="{{ asset('assets/img/admins/' . $adminInfo->image) }}" alt="image">
                    @else
                      <img src="{{ asset('assets/img/noimage.jpg') }}" alt="...">
                    @endif
                  </div>
                  <br><br>

                  <input type="hidden" id="fileInput1" name="image">
                  <button 
                    id="chooseImage1" 
                    class="choose-image btn btn-primary" 
                    type="button" 
                    data-multiple="false" 
                    data-toggle="modal" 
                    data-target="#lfmModal1"
                  >{{ __('Choose Image') }}</button>
                  @if ($errors->has('image'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('image') }}</p>
                  @endif
                  <p class="text-warning mt-2 mb-0">{{ __('Upload squre size image for best quality.') }}</p>

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
                  <label>{{ __('Username*') }}</label>
                  <input type="text" class="form-control" name="username" value="{{ $adminInfo->username }}">
                  @if ($errors->has('username'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('username') }}</p>
                  @endif
                </div>

                <div class="form-group">
                  <label>{{ __('Email*') }}</label>
                  <input type="email" class="form-control" name="email" value="{{ $adminInfo->email }}">
                  @if ($errors->has('email'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('email') }}</p>
                  @endif
                </div>

                <div class="form-group">
                  <label>{{ __('First Name*') }}</label>
                  <input type="text" class="form-control" name="first_name" value="{{ $adminInfo->first_name }}">
                  @if ($errors->has('first_name'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('first_name') }}</p>
                  @endif
                </div>

                <div class="form-group">
                  <label>{{ __('Last Name*') }}</label>
                  <input type="text" class="form-control" name="last_name" value="{{ $adminInfo->last_name }}">
                  @if ($errors->has('last_name'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('last_name') }}</p>
                  @endif
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="card-footer">
          <div class="row">
            <div class="col-12 text-center">
              <button type="submit" form="editProfileForm" class="btn btn-success">
                {{ __('Update') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
