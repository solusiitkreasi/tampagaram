<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LfmPath
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle(Request $request, Closure $next)
  {
    // set the base path for laravel filemanager
    if (config('filesystems.disks.public.root') != 'assets/lfm') {
      config([
        'filesystems.disks.public.root' => 'assets/lfm'
      ]);
    }

    if (config('filesystems.disks.public.url') != url('/') . '/assets/lfm') {
      config([
        'filesystems.disks.public.url' => url('/') . '/assets/lfm'
      ]);
    }
    
    return $next($request);
  }
}
