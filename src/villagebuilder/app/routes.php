<?php

/*
 * Newly added/uncategorized
 */
Route::post('api/post-guest', array(
    'as' => 'api-post-guest',
    'uses' => 'ApiGuest@postGuest'
));
Route::get('api/get-friendship-using-email', array(
    'as' => 'api-get-friendship-using-email',
    'uses' => 'ApiFriendship@getFriendshipUsingEmail'
));
Route::get('api/get-participant', array(
    'as' => 'api-get-participant',
    'uses' => 'ApiParticipant@getParticipant'
));
Route::post('api/put-approve-membership', array(
    'as' => 'api-put-approve-membership',
    'uses' => 'ApiMembership@putApproveMembership'
));
Route::get('api/get-add-relationship-by-email', array(
    'as' => 'api-get-add-realtionship-by-email',
    'uses' => 'ApiRelationship@getAddRelationshipByEmail'
));
Route::get('api/get-add-relationship-by-email-verify', array(
    'as' => 'api-get-add-realtionship-by-email-verify',
    'uses' => 'ApiRelationship@getAddRelationshipByEmailVerify'
));
Route::post('api/post-add-relationship-by-email', array(
    'as' => 'api-post-add-realtionship-by-email',
    'uses' => 'ApiRelationship@postAddRelationshipByEmail'
));
Route::post('api/post-delete-alert', array(
    'as' => 'api-post-delete-alert',
    'uses' => 'ApiAlert@postDeleteAlert'
));
Route::get('api/guest-login', array(
    'as' => 'api-guest-login',
    'uses' => 'ApiGuest@guestLogin'
));









/*
 * SPECIAL SECURITY HANDLING
 * check if provided User ID and currently logged in User ID logged match
 * this is done directly in the controller (for now)
 */
Route::get('api/get-person', array(
    'as' => 'api-get-person',
    'uses' => 'ApiPerson@getPerson'
));
Route::post('api/delete-person', array(
    'as' => 'api-delete-person',
    'uses' => 'ApiPerson@deletePerson'
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
Route::post('api/post-user-image', array(
    'as' => 'api-post-user-image',
    'uses' => 'ApiImages@postUserImage'
));





Route::group(array('before' => 'csrf'), function() {
    /*
     * CROSS-SITE FORGERY PROTECTION ONLY (LOGGED IN OR LOGGED OUT OK)
     */
    Route::post('api/post-person', array(
        'as' => 'api-post-person',
        'uses' => 'ApiPerson@postPerson'
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
    Route::get('api/get-password', array(
        'as' => 'api-get-password',
        'uses' => 'ApiPerson@getPassword'
    ));
    Route::get('api/get-group', array(
        'as' => 'api-get-group',
        'uses' => 'ApiGroup@getGroup'
    ));
    Route::post('api/delete-group', array(
        'as' => 'api-delete-group',
        'uses' => 'ApiGroup@deleteGroup'
    ));
    Route::get('api/get-collection-nearby-people', array(
        'as' => 'api-get-collection-nearby-people',
        'uses' => 'ApiParticipant@getCollectionNearbyPeople'
    ));
    Route::post('api/post-friendship', array(
        'as' => 'api-post-friendship',
        'uses' => 'ApiFriendship@postFriendship'
    ));
    Route::get('api/get-collection-friendship', array(
        'as' => 'api-get-collection-friendship',
        'uses' => 'ApiFriendship@getCollectionFriendship'
    ));
    Route::post('api/delete-friendship', array(
        'as' => 'api-delete-friendship',
        'uses' => 'ApiFriendship@deleteFriendship'
    ));
    Route::get('api/get-collection-alert', array(
        'as' => 'api-get-collection-alert',
        'uses' => 'ApiAlert@getCollectionAlert'
    ));
    Route::post('api/post-reset-unviewed-alert-count', array(
        'as' => 'api-post-reset-unviewed-alert-count',
        'uses' => 'ApiAlert@postResetUnviewedAlertCount'
    ));
    Route::get('api/get-collection-search-participants', array(
        'as' => 'api-get-collection-search-participants',
        'uses' => 'ApiParticipant@getCollectionSearchParticipants'
    ));
    Route::post('api/post-group-membership', array(
        'as' => 'api-post-group-membership',
        'uses' => 'ApiMembership@postMembership'
    ));
    Route::post('api/delete-group-membership', array(
        'as' => 'api-delete-group-membership',
        'uses' => 'ApiMembership@deleteMembership'
    ));

    
    
    
    
    
    

    

    Route::group(array('before' => 'csrf'), function() {
        
        /*
         * LOGGED IN AND CROSS-SITE FORGERY PROTECTION
         */
        Route::post('api/put-person', array(
            'as' => 'api-put-person',
            'uses' => 'ApiPerson@putPerson'
        ));
        Route::post('api/put-password', array(
            'as' => 'api-put-password',
            'uses' => 'ApiPerson@putPassword'
        ));
        Route::post('api/post-group', array(
            'as' => 'api-post-group',
            'uses' => 'ApiGroup@postGroup'
        ));
        Route::post('api/put-group', array(
            'as' => 'api-put-group',
            'uses' => 'ApiGroup@putGroup'
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



