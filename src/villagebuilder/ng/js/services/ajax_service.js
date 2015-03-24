
app.service("Ajax", function() {
    
    var root = "http://johnmeinken.com/vb-dev/src/villagebuilder/public/api/";
    
    
    this.GET_PARTICIPANT = root + 'get-participant';
    
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
    this.GET_ACCOUNT = root + 'get-person';
    this.POST_ACCOUNT = root + 'post-person';
    this.PUT_ACCOUNT = root + 'put-person';
    this.DELETE_ACCOUNT = root + 'delete-person';
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
     * Guest API
     */
    this.GET_GUEST = root + 'get-guest';
    this.POST_GUEST = root + 'post-guest';
    
    /*
     * Group Member API
     */
    this.POST_GROUP_MEMBERSHIP = root + 'post-group-membership';
    this.DELETE_GROUP_MEMBERSHIP = root + 'delete-group-membership';
    this.PUT_APPROVE_MEMBERSHIP = root + 'put-approve-membership';
    
    /*
     * Friendship API
     */
    this.GET_COLLECTION_NEARBY_PEOPLE = root + 'get-collection-nearby-people';
    this.POST_FRIENDSHIP = root + 'post-friendship';
    this.DELETE_FRIENDSHIP = root + 'delete-friendship';
    this.GET_COLLECTION_FRIENDSHIP = root + 'get-collection-friendship';
    this.GET_COLLECTION_SEARCH_PARTICIPANTS = root + 'get-collection-search-participants';
    this.POST_FRIENDSHIP_USING_EMAIL = root + 'post-friendship-using-email';
    
    /*
     * Alert API
     */
    this.GET_COLLECTION_ALERT = root + 'get-collection-alert';
    this.POST_RESET_UNVIEWED_ALERT_COUNT = root + 'post-reset-unviewed-alert-count';
    
    /*
     * Error Messages
     */

    this.ERROR_GENERAL_TITLE = "That Wasn't Supposed to Happen";
    this.ERROR_GENERAL_DESCRIPTION = 'There was an error processing your request. ' +
                        "Please try again.  If the problem persists, contact us. ";
 
 
    
 
 
 

});