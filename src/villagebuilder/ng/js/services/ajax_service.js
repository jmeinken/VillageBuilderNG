
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
    
    /*
     * Account API
     */
    this.GET_ACCOUNT = root + 'get-account';
    this.POST_ACCOUNT = root + 'post-account';
    this.PUT_ACCOUNT = root + 'put-account';
    this.GET_PASSWORD = root + 'get-password';
    this.PUT_PASSWORD = root + 'put-password';


 

});