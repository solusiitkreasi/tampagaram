{{-- bootstrap css --}}
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

{{-- jQuery-ui css --}}
<link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}">

{{-- plugins css --}}
<link rel="stylesheet" href="{{ asset('assets/css/plugins.min.css') }}">

{{-- default css --}}
<link rel="stylesheet" href="{{ asset('assets/css/default.css') }}">

{{-- main css --}}
<link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">

{{-- responsive css --}}
<link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}">

{{-- right-to-left css --}}
@if ($currentLanguageInfo->direction == 1)
  <link rel="stylesheet" href="{{ asset('assets/css/rtl.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/rtl-responsive.css') }}">
@endif

{{-- base-color css using a php file --}}
<link
  rel="stylesheet"
  href="{{ asset('assets/css/base-color.php?color1=' . $websiteInfo->primary_color . '&color2=' . $websiteInfo->secondary_color) }}"
>

<style>
    .breadcrumb-area::after {
        background-color: #{{$websiteInfo->breadcrumb_overlay_color}};
        opacity: {{$websiteInfo->breadcrumb_overlay_opacity}};
    }
</style>
