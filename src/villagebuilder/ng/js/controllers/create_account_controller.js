
app.controller('CreateAccountController', function($scope, $location, $http, $routeParams, Ajax, State) {
    
    //if user is logged in, redirect
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
    
    $scope.testVar = "hello";
    $scope.showView = false;
    
    $scope.code = $routeParams.code;
    
    $scope.request = {};
    $scope.requestMeta = {};
    $scope.formDataLoaded = false;
    $scope.requestErrors = {};

    $scope.createAccount = function() {
        $http.post(Ajax.POST_ACCOUNT, this.request).
            success(function(data, status, headers, config) {
                State.debug = status;
                State.infoTitle = "Almost There";
                State.infoMessage = "Check your email to confirm account creation."
                $location.path( '/info' );
            }).
            error(function(data, status, headers, config) {
                State.debug = status;
                if (status == 400)  {  //bad request (validation failed)
                    $scope.requestErrors = data;
                } else if (data.hasOwnProperty('errorMessage')) {  //resource not found (record missing)
                    $scope.formErrorMessage = data.errorMessage;
                } else {  // token mismatch (500), unauthorized (401), etc.
                    State.infoTitle = Ajax.ERROR_GENERAL_TITLE;
                    State.infoMessage = Ajax.ERROR_GENERAL_DESCRIPTION;
                    State.infoLinks = [
                        {"link" : "#/login", "description": "Return to Login Page"}
                    ];
                    $location.path( '/info' );
                }
            });
    }

    
    $scope.initializeView = function() {
        // load data for account form
        $http.get(Ajax.GET_ACCOUNT).
            success(function(data, status, headers, config) {
                $scope.request = data.values;
                $scope.requestMeta = data.meta;
                $scope.formDataLoaded = true;
                State.debug = "account";
            }).
            error(function(data, status, headers, config) {
                State.debug = "failure";
            });
    }
    
    
    

   
 



}); 


