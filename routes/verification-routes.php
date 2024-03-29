<?php

Route::middleware(['web', 'auth:' . Illuminate\Support\Facades\Config::get('admin-auth.defaults.guard')])->group(static function () {
    Route::namespace('Brackets\Verifications\Http\Controllers')->group(static function () {
        Route::get('/verify-code', 'VerificationController@showVerificationForm')->name('brackets/verifications/show');

        Route::post('/verify-code', 'VerificationController@verify')->name('brackets/verifications/verify')->middleware(['throttle:60,1']);

        Route::post('/resend-code', 'VerificationController@sendNewCode')->name('brackets/verifications/resend')->middleware(['throttle:60,1']);
    });
});
