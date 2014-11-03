
app.service("State", function($http, $location) {
    
    this.debug = "no messages";

    //true/false if user logged in/out; undefined if check has not been performed
    this.authenticated; 
    
    //if user tries to access a protected page and is forced to log in, they
    // will be redirected back to the page set here after successful login
    this.intendedLocation = '/home';
    
    //stores login form
    this.loginRequest = {};
    this.loginRequestMeta = {};
    this.loginFormDataLoaded = false;

   

    
/***AJAX Requests**************************************************************/
    
    //used by success and error functions to access this Data object
    var self = this;
    
   
    
    this.authenticate = function() {   
        $http.get('http://johnmeinken.com/vb-dev/src/villagebuilder/public/api/check_login_status').
            success(function(data, status, headers, config) {
                self.authenticated = data.logged_in;
                //self.authenticated = "worked";
            }).
            error(function(data, status, headers, config) {

            });
    };
    
    this.loadLoginMeta = function() {   
        $http.get('http://johnmeinken.com/vb-dev/src/villagebuilder/public/api/meta-log-in').
            success(function(data, status, headers, config) {
                self.loginRequest = data.defaults;
                self.loginRequestMeta = data.meta;
                self.loginFormDataLoaded = true;
            }).
            error(function(data, status, headers, config) {
            });
    };
    
    this.signIn = function() {   
        $http.post('http://johnmeinken.com/vb-dev/src/villagebuilder/public/api/log-in', this.loginRequest).
            success(function(data, status, headers, config) {
                self.debug = status;
                self.authenticated = true;
            }).
            error(function(data, status, headers, config) {
                self.debug = status;
            });
    };
    
    this.signOut = function() {   
        $http.get('http://johnmeinken.com/vb-dev/src/villagebuilder/public/api/log-out').
            success(function(data, status, headers, config) {
                self.authenticated = false;
            }).
            error(function(data, status, headers, config) {
                self.debug = "log out failed";
            });
    };

 

});