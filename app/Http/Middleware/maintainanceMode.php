<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class MaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        // Check if maintenance mode is enabled from settings
        $maintenanceMode = '0'; // Default to off
        
        // Try to get setting, but handle if table doesn't exist yet
        try {
            $maintenanceMode = Setting::get('maintenance_mode', '0');
        } catch (\Exception $e) {
            // Table might not exist yet, just use default
        }
        
        if ($maintenanceMode == '1') {
            // Allow admins to access even in maintenance mode
            if (Auth::check() && Auth::user()->role === 'admin') {
                return $next($request);
            }
            
            // Skip maintenance for login, register, and maintenance page
            $allowedRoutes = ['login', 'register', 'maintenance'];
            if (!in_array($request->route()->getName(), $allowedRoutes)) {
                return redirect()->route('maintenance');
            }
        }
        
        return $next($request);
    }
}