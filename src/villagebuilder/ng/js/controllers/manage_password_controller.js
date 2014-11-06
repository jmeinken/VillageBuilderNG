
app.controller('ManagePasswordController', function($scope, $location, $http, Ajax, State) {
    
    $scope.showView = false;
    
    $scope.passwordRequest = {};
    $scope.passwordRequestMeta = {};
    $scope.passwordFormDataLoaded = false;
    
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
                getFormData();
            }
        }
    );
        
    function getFormData() {
       State.debug="started";
       $http.get(Ajax.GET_PASSWORD).
            success(function(data, status, headers, config) {
                $scope.passwordRequest = data.values;
                $scope.passwordRequestMeta = data.meta;
                $scope.passwordFormDataLoaded = true;
                State.debug = "account";
            }).
            error(function(data, status, headers, config) {
                State.debug = "failure";
            });
   }
   
    $scope.updatePassword = function() {
        //State.debug = $scope.accountRequest;
        $http.post(Ajax.PUT_PASSWORD, $scope.passwordRequest).
            success(function(data, status, headers, config) {
                State.debug = data;
            }).
            error(function(data, status, headers, config) {
                State.debug = data;
            });
    }
    
  


}); 


