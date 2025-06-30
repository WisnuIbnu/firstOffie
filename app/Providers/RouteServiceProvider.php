use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

public function boot()
{
    RateLimiter::for('global', function ($request) {
        return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
    });

    RateLimiter::for('check-booking', function ($request) {
        return Limit::perMinute(50)->by($request->ip());
    });

    RateLimiter::for('booking-transaction', function ($request) {
        return Limit::perMinute(10)->by($request->ip());
    });

    $this->routes(function () {
        Route::middleware('api')
            ->prefix('api')
            ->group(base_path('routes/api.php'));
    });
}
