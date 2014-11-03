<?php


Route::get('/', array(
    'as' => 'home',
    'uses' => 'HomeController@home'
    
));


/*
 * API
 */
Route::get('/api/check_login_status', array(
    'as' => 'api-check-login-status',
    'uses' => 'ApiController@getLoginStatus'
));
Route::get('api/meta-log-in', array(
    'as' => 'api-meta-log-in',
    'uses' => 'ApiController@metaLogIn'
));
Route::get('api/log-out', array(
    'as' => 'api-log-out',
    'uses' => 'ApiController@getLogOut'
));
Route::group(array('before' => 'csrf'), function() {
    Route::post('api/log-in', array(
        'as' => 'api-post-log-in',
        'uses' => 'ApiController@postLogIn'
    ));
});



















/*
 * Authenticated group 
 * (auth and guest are set in filters)
 */
Route::group(array('before' => 'auth'), function() {
    
    Route::get('/account/sign-out', array(
        'as' => 'account-sign-out',
        'uses' => 'AccountController@getSignOut'
    ));

    Route::get('/account/change-password', array(
        'as' => 'account-change-password',
        'uses' => 'AccountController@getChangePassword'
    ));
    
    /*
     *  Cross-site forgery protection
     */
    Route::group(array('before' => 'csrf'), function() {
        Route::post('/account/change-password', array(
            'as' => 'account-change-password-post',
            'uses' => 'AccountController@postChangePassword'
        ));
    });
    
});

/*
 * Unauthenticated group 
 */
Route::group(array('before' => 'guest'), function() {
    
    /*
     *  Cross-site forgery protection
     */
    Route::group(array('before' => 'csrf'), function() {
        
        //create account (post)
        Route::post('/account/create', array(
            'as' => 'account-create-post',
            'uses' => 'AccountController@postCreate'
        ));
        
        //sign in (POST)
        Route::post('/account/signin', array(
            'as' => 'account-sign-in-post',
            'uses' => 'AccountController@postSignIn'
        ));
        
        Route::post('/account/forgot-password', array(
       'as' => 'account-forgot-password-post',
        'uses' => 'AccountController@postForgotPassword'
    ));
        
    });
    
    //create account (GET)
    Route::get('/account/create', array(
        'as' => 'account-create',
        'uses' => 'AccountController@getCreate'
    ));
    
    Route::get('/account/activate/{code}', array(
        'as' => 'account-activate',
        'uses' => 'AccountController@getActivate'
        
    ));
    Route::get('/account/forgot-password', array(
       'as' => 'account-forgot-password',
        'uses' => 'AccountController@getForgotPassword'
    ));
    
    Route::get('/account/recover/{code}', array(
        'as' => 'account-recover',
        'uses' => 'AccountController@getRecover'
        
    ));
    
    
    
});

//sign in (GET)
Route::get('/account/signin', array(
    'as' => 'account-sign-in',
    'uses' => 'AccountController@getSignIn'
));


Route::get('/user/{username}', array(
    'as' => 'profile-user',
    'uses' => 'ProfileController@user' 
));


