
app.controller('ActivateAccountController', function($scope, $location, $http, $routeParams, Ajax, State) {
    
    //user is not redirected even if they are logged in
    
    $scope.code = $routeParams.code;
    
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


