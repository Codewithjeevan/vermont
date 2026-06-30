<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

/**
 * Handles small page/redirect routes that were previously defined as route
 * closures in routes/web.php. Moving them into a controller allows
 * `php artisan route:cache` to work (closure routes cannot be cached).
 */
class PageController extends Controller
{
    /** Debug helper — forces a 500 (was the `test` route). */
    public function test()
    {
        abort(500);
    }

    /** Renders the 404 error view (was the `404` route). */
    public function show404()
    {
        return view('errors.404');
    }

    /** Renders the 500 error view (was the `500` route). */
    public function show500()
    {
        return view('errors.500');
    }

    /** Renders the 401/access error view (was the `error/{page}` route). */
    public function showError($page)
    {
        return view('errors.401', ['page' => $page]);
    }

    /** Post-login landing redirect by role (was the `/home` route). */
    public function home()
    {
        if (Auth::user()->roleId == 3) {
            if (\Common::can_billscreen_view('billing', 'billscreen') == 2) {
                return redirect('billing/billing2');
            }

            return redirect('billing/billing');
        }

        return redirect('dashboard/dashboard');
    }
}
