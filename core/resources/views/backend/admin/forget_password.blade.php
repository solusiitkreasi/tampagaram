<!DOCTYPE html>
<html>
  <head>
    {{-- required meta tags --}}
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, shrink-to-fit=no"
    >

    {{-- title --}}
    <title>{{ __('Forget Password') }}</title>

    {{-- fav icon --}}
    <link
      rel="shortcut icon"
      type="image/png"
      href="{{ asset('assets/img/' . $websiteInfo->favicon) }}"
    >

    {{-- bootstrap css --}}
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

    {{-- atlantis css --}}
    <link rel="stylesheet" href="{{asset('assets/css/atlantis.css')}}">

    {{-- admin-login css also using for forget password --}}
    <link rel="stylesheet" href="{{ asset('assets/css/admin-login.css') }}">
  </head>

  <body>
    {{-- forget password form start --}}
    <div class="forget-page">
      <div class="text-center mb-4">
        <img class="login-logo" src="{{ asset('assets/img/' . $websiteInfo->logo) }}" alt="logo">
      </div>

      <div class="form">
        @if (session()->has('success'))
            <div class="alert alert-success fade show" role="alert" style="font-size: 14px;">
            <strong>Success!</strong> {{session('success')}}
            </div>
        @endif
        <form
          class="forget-password-form"
          action="{{ route('admin.mail_for_forget_password') }}"
          method="POST"
        >
          @csrf
          <input type="email" name="email" placeholder="Enter Your Email" value="{{ old('email') }}"/>
          @if ($errors->has('email'))
            <p class="text-danger text-left">{{ $errors->first('email') }}</p>
          @endif

          <button type="submit">{{ __('proceed') }}</button>
        </form>

        <a class="back-to-login" href="{{ route('admin.login') }}">
          &lt;&lt; {{ __('Back') }}
        </a>
      </div>
    </div>
    {{-- forget password form end --}}


    {{-- jQuery --}}
    <script src="{{ asset('assets/js/jquery-3.4.1.min.js') }}"></script>

    {{-- popper js --}}
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>

    {{-- bootstrap js --}}
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>

    {{-- bootstrap notify --}}
    <script src="{{asset('assets/js/bootstrap-notify.min.js')}}"></script>

    {{-- fonts and icons script --}}
    <script src="{{asset('assets/js/webfont.min.js')}}"></script>
    <script>
        "use strict";
        var mainURL = "{{ url('/') }}";
    </script>
    {{-- admin forget password script --}}
    <script src="{{asset('assets/js/admin-login.js')}}"></script>

    @if (session()->has('warning'))
      <script>
        "use strict";
        var content = {};

        content.message = '{{ session('warning') }}';
        content.title = 'Warning!';
        content.icon = 'fa fa-bell';

        $.notify(content, {
          type: 'warning',
          placement: {
            from: 'top',
            align: 'right'
          },
          showProgressbar: true,
          time: 1000,
          delay: 4000
        });
      </script>
    @endif
  </body>
</html>
