
app.service("State", function($http, $location, Ajax) {
    
    this.debug = "no messages";
    
    //used by '#/info' view to give simple message to user
    this.infoTitle = "";
    this.infoMessage = ""

    //true/false if user logged in/out; undefined if check has not been performed
    this.authenticated; 
    
    //if user tries to access a protected page and is forced to log in, they
    // will be redirected back to the page set here after successful login
    this.intendedLocation = '/home';
    
    //user information
    this.userId = "";
    

    
/***AJAX Requests**************************************************************/
    
    //used by success and error functions to access this Data object
    var self = this;

    this.authenticate = function() {   
        $http.get(Ajax.CHECK_LOGIN_STATUS).
            success(function(data, status, headers, config) {
                self.authenticated = data.logged_in;
                self.userId = data.user_id;
                //self.debug = data;
            }).
            error(function(data, status, headers, config) {
                //self.debug = data;
            });
    };

    this.signOut = function() {   
        $http.get(Ajax.POST_LOG_OUT).
            success(function(data, status, headers, config) {
                self.authenticated = false;
            }).
            error(function(data, status, headers, config) {
                self.debug = "log out failed";
            });
    };


});