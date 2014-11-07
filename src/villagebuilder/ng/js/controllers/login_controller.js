app.controller('LoginController', function($scope, $location, $http, Ajax, State, Request) {


    $scope.showView = false;
    
    //check authentication and redirect or load forms and show view
    $scope.$watch(function() {return State.authenticated}, function (value) {
            //do if logged in
            if (typeof value !== 'undefined' && value === true) {
                $location.path( State.intendedLocation );
            //do if logged out    
            } else if (typeof value !== 'undefined' && value === false){
                $scope.showView = true;
                Request.loadForm('login', Ajax.GET_LOG_IN);
            }
    });

    //log user in
    $scope.logIn = function() {
        Request.login.inputErrors = {};
        Request.login.formError = "";
        $http.post(Ajax.POST_LOG_IN, Request.login.request).
            success(function(data, status, headers, config) {
                State.debug = status;
                State.userId = data.user_id;
                State.authenticated = true;
            }).
            error(function(data, status, headers, config) {
                State.debug = status;
                if (status == 400)  {  //bad request (validation failed)
                    Request.login.inputErrors = data;
                } else if (data.hasOwnProperty('errorMessage')) {  //resource not found (record missing)
                    Request.login.formError = data.errorMessage;
                    State.debug="error message found";
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



