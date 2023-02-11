@extends('backend.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Add Blog') }}</h4>
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
        <a href="#">{{ __('Blogs Management') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Blogs') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Add Blog') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">{{ __('Add New Blog') }}</div>
          <a
            class="btn btn-info btn-sm float-right d-inline-block"
            href="{{ route('admin.blogs_management.blogs') }}"
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
              <div class="alert alert-danger pb-1" id="blogErrors" style="display: none;">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <ul></ul>
              </div>

              <form id="blogForm" action="{{ route('admin.blogs_management.store_blog') }}" method="POST">
                @csrf
                <div class="form-group">
                  <label for="">{{ __('Blog\'s Image*') }}</label>
                  <br>
                  <div class="thumb-preview" id="thumbPreview1">
                    <img src="{{ asset('assets/img/noimage.jpg') }}" alt="...">
                  </div>
                  <br><br>

                  <input type="hidden" id="fileInput1" name="blog_img">
                  <button
                    id="chooseImage1"
                    class="choose-image btn btn-primary"
                    type="button"
                    data-multiple="false"
                    data-toggle="modal"
                    data-target="#lfmModal1"
                  >{{ __('Choose Image') }}</button>

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
                  <div class="col-lg-12">
                    <div class="form-group">
                      <label for="">{{ __('Serial Number*') }}</label>
                      <input type="number" class="form-control" name="serial_number" placeholder="Enter Serial Number">
                      <p class="text-warning mt-2">
                        <small>{{ __('The higher the serial number is, the later the blog will be shown.') }}</small>
                      </p>
                    </div>
                  </div>
                </div>

                <div id="accordion" class="mt-3">
                  @foreach ($languages as $language)
                    <div class="version">
                      <div class="version-header" id="heading{{ $language->id }}">
                        <h5 class="mb-0">
                          <button
                            type="button"
                            class="btn btn-link"
                            data-toggle="collapse"
                            data-target="#collapse{{ $language->id }}"
                            aria-expanded="{{ $language->is_default == 1 ? 'true' : 'false' }}"
                            aria-controls="collapse{{ $language->id }}"
                          >
                            {{ $language->name . __(' Language') }} {{ $language->is_default == 1 ? '(Default)' : '' }}
                          </button>
                        </h5>
                      </div>

                      <div
                        id="collapse{{ $language->id }}"
                        class="collapse {{ $language->is_default == 1 ? 'show' : '' }}"
                        aria-labelledby="heading{{ $language->id }}"
                        data-parent="#accordion"
                      >
                        <div class="version-body">
                          <div class="row">
                            <div class="col-lg-6">
                              <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                <label>{{ __('Title*') }}</label>
                                <input type="text" class="form-control" name="{{ $language->code }}_title" placeholder="Enter Title">
                              </div>
                            </div>

                            <div class="col-lg-6">
                              <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                <label>{{ __('Category*') }}</label>
                                <select name="{{ $language->code }}_category" class="form-control">
                                  <option selected disabled>
                                    {{ __('Select a Category') }}
                                  </option>

                                  @php
                                    $categories = App\Models\BlogManagement\BlogCategory::where('language_id', $language->id)->where('status', 1)->get();
                                  @endphp

                                  @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">
                                      {{ $category->name }}
                                    </option>
                                  @endforeach
                                </select>
                              </div>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-lg-12">
                              <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                <label>{{ __('Content*') }}</label>
                                <textarea id="{{ $language->code }}_BlogContent" class="form-control summernote" name="{{ $language->code }}_content" placeholder="Enter Content" data-height="300"></textarea>
                              </div>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-lg-12">
                              <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                <label>{{ __('Meta Keywords') }}</label>
                                <input class="form-control" name="{{ $language->code }}_meta_keywords" placeholder="Enter Meta Keywords" data-role="tagsinput">
                              </div>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-lg-12">
                              <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                <label>{{ __('Meta Description') }}</label>
                                <textarea class="form-control" name="{{ $language->code }}_meta_description" rows="5" placeholder="Enter Meta Description"></textarea>
                              </div>
                            </div>
                          </div>

                          <div class="row">
                              <div class="col-12">
                                  @php
                                      $currLang = $language;
                                  @endphp
                                    @foreach ($languages as $language)
                                        @continue($currLang->id == $language->id)

                                        <div class="form-check py-0">
                                            <label class="form-check-label">
                                                <input class="form-check-input" type="checkbox" value="" onchange="cloneContent('collapse{{ $currLang->id }}', 'collapse{{ $language->id }}', event)">
                                                <span class="form-check-sign">Clone for <strong class="text-capitalize text-secondary">{{$language->name}}</strong> Language</span>
                                            </label>
                                        </div>
                                    @endforeach
                              </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="card-footer">
          <div class="row">
            <div class="col-12 text-center">
              <button type="submit" form="blogForm" class="btn btn-success">
                {{ __('Save') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
