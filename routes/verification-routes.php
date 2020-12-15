<?php

Route::middleware(['web'])->group(static function () {
    Route::namespace('Brackets\Verifications\Http\Controllers')->group(static function () {
        Route::get('/verify-code/{redirectTo}', 'VerificationController@showVerificationForm')->name('brackets/verifications/show');

        Route::post('/verify-code', 'VerificationController@verify')->name('brackets/verifications/verify');        //TODO: add throttle middleware !!!
    });
});
