app.controller('LoginController', function($scope, $location, $http, Ajax, State) {


    $scope.showView = false;
    
    //stores data for login form
    $scope.request = {};
    $scope.requestMeta = {};
    $scope.formDataLoaded = false;
    $scope.requestErrors = {};
    $scope.formErrorMessage = "";
    
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

    //loads data for login form
    function getFormData() {
        $http.get(Ajax.GET_LOG_IN).
            success(function(data, status, headers, config) {
                $scope.request = data.values;
                $scope.requestMeta = data.meta;
                $scope.formDataLoaded = true;
                State.debug = status;
            }).
            error(function(data, status, headers, config) {
                
            });
    }
    
    //log user in
    $scope.logIn = function() {
        $scope.requestErrors = {};
        $scope.formErrorMessage = "";
        $http.post(Ajax.POST_LOG_IN, this.request).
            success(function(data, status, headers, config) {
                State.debug = status;
                State.userId = data.user_id;
                State.authenticated = true;
            }).
            error(function(data, status, headers, config) {
                State.debug = status;
                if (status == 400)  {  //bad request (validation failed)
                    $scope.requestErrors = data;
                } else if (data.hasOwnProperty('errorMessage')) {  //resource not found (record missing)
                    $scope.formErrorMessage = data.errorMessage;
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



