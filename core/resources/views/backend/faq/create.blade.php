<div
  class="modal fade"
  id="createModal"
  tabindex="-1"
  role="dialog"
  aria-labelledby="exampleModalCenterTitle"
  aria-hidden="true"
>
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">{{ __('Add FAQ') }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form
          id="ajaxForm"
          class="modal-form"
          action="{{ route('admin.faq_management.store_faq', ['language' => request()->input('language')]) }}"
          method="post"
        >
          @csrf
          <div class="form-group">
            <label for="">{{ __('Question*') }}</label>
            <input
              type="text"
              class="form-control"
              name="question"
              placeholder="Enter Question"
            >
            <p id="err_question" class="mt-1 mb-0 text-danger em"></p>
          </div>

          <div class="form-group">
            <label for="">{{ __('Answer*') }}</label>
            <textarea
              class="form-control"
              name="answer"
              rows="5"
              cols="80"
              placeholder="Enter Answer"
            ></textarea>
            <p id="err_answer" class="mt-1 mb-0 text-danger em"></p>
          </div>

          <div class="form-group">
            <label for="">{{ __('FAQ Serial Number*') }}</label>
            <input
              type="number"
              class="form-control ltr"
              name="serial_number"
              placeholder="Enter FAQ Serial Number"
            >
            <p id="err_serial_number" class="mt-1 mb-0 text-danger em"></p>
            <p class="text-warning mt-2">
              <small>{{ __('The higher the serial number is, the later the FAQ will be shown.') }}</small>
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
