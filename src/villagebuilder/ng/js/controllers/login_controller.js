app.controller('LoginController', function($scope, $location, $http, Ajax, State) {

    //redirect if user is already logged in
    $scope.$watch(function() {return State.authenticated}, 
        function (value) {
            if (typeof value === 'undefined') {
                // do nothing and wait
            } else if (value === true) {
                //user already logged in; redirect to home page
                $location.path( State.intendedLocation );
            } else {
                //show this page
                $scope.showView = true;
            }
        }
    );


    //show login view
    $scope.showView = false;
    
    //stores data for login form
    $scope.loginRequest = {};
    $scope.loginRequestMeta = {};
    $scope.loginFormDataLoaded = false;
    
    //login error message
    $scope.loginErrorMessage = "";


    //loads data for login form
    $scope.initializeView = function() {
        $http.get(Ajax.GET_LOG_IN).
            success(function(data, status, headers, config) {
                $scope.loginRequest = data.values;
                $scope.loginRequestMeta = data.meta;
                $scope.loginFormDataLoaded = true;
            }).
            error(function(data, status, headers, config) {
            });
    }
    
    //log user in
    $scope.logIn = function() {
        $http.post(Ajax.POST_LOG_IN, this.loginRequest).
            success(function(data, status, headers, config) {
                State.debug = status;
                State.userId = data.user_id;
                State.authenticated = true;
            }).
            error(function(data, status, headers, config) {
                State.debug = data;
                $scope.loginErrorMessage = "Login failed.  Please check that the "
                    + "username and password are valid.";
                $scope.loginRequest.password = "";
            });
    }

    
    

   
 



}); 



