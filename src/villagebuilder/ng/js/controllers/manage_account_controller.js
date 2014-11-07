
app.controller('ManageAccountController', function($scope, $location, $http, Ajax, State, Request) {
    
    $scope.showView = false;
    
    $scope.request = {};
    $scope.requestMeta = {};
    $scope.formDataLoaded = false;
    $scope.requestErrors = {};
    
    //redirect to login if not logged in
    $scope.$watch(function() {return State.authenticated}, 
        function (value) {
            if (typeof value === 'undefined') {
                // do nothing
            } else if (value === false) {
                // set intended page as home and redirect to login
                State.intendedLocation = '/home';
                $location.path( "/login" );
            } else {
                //load the page
                $scope.showView = true;
                Request.loadForm('account', Ajax.GET_ACCOUNT, { user_id: State.userId });
            }
        }
    );
        

   
    $scope.updateAccount = function() {
        Request.account.inputErrors = {};
        Request.account.formError = "";
        $http.post(Ajax.PUT_ACCOUNT, Request.account.request).
            success(function(data, status, headers, config) {
                State.debug = data;
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
                        {"link" : "#/home", "description": "Return to Home Page"}
                    ];
                    $location.path( '/info' );
                }
            });
    }
    

   
   
    
    

   
 
    
    

    

   
 



}); 


