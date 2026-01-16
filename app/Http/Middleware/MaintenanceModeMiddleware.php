<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceModeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
  public function handle(Request $request, Closure $next): Response
  {
    if ($this->isInMaintenanceMode()) {
      $message = getSettings('maintenanceNotification');
      return response()->view('maintenanceBF1125end', compact('message'));
    }

    return $next($request);
  }

  private function isInMaintenanceMode()
  {
    return (!Auth::check() || !Auth::user()->hasPermissionTo('Доступ к админпанели')) && !getSettings('maintenanceStatus');
//    return Auth::check() && Auth::id() == 1;
  }
}
