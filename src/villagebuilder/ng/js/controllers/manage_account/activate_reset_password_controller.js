
app.controller('ActivateResetPasswordController', function($scope, $location, $http, $stateParams, Ajax, State) {
    
    //user is not redirected even if they are logged in
    
    $scope.code = $stateParams.code;
    
    $scope.activateResetPassword = function() {
        $http.post(Ajax.POST_ACTIVATE_RESET_PASSWORD, {'code': $stateParams.code }).
            success(function(data, status, headers, config) {
                State.infoTitle = "Password Reset";
                State.infoMessage = "You can now log in with the temporary password we sent you.";
                State.infoLinks = [
                    {"link" : "#/login", "description": "Go to Login Page"}
                ];
                $location.path( '/info' );
            }).
            error(function(data, status, headers, config) {
                State.debug = status;
                if (data.hasOwnProperty('errorMessage')) {
                    State.infoTitle = "Error";
                    State.infoMessage = data.errorMessage;
                } else {
                    State.infoTitle = Ajax.ERROR_GENERAL_TITLE;
                    State.infoMessage = Ajax.ERROR_GENERAL_DESCRIPTION;
                }
                State.infoLinks = [
                    {"link" : "#/login", "description": "Go to Login Page"}
                ];
                $location.path( '/info' );
            });
    }



}); 


