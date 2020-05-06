<?php 

Route::group(['prefix' => 'api','middleware' => ['jwt.auth']], function () {
 
    Route::get('/partner', 'Scaupize1123\JustOfficalPartner\Controllers\Api\PartnerController@showPage')->name('partner.showPage');
    Route::get('/partner/{uuid}', 'Scaupize1123\JustOfficalPartner\Controllers\Api\PartnerController@showSingle')->name('partner.showSingle');
    Route::delete('/partner/{uuid}', 'Scaupize1123\JustOfficalPartner\Controllers\Api\PartnerController@delete')->name('partner.delete');
    Route::post('/partner', 'Scaupize1123\JustOfficalPartner\Controllers\Api\PartnerController@create')->name('partner.create');
    Route::put('/partner/{uuid}', 'Scaupize1123\JustOfficalPartner\Controllers\Api\PartnerController@update')->name('partner.update');      
});