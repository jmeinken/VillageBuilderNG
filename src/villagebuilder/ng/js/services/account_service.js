
app.service("Account", function($http, $location) {
    
    this.debug = "no messages";
    
    //used by '#/info' view to give simple message to user
    this.infoTitle = "";
    this.infoMessage = ""

    //true/false if user logged in/out; undefined if check has not been performed
    this.authenticated; 
    
    //if user tries to access a protected page and is forced to log in, they
    // will be redirected back to the page set here after successful login
    this.intendedLocation = '/home';
    
    //stores login form
    this.loginRequest = {};
    this.loginRequestMeta = {};
    this.loginFormDataLoaded = false;
    
    this.accountRequest = {};
    this.accountRequestMeta = {};
    this.accountFormDataLoaded = false;

   

    
/***AJAX Requests**************************************************************/
    
    //used by success and error functions to access this Data object
    var self = this;
    
   
    
    this.authenticate = function() {   
        $http.get('http://johnmeinken.com/vb-dev/src/villagebuilder/public/api/check-login-status').
            success(function(data, status, headers, config) {
                self.authenticated = data.logged_in;
                self.debug = data;
            }).
            error(function(data, status, headers, config) {
                self.debug = data;
            });
    };
    
    this.loadLoginMeta = function() {   
        $http.get('http://johnmeinken.com/vb-dev/src/villagebuilder/public/api/get-log-in').
            success(function(data, status, headers, config) {
                self.loginRequest = data.defaults;
                self.loginRequestMeta = data.meta;
                self.loginFormDataLoaded = true;
            }).
            error(function(data, status, headers, config) {
            });
    };
    
    this.loadAccountMeta = function() {   
        $http.get('http://johnmeinken.com/vb-dev/src/villagebuilder/public/api/get-account').
            success(function(data, status, headers, config) {
                self.accountRequest = data.defaults;
                self.accountRequestMeta = data.meta;
                self.accountFormDataLoaded = true;
            }).
            error(function(data, status, headers, config) {
            });
    };
    this.createAccount = function() {   
        $http.post('http://johnmeinken.com/vb-dev/src/villagebuilder/public/api/post-account', this.accountRequest).
            success(function(data, status, headers, config) {
                self.debug = status;
                self.infoTitle = "Almost There";
                self.infoMessage = "Check your email to confirm account creation."
                $location.path( '/info' );
            }).
            error(function(data, status, headers, config) {
                self.debug = status;
                self.infoTitle = "Error";
                self.infoMessage = data;
                $location.path( '/info' );
            });
    };
    
    this.signIn = function() {   
        $http.post('http://johnmeinken.com/vb-dev/src/villagebuilder/public/api/post-log-in', this.loginRequest).
            success(function(data, status, headers, config) {
                self.debug = status;
                self.authenticated = true;
            }).
            error(function(data, status, headers, config) {
                self.debug = status;
            });
    };
    
    this.signOut = function() {   
        $http.get('http://johnmeinken.com/vb-dev/src/villagebuilder/public/api/post-log-out').
            success(function(data, status, headers, config) {
                self.authenticated = false;
            }).
            error(function(data, status, headers, config) {
                self.debug = "log out failed";
            });
    };
    
    this.activateAccount = function(code) { 
        //self.debug = code;
        $http.post('http://johnmeinken.com/vb-dev/src/villagebuilder/public/api/post-activate-account', 
                {'code': code }).
            success(function(data, status, headers, config) {
                self.infoTitle = "Account Activated";
                self.infoMessage = "You can now log in with your email and password"
                $location.path( '/info' );
            }).
            error(function(data, status, headers, config) {
                self.debug = data;
            });
    };

 

});