
app.controller('ManageAccountController', function($scope, $location, $http, Ajax, State) {
    
    $scope.showView = false;
    
    $scope.accountRequest = {};
    $scope.accountRequestMeta = {};
    $scope.accountFormDataLoaded = false;
    
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
       $http.get(Ajax.GET_ACCOUNT, { params: { user_id: State.userId } }).
            success(function(data, status, headers, config) {
                $scope.accountRequest = data.values;
                $scope.accountRequestMeta = data.meta;
                $scope.accountFormDataLoaded = true;
                State.debug = "account";
            }).
            error(function(data, status, headers, config) {
                State.debug = "failure";
            });
   }
   
    $scope.updateAccount = function() {
        //State.debug = $scope.accountRequest;
        $http.post(Ajax.PUT_ACCOUNT, $scope.accountRequest).
            success(function(data, status, headers, config) {
                State.debug = data;
            }).
            error(function(data, status, headers, config) {
                State.debug = data;
            });
    }
    
    //load form data if authentication has finished
    /*
    $scope.$watch('showView', 
        function (newValue, oldValue) {
            if (newValue === true) {
                $http.get(Ajax.GET_ACCOUNT + '?user_id=' + State.userId).
                success(function(data, status, headers, config) {
                    $scope.accountRequest = data.values;
                    $scope.accountRequestMeta = data.meta;
                    $scope.accountFormDataLoaded = true;
                    State.debug = "account";
                }).
                error(function(data, status, headers, config) {
                    State.debug = "failure";
                });
            }
        }
    );
    */
   
   
    
    

   
 
    
    

    

   
 



}); 


