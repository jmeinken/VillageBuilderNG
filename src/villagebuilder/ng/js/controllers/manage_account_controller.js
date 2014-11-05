
app.controller('ManageAccountController', function($scope, $location, $http, Ajax, State) {
    
    $scope.showView = false;
    
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
            }
        }
    );
    
    $scope.accountRequest = {};
    $scope.accountRequestMeta = {};
    $scope.accountFormDataLoaded = false;

    $scope.updateAccount = function() {
        /*
        $http.post(Ajax.POST_ACCOUNT, this.accountRequest).
            success(function(data, status, headers, config) {
                State.debug = status;
                State.infoTitle = "Almost There";
                State.infoMessage = "Check your email to confirm account creation."
                $location.path( '/info' );
            }).
            error(function(data, status, headers, config) {
                State.debug = status;
                State.infoTitle = "Error";
                State.infoMessage = data;
                $location.path( '/info' );
            });
        */
    }

    
    $scope.initializeView = function() {
        // load data for account form
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
    

    

   
 



}); 


