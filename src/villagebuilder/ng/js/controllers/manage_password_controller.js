
app.controller('ManagePasswordController', function($scope, $location, $http, Ajax, State, Request) {
    
    $scope.showView = false;
    
    
       
    $scope.$watch(function() {return State.authenticated}, function (value) {
            //do if logged in
            if (typeof value !== 'undefined' && value === true) {
                $scope.showView = true;
                Request.loadForm('password', Ajax.GET_PASSWORD);
            //do if logged out    
            } else if (typeof value !== 'undefined' && value === false){
                State.intendedLocation = '/home';
                $location.path( "/login" );
            }
    });
        

   
    $scope.updatePassword = function() {
        Request.password.inputErrors = {};
        Request.password.formError = "";
        $http.post(Ajax.PUT_PASSWORD, Request.password.request).
            success(function(data, status, headers, config) {
                State.debug = data;
            }).
            error(function(data, status, headers, config) {
                State.debug = status;
                if (status == 400)  {  //bad request (validation failed)
                    Request.password.inputErrors = data;
                } else if (data.hasOwnProperty('errorMessage')) {  //resource not found (record missing)
                    Request.password.formError = data.errorMessage;
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


