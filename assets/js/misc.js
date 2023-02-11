(function ($) {
  "use strict";

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

$('#blogForm').on('submit', function(e) {
  $('.request-loader').addClass('show');
  e.preventDefault();

  let action = $('#blogForm').attr('action');
  let fd = new FormData(document.querySelector('#blogForm'));

  $.ajax({
    url: action,
    method: 'POST',
    data: fd,
    contentType: false,
    processData: false,
    success: function(data) {
      $('.request-loader').removeClass('show');

      if (data == 'success') {
        location.reload(true);
      }
    },
    error: function(error) {
      $('#blogErrors').show();
      let errors = ``;

      for (let x in error.responseJSON.errors) {
        errors += `<li>
          <p class="text-danger mb-0">${ error.responseJSON.errors[x][0] }</p>
        </li>`;
      }

      $('#blogErrors ul').html(errors);

      $('.request-loader').removeClass('show');

      $('html, body').animate({
        scrollTop: $('#blogErrors').offset().top - 100
      }, 1000);
    }
  });
});

  $('#socialForm').on('submit', function (e) {
    e.preventDefault();

    $('#inputIcon').val($('.iconpicker-component').find('i').attr('class'));
    document.getElementById('socialForm').submit();
  });

 
})(jQuery);
