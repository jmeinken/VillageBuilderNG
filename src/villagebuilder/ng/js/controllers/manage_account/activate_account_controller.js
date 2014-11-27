
app.controller('ActivateAccountController', function($scope, $location, $http, $stateParams, Ajax, State) {
    
    //user is not redirected even if they are logged in
    
    $scope.code = $stateParams.code;
    
    $scope.activateAccount = function() {
        $http.post(Ajax.POST_ACTIVATE_ACCOUNT, {'code': $stateParams.code }).
            success(function(data, status, headers, config) {
                State.infoTitle = "Account Activated";
                State.infoMessage = "You can now log in with your email and password";
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


