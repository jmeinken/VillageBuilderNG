
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
    
    $scope.accountRequest = {};
    $scope.accountRequestMeta = {};
    $scope.accountFormDataLoaded = false;

    $scope.createAccount = function() {
        $http.post(Ajax.POST_ACCOUNT, this.accountRequest).
            success(function(data, status, headers, config) {
                State.debug = status;
                State.infoTitle = "Almost There";
                State.infoMessage = "Check your email to confirm account creation."
                $location.path( '/info' );
            }).
            error(function(data, status, headers, config) {
                State.debug = status;
                State.infoTitle = "Error";
                State.infoMessage = data;
                $location.path( '/info' );
            });
    }

    
    $scope.initializeView = function() {
        // load data for account form
        $http.get(Ajax.GET_ACCOUNT).
            success(function(data, status, headers, config) {
                $scope.accountRequest = data.defaults;
                $scope.accountRequestMeta = data.meta;
                $scope.accountFormDataLoaded = true;
                State.debug = "account";
            }).
            error(function(data, status, headers, config) {
                State.debug = "failure";
            });
    }
    
    $scope.activateAccount = function() {
        $http.post(Ajax.POST_ACTIVATE_ACCOUNT, {'code': $routeParams.code }).
            success(function(data, status, headers, config) {
                State.infoTitle = "Account Activated";
                State.infoMessage = "You can now log in with your email and password"
                $location.path( '/info' );
            }).
            error(function(data, status, headers, config) {
                State.debug = status;
            });
    }
    

   
 



}); 


