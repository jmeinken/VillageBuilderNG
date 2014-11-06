
app.controller('ResetPasswordController', function($scope, $location, $http, Ajax, State) {
    
    $scope.showView = false;
    
    $scope.passwordRequest = {};
    $scope.passwordRequestMeta = {};
    $scope.passwordFormDataLoaded = false;
    
    //redirect if user is already logged in
    $scope.$watch(function() {return State.authenticated}, 
        function (value) {
            if (typeof value === 'undefined') {
                // do nothing and wait
            } else if (value === true) {
                //user already logged in; redirect to home page
                $location.path( State.intendedLocation );
            } else {
                //show this page
                $scope.showView = true;
                getFormData();
            }
        }
    );
        
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
        //State.debug = $scope.accountRequest;
        $http.post(Ajax.POST_RESET_PASSWORD, $scope.passwordRequest).
            success(function(data, status, headers, config) {
                State.debug = data;
            }).
            error(function(data, status, headers, config) {
                State.debug = data;
            });
    }
    
  


}); 


