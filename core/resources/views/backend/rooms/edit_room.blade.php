@extends('backend.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Edit Room') }}</h4>
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
        <a href="#">{{ __('Rooms Management') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Rooms') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Edit Room') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">{{ __('Update Room') }}</div>
          <a
            class="btn btn-info btn-sm float-right d-inline-block"
            href="{{ route('admin.rooms_management.rooms') }}"
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
              <div class="alert alert-danger pb-1" id="roomErrors" style="display: none;">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <ul></ul>
              </div>

              <form id="roomForm" action="{{ route('admin.rooms_management.update_room', ['id' => $room->id]) }}" method="POST">
                @csrf
                {{-- featured image start --}}
                <div class="form-group">
                  <label for="">{{ __('Featured Image*') }}</label>
                  <br>
                  <div class="thumb-preview" id="thumbPreview1">
                    <img src="{{ asset('assets/img/rooms/' . $room->featured_img) }}" alt="room image">
                  </div>
                  <br><br>

                  <input type="hidden" id="fileInput1" name="featured_img">
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
                {{-- featured image end --}}

                {{-- slider images start --}}
                <div class="form-group my-3">
                  <label for="">{{ __('Slider Images*') }}</label>
                  <br>
                  <div class="slider-thumbs" id="sliderThumbs2"></div>

                  <input type="hidden" id="fileInput2" name="slider_imgs">
                  <button
                    id="chooseImage2"
                    class="choose-image btn btn-primary mt-1"
                    type="button"
                    data-multiple="true"
                    data-toggle="modal"
                    data-target="#lfmModal2"
                  >{{ __('Choose Image') }}</button>

                  {{-- lfm modal --}}
                  <div
                    class="modal fade lfm-modal"
                    id="lfmModal2"
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
                            id="lfmIframe2"
                            src="{{ url('laravel-filemanager') }}?serial=2&room={{ $room->id }}"
                            style="width: 100%; height: 500px; overflow: hidden; border: none;"
                          ></iframe>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                {{-- slider images end --}}

                <div class="row">
                  <div class="col-lg-4">
                    <div class="form-group">
                      <label>{{ __('Room Status*') }}</label>
                      <select name="status" class="form-control">
                        <option disabled selected>{{ __('Select a Status') }}</option>
                        <option value="1" {{ $room->status == 1 ? 'selected' : '' }}>
                          {{ __('Show') }}
                        </option>
                        <option value="0" {{ $room->status == 0 ? 'selected' : '' }}>
                          {{ __('Hide') }}
                        </option>
                      </select>
                    </div>
                  </div>

                  <div class="col-lg-4">
                    <div class="form-group">
                      <label>{{ __('Rent / Night') }} (in {{$websiteInfo->base_currency_text}}) *</label>
                      <input type="number" class="form-control" name="rent" placeholder="Enter Room Rent" value="{{ $room->rent }}">
                    </div>
                  </div>

                  <div class="col-lg-4">
                    <div class="form-group">
                      <label>{{ __('Quantity*') }}</label>
                      <input type="number" class="form-control" name="quantity" placeholder="Enter No. Of Room" value="{{ $room->quantity }}">
                    </div>
                  </div>
                </div>

                <div class="row">
                    <div class="col-lg-4">
                      <div class="form-group">
                        <label>{{ __('Beds *') }}</label>
                        <input type="number" class="form-control" name="bed" placeholder="Enter No. Of Bed" value="{{ $room->bed }}">
                      </div>
                    </div>
                  <div class="col-lg-4">
                    <div class="form-group">
                      <label>{{ __('Baths *') }}</label>
                      <input type="number" class="form-control" name="bath" placeholder="Enter No. Of Bath" value="{{ $room->bath }}">
                    </div>
                  </div>

                  <div class="col-lg-4">
                    <div class="form-group">
                      <label>{{ __('Max. Guests') }}</label>
                      <input type="number" class="form-control" name="max_guests" placeholder="Enter maximum guests" value="{{ $room->max_guests }}">
                      <p class="text-warning mb-0">Leave blank if you want to make it unlimited.</p>
                    </div>
                  </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>{{ __('Latitude') }}</label>
                            <input type="text" class="form-control" name="latitude" placeholder="Enter latitude for map" value="{{ $room->latitude }}">
                            <p class="text-warning mb-0">Will be used to show in google map.</p>
                        </div>
                    </div>

                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>{{ __('Longitude') }}</label>
                      <input type="text" class="form-control" name="longitude" placeholder="Enter longitude for map" value="{{ $room->longitude }}">
                      <p class="text-warning mb-0">Will be used to show in google map.</p>
                    </div>
                  </div>
                </div>

                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                        <label>{{ __('Address') }}</label>
                        <input type="text" class="form-control" name="address" placeholder="Enter Address" value="{{ $room->address }}">
                        </div>
                    </div>

                  <div class="col-lg-4">
                    <div class="form-group">
                      <label>{{ __('Phone') }}</label>
                      <input type="text" class="form-control" name="phone" placeholder="Enter Phone" value="{{ $room->phone }}">
                    </div>
                  </div>

                  <div class="col-lg-4">
                    <div class="form-group">
                      <label>{{ __('Email') }}</label>
                      <input type="email" class="form-control" name="email" placeholder="Enter Email" value="{{ $room->email }}">
                    </div>
                  </div>
                </div>

                <div id="accordion" class="mt-5">
                  @foreach ($languages as $language)
                    @php
                      $roomContent = $language->roomDetails()->where('room_id', $room->id)->first();
                      $title = !empty($roomContent) ? $roomContent->title : '';
                      $categoryId = !empty($roomContent) ? $roomContent->room_category_id : '';
                      $summary = !empty($roomContent) ? $roomContent->summary : '';
                      $description = !empty($roomContent) ? $roomContent->description : '';
                      $meta_keywords = !empty($roomContent) ? $roomContent->meta_keywords : '';
                      $meta_description = !empty($roomContent) ? $roomContent->meta_description : '';
                    @endphp

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
                            <div class="
                            @if ($websiteInfo->room_category_status == 1)
                            col-lg-6
                            @elseif ($websiteInfo->room_category_status == 0)
                            col-lg-12
                            @endif
                            ">
                              <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                <label>{{ __('Room Title*') }}</label>
                                <input type="text" class="form-control" name="{{ $language->code }}_title" placeholder="Enter Title" value="{{ !empty($roomContent->title) ? $roomContent->title : '' }}">
                              </div>
                            </div>

                            @if ($websiteInfo->room_category_status == 1)
                            <div class="col-lg-6">
                              <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                @php
                                  $categories = App\Models\RoomManagement\RoomCategory::where('language_id', $language->id)->where('status', 1)->get();
                                @endphp

                                <label>{{ __('Category*') }}</label>
                                <select name="{{ $language->code }}_category" class="form-control">
                                  <option disabled selected value="">{{ __('Select a Category') }}</option>

                                  @foreach ($categories as $category)
                                    <option
                                      value="{{ $category->id }}"
                                      {{ $categoryId == $category->id ? 'selected' : '' }}
                                    >{{ $category->name }}</option>
                                  @endforeach
                                </select>
                              </div>
                            </div>
                            @endif
                          </div>

                          <div class="row">
                            <div class="col-lg-12">
                              <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                @php
                                  $amenities = App\Models\RoomManagement\RoomAmenity::where('language_id', $language->id)->orderBy('serial_number', 'asc')->get();

                                  if (!empty($roomContent->amenities) && $roomContent->amenities != '[]') {
                                    $amenityData = json_decode($roomContent->amenities, true);
                                  } else {
                                    $amenityData = [];
                                  }
                                @endphp

                                <label class="d-block">{{ __('Room Amenities*') }}</label>
                                @if (!empty($amenities))
                                    @foreach ($amenities as $amenity)
                                    <div class="d-inline mr-3">
                                        <input type="checkbox" class="mr-1" name="{{ $language->code }}_amenities[]" value="{{ $amenity->id }}" {{ in_array($amenity->id, $amenityData) ? 'checked' : '' }}>
                                        <label for="">{{ $amenity->name }}</label>
                                    </div>
                                    @endforeach
                                @endif
                              </div>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-lg-12">
                              <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                <label>{{ __('Summary*') }}</label>
                                <textarea class="form-control" name="{{ $language->code }}_summary" placeholder="Enter Summary" rows="3">{{ $summary }}</textarea>
                              </div>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-lg-12">
                              <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                <label>{{ __('Room Description*') }}</label>
                                <textarea class="form-control summernote" name="{{ $language->code }}_description" placeholder="Enter Description" data-height="300" id="{{$language->code}}RoomDesc">{{ replaceBaseUrl($description, 'summernote') }}</textarea>
                              </div>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-lg-12">
                              <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                <label>{{ __('Room Meta Keywords') }}</label>
                                <input class="form-control" name="{{ $language->code }}_meta_keywords" placeholder="Enter Meta Keywords" data-role="tagsinput" value="{{ $meta_keywords }}">
                              </div>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-lg-12">
                              <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                <label>{{ __('Room Meta Description') }}</label>
                                <textarea class="form-control" name="{{ $language->code }}_meta_description" rows="5" placeholder="Enter Meta Description">{{ $meta_description }}</textarea>
                              </div>
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
              <button type="submit" form="roomForm" class="btn btn-success">
                {{ __('Update') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('script')
  <script src="{{asset('assets/js/admin-room.js')}}"></script>
@endsection
