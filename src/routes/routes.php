<?php

Route::get('zoho', 'ZohoController@zoho');
Route::get(config('zoho.redirect_to'), 'ZohoController@oauth2back');
