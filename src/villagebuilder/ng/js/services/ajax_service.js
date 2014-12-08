
app.service("Ajax", function() {
    
    var root = "http://johnmeinken.com/vb-dev/src/villagebuilder/public/api/";
    
    /*
     * Authentication API
     */
    this.CHECK_LOGIN_STATUS = root + 'check-login-status';
    this.GET_LOG_IN = root + 'get-log-in';
    this.POST_LOG_IN = root + 'post-log-in';
    this.POST_LOG_OUT = root + 'post-log-out';
    this.POST_ACTIVATE_ACCOUNT = root + 'post-activate-account';
    this.GET_RESET_PASSWORD = root + 'get-reset-password';
    this.POST_RESET_PASSWORD = root + 'post-reset-password';
    this.POST_ACTIVATE_RESET_PASSWORD = root + 'post-activate-reset-password';
    
    /*
     * Account API
     */
    this.GET_ACCOUNT = root + 'get-account';
    this.POST_ACCOUNT = root + 'post-account';
    this.PUT_ACCOUNT = root + 'put-account';
    this.DELETE_ACCOUNT = root + 'delete-account';
    this.GET_PASSWORD = root + 'get-password';
    this.PUT_PASSWORD = root + 'put-password';
    this.POST_USER_IMAGE = root + 'post-user-image';
    
    /*
     * Group API
     */
    this.GET_GROUP = root + 'get-group';
    this.POST_GROUP = root + 'post-group';
    this.PUT_GROUP = root + 'put-group';
    this.DELETE_GROUP = root + 'delete-group';
    
    /*
     * Friendship API
     */
    this.GET_COLLECTION_NEARBY_PEOPLE = root + 'get-collection-nearby-people';
    this.POST_FRIENDSHIP = root + 'post-friendship';
    this.DELETE_FRIENDSHIP = root + 'delete-friendship';
    this.GET_COLLECTION_FRIENDSHIP = root + 'get-collection-friendship';
    
    /*
     * Error Messages
     */

    this.ERROR_GENERAL_TITLE = "That Wasn't Supposed to Happen";
    this.ERROR_GENERAL_DESCRIPTION = 'There was an error processing your request. ' +
                        "Please try again.  If the problem persists, contact us. ";
 

});