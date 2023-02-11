<div class="header-top-area section-bg">
    <div class="container-fluid">
      <div class="row align-items-center">
        <div class="col-lg-7 d-lg-block d-none">
          <ul class="top-contact-info list-inline">
            @if (!is_null($websiteInfo->address))
              <li><i class="far fa-map-marker-alt"></i>{{ $websiteInfo->address }}</li>
            @endif

            @if (!is_null($websiteInfo->support_contact))
              <li><i class="far fa-phone"></i>{{ $websiteInfo->support_contact }}</li>
            @endif
          </ul>
        </div>

        <div class="col-lg-5">
          <div class="top-right">
            <ul class="top-menu list-inline d-inline">
              @guest('web')
                <li><a href="{{ route('user.login') }}"><i class="fas fa-sign-in-alt mr-1"></i> {{ __('Login') }}</a></li>
                <li><a href="{{ route('user.signup') }}"><i class="fas fa-user-plus mr-1"></i> {{ __('Signup') }}</a></li>
              @endguest

              @auth('web')
                <li><a href="{{ route('user.dashboard') }}"><i class="far fa-user mr-1"></i> {{ __('Dashboard') }}</a></li>
                <li><a href="{{ route('user.logout') }}"><i class="fas fa-sign-out-alt mr-1"></i> {{ __('Logout') }}</a></li>
              @endauth
            </ul>

            @if (count($socialLinkInfos) > 0)
              <ul class="top-social-icon list-inline d-lg-inline-block d-none">
                @foreach ($socialLinkInfos as $socialLinkInfo)
                  <li>
                    <a href="{{ $socialLinkInfo->url }}"><i class="{{ $socialLinkInfo->icon }}"></i></a>
                  </li>
                @endforeach
              </ul>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
