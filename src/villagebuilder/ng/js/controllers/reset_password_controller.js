
app.controller('ResetPasswordController', function($scope, $location, $http, Ajax, State) {
    
    $scope.showView = false;
    
    $scope.passwordRequest = {};
    $scope.passwordRequestMeta = {};
    $scope.passwordFormDataLoaded = false;
    
    //check authentication and redirect
    $scope.$watch(function() {return State.authenticated}, function (value) {
            //do if logged in
            if (typeof value !== 'undefined' && value === true) {
                $location.path( State.intendedLocation );
            //do if logged out    
            } else if (typeof value !== 'undefined' && value === false){
                $scope.showView = true;
                getFormData();
            }
    });
     
    function getFormData() {
       State.debug="started";
       $http.get(Ajax.GET_RESET_PASSWORD).
            success(function(data, status, headers, config) {
                $scope.passwordRequest = data.values;
                $scope.passwordRequestMeta = data.meta;
                $scope.passwordFormDataLoaded = true;
                State.debug = data;
            }).
            error(function(data, status, headers, config) {
                State.debug = data;
            });
   }
   
    $scope.updatePassword = function() {
        //State.debug = $scope.request;
        $http.post(Ajax.POST_RESET_PASSWORD, $scope.passwordRequest).
            success(function(data, status, headers, config) {
                State.debug = data;
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
                        {"link" : "#/home", "description": "Return to Home Page"}
                    ];
                    $location.path( '/info' );
                }
            });
    }
    
  


}); 


