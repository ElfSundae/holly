<?php

Route::get('captcha/{config?}', '\Mews\Captcha\CaptchaController@getCaptcha');

Route::get('/', function () {
    return api('hello');
});
