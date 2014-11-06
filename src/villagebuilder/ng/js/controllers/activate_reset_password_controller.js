
app.controller('ActivateResetPasswordController', function($scope, $location, $http, $routeParams, Ajax, State) {
    
    //user is not redirected even if they are logged in
    
    $scope.code = $routeParams.code;
    
    $scope.activateAccount = function() {
        $http.post(Ajax.POST_ACTIVATE_RESET_PASSWORD, {'code': $routeParams.code }).
            success(function(data, status, headers, config) {
                State.infoTitle = "Password Reset";
                State.infoMessage = "You can now log in with the temporary password we sent you."
                $location.path( '/info' );
            }).
            error(function(data, status, headers, config) {
                State.debug = data;
            });
    }



}); 


