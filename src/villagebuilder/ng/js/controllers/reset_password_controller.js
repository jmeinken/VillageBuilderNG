
app.controller('ResetPasswordController', function($scope, $location, $http, Ajax, State, Request) {
    
    $scope.showView = false;
    
    
    //check authentication and redirect
    $scope.$watch(function() {return State.authenticated}, function (value) {
            //do if logged in
            if (typeof value !== 'undefined' && value === true) {
                $location.path( State.intendedLocation );
            //do if logged out    
            } else if (typeof value !== 'undefined' && value === false){
                $scope.showView = true;
               Request.loadForm('resetPassword', Ajax.GET_RESET_PASSWORD);
            }
    });
     

   
    $scope.resetPassword = function() {
        Request.resetPassword.inputErrors = {};
        Request.resetPassword.formError = "";
        $http.post(Ajax.POST_RESET_PASSWORD, Request.resetPassword.request).
            success(function(data, status, headers, config) {
                State.debug = data;
            }).
            error(function(data, status, headers, config) {
                State.debug = status;
                if (status == 400)  {  //bad request (validation failed)
                    Request.resetPassword.inputErrors = data;
                } else if (data.hasOwnProperty('errorMessage')) {  //resource not found (record missing)
                    Request.resetPassword.formError = data.errorMessage;
                } else {  // token mismatch (500), unauthorized (401), etc.
                    State.infoTitle = Ajax.ERROR_GENERAL_TITLE;
                    State.infoMessage = Ajax.ERROR_GENERAL_DESCRIPTION;
                    State.infoLinks = [
                        {"link" : "#/home", "description": "Return to Home Page"}
                    ];
                    $location.path( '/info' );
                }
            });
    }
    
  


}); 


