@extends('backend.layout')

{{-- this style will be applied when the direction of language is right-to-left --}}
@includeIf('backend.partials.rtl_style')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Intro Section') }}</h4>
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
        <a href="#">{{ __('Intro Section') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-lg-10">
              <div class="card-title">{{ __('Update Intro Section') }}</div>
            </div>

            <div class="col-lg-2">
              @includeIf('backend.partials.languages')
            </div>
          </div>
        </div>

        <div class="card-body pt-5 pb-4">
          <div class="row">
            <div class="col-lg-8 offset-lg-2">
              <form
                id="introSecForm"
                action="{{ route('admin.home_page.update_intro_section', ['language' => request()->input('language')]) }}"
                method="POST"
              >
                @csrf
                <div class="form-group">
                  <div class="thumb-preview" id="thumbPreview1">
                    @if (!empty($data->intro_img))
                      <img src="{{ asset('assets/img/intro_section/' . $data->intro_img) }}" alt="image">
                    @else
                      <img src="{{ asset('assets/img/noimage.jpg') }}" alt="...">
                    @endif
                  </div>
                  <br><br>

                  <input type="hidden" id="fileInput1" name="intro_img">
                  <button
                    id="chooseImage1"
                    class="choose-image btn btn-primary"
                    type="button"
                    data-multiple="false"
                    data-toggle="modal"
                    data-target="#lfmModal1"
                  >{{ __('Choose Image') }}</button>
                  @if ($errors->has('intro_img'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('intro_img') }}</p>
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
                      <label for="">{{ __('Intro Primary Title*') }}</label>
                      <input type="text" class="form-control" name="intro_primary_title" value="{{ $data != null ? $data->intro_primary_title : '' }}">
                      @if ($errors->has('intro_primary_title'))
                        <p class="mt-2 mb-0 text-danger">{{ $errors->first('intro_primary_title') }}</p>
                      @endif
                    </div>
                  </div>

                  <div class="col-lg-6">
                    <div class="form-group">
                      <label for="">{{ __('Intro Secondary Title*') }}</label>
                      <input type="text" class="form-control" name="intro_secondary_title" value="{{ $data != null ? $data->intro_secondary_title : '' }}">
                      @if ($errors->has('intro_secondary_title'))
                        <p class="mt-2 mb-0 text-danger">{{ $errors->first('intro_secondary_title') }}</p>
                      @endif
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label for="">{{ __('Intro Text*') }}</label>
                  <textarea class="form-control" name="intro_text" rows="5" cols="80">{{ $data != null ? $data->intro_text : '' }}</textarea>
                  @if ($errors->has('intro_text'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('intro_text') }}</p>
                  @endif
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="card-footer">
          <div class="row">
            <div class="col-12 text-center">
              <button type="submit" form="introSecForm" class="btn btn-success">
                {{ __('Update') }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">{{ __('Counter Informations') }}</div>
          <a
            href="{{ route('admin.home_page.intro_section.create_count_info') . '?language=' . request()->input('language') }}"
            class="btn btn-sm btn-primary float-lg-right float-left"
          ><i class="fas fa-plus"></i> {{ __('Add') }}</a>
        </div>

        <div class="card-body">
          <div class="row">
            <div class="col-lg-12">
              @if (count($counterInfos) == 0)
                <h3 class="text-center">{{ __('NO COUNTER INFO FOUND!') }}</h3>
              @else
                <div class="table-responsive">
                  <table class="table table-striped mt-3">
                    <thead>
                      <tr>
                        <th scope="col">{{ __('#') }}</th>
                        <th scope="col">{{ __('Icon') }}</th>
                        <th scope="col">{{ __('Title') }}</th>
                        <th scope="col">{{ __('Amount') }}</th>
                        <th scope="col">{{ __('Serial Number') }}</th>
                        <th scope="col">{{ __('Actions') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($counterInfos as $counterInfo)
                        <tr>
                          <td>{{ $loop->iteration }}</td>
                          <td><i class="{{ $counterInfo->icon }}"></i></td>
                          <td>{{ convertUtf8($counterInfo->title) }}</td>
                          <td>{{ $counterInfo->amount }}</td>
                          <td>{{ $counterInfo->serial_number }}</td>
                          <td>
                            <a
                              class="btn btn-secondary btn-sm mr-1"
                              href="{{ route('admin.home_page.intro_section.edit_count_info', ['id' => $counterInfo->id]) . '?language=' . request()->input('language') }}"
                            >
                              <span class="btn-label">
                                <i class="fas fa-edit"></i>
                              </span>
                              {{ __('Edit') }}
                            </a>

                            <form
                              class="deleteForm d-inline-block"
                              action="{{ route('admin.home_page.intro_section.delete_count_info') }}" method="post"
                            >
                              @csrf
                              <input type="hidden" name="counterInfo_id" value="{{ $counterInfo->id }}">
                              <button type="submit" class="btn btn-danger btn-sm deleteBtn">
                                <span class="btn-label">
                                  <i class="fas fa-trash"></i>
                                </span>
                                {{ __('Delete') }}
                              </button>
                            </form>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
