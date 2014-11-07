
app.controller('CreateAccountController', function($scope, $location, $http, Ajax, State, Request) {
    
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
                Request.loadForm('account', Ajax.GET_ACCOUNT);
                $scope.showView = true;
            }
        }
    );
    
    $scope.showView = false;
    
    
    $scope.createAccount = function() {
        Request.account.inputErrors = {};
        Request.account.formError = "";
        $http.post(Ajax.POST_ACCOUNT, Request.account.request).
            success(function(data, status, headers, config) {
                State.debug = status;
                State.infoTitle = "Almost There";
                State.infoMessage = "Check your email to confirm account creation.";
                State.infoLinks = [
                        {"link" : "#/login", "description": "Return to Login Page"}
                    ];
                $location.path( '/info' );
            }).
            error(function(data, status, headers, config) {
                State.debug = status;
                if (status == 400)  {  //bad request (validation failed)
                    Request.account.inputErrors = data;
                } else if (data.hasOwnProperty('errorMessage')) {  //resource not found (record missing)
                    Request.account.formError = data.errorMessage;
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



    
    
    

   
 



}); 


