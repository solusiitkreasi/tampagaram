<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">{{ __('Add Brand') }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form 
          id="ajaxForm" 
          class="modal-form" 
          action="{{ route('admin.home_page.brand_section.store_brand', ['language' => request()->input('language')]) }}" 
          method="POST"
        >
          @csrf
          <div class="form-group">
            <div class="thumb-preview" id="thumbPreview1">
              <img src="{{ asset('assets/img/noimage.jpg') }}" alt="...">
            </div>
            <br><br>

            <input type="hidden" id="fileInput1" name="brand_img">
            <button 
              id="chooseImage1" 
              class="choose-image btn btn-primary" 
              type="button" 
              data-multiple="false" 
              data-toggle="modal" 
              data-target="#lfmModal1"
            >{{ __('Choose Image') }}</button>
            <p id="err_brand_img" class="mt-2 mb-0 text-danger em"></p>
          </div>

          <div class="form-group">
            <label for="">{{ __('Brand\'s URL*') }}</label>
            <input type="url" class="form-control ltr" name="brand_url" placeholder="Enter Brand URL">
            <p id="err_brand_url" class="mt-2 mb-0 text-danger em"></p>
          </div>

          <div class="form-group">
            <label for="">{{ __('Serial Number*') }}</label>
            <input type="number" class="form-control ltr" name="serial_number" placeholder="Enter Serial Number">
            <p id="err_serial_number" class="mt-2 mb-0 text-danger em"></p>
            <p class="text-warning mt-2">
              <small>{{ __('The higher the serial number is, the later the brand will be shown.') }}</small>
            </p>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          {{ __('Close') }}
        </button>
        <button id="submitBtn" type="button" class="btn btn-primary">
          {{ __('Save') }}
        </button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade lfm-modal" id="lfmModal1" tabindex="-1" role="dialog" aria-labelledby="lfmModalTitle" aria-hidden="true">
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
