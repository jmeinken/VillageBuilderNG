<?php


/*
 * SPECIAL SECURITY HANDLING
 * check if provided User ID and currently logged in User ID logged match
 * this is done directly in the controller (for now)
 */
Route::get('api/get-account', array(
    'as' => 'api-get-account',
    'uses' => 'ApiAccount@getAccount'
));
Route::post('api/delete-account', array(
    'as' => 'api-delete-account',
    'uses' => 'ApiAccount@deleteAccount'
));












/*
 * NO SECURITY CHECK
 */
Route::get('/api/check-login-status', array(
    'as' => 'api-check-login-status',
    'uses' => 'ApiAuthentication@checkLoginStatus'
));
Route::post('api/post-activate-account', array(
    'as' => 'api-post-activate-account',
    'uses' => 'ApiAuthentication@postActivateAccount'
));
Route::post('api/post-activate-reset-password', array(
    'as' => 'api-post-activate-reset-password',
    'uses' => 'ApiAuthentication@postActivateResetPassword'
));
Route::get('api/test', array (
    'uses' => 'ApiAccount@getAccountCurrentValues'
));





Route::group(array('before' => 'csrf'), function() {
    /*
     * CROSS-SITE FORGERY PROTECTION ONLY (LOGGED IN OR LOGGED OUT OK)
     */
    Route::post('api/post-account', array(
        'as' => 'api-post-account',
        'uses' => 'ApiAccount@postAccount'
    ));

    
    
});





Route::group(array('before' => 'auth'), function() {
    
    /*
     * LOGGED IN
     */
    Route::get('api/post-log-out', array(
        'as' => 'api-post-log-out',
        'uses' => 'ApiAuthentication@postLogOut'
    ));
    Route::post('api/post-user-image', array(
        'as' => 'api-post-user-image',
        'uses' => 'ApiAccount@postUserImage'
    ));
    Route::get('api/get-password', array(
        'as' => 'api-get-password',
        'uses' => 'ApiAccount@getPassword'
    ));
    
    
    
    
    
    

    

    Route::group(array('before' => 'csrf'), function() {
        
        /*
         * LOGGED IN AND CROSS-SITE FORGERY PROTECTION
         */
        Route::post('api/put-account', array(
            'as' => 'api-put-account',
            'uses' => 'ApiAccount@putAccount'
        ));
        Route::post('api/put-password', array(
            'as' => 'api-put-password',
            'uses' => 'ApiAccount@putPassword'
        ));
        
        
       
        
        
        
        
        
        
    }); 
});

Route::group(array('before' => 'guest'), function() {
    /*
     * LOGGED OUT
     */
    Route::get('api/get-log-in', array(
        'as' => 'api-get-log-in',
        'uses' => 'ApiAuthentication@getLogIn'
    ));
    Route::get('api/get-reset-password', array(
        'as' => 'api-get-reset-password',
        'uses' => 'ApiAuthentication@getResetPassword'
    ));
    
    
    

    Route::group(array('before' => 'csrf'), function() {
        /*
         * LOGGED OUT AND CROSS-SITE FORGERY PROTECTION
         */
        Route::post('api/post-log-in', array(
            'as' => 'api-post-log-in',
            'uses' => 'ApiAuthentication@postLogIn'
        ));
        Route::post('api/post-reset-password', array(
            'as' => 'api-post-reset-password',
            'uses' => 'ApiAuthentication@postResetPassword'
        ));
        
        
        
        
        

    });
});



