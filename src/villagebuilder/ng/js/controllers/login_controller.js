app.controller('LoginController', function($scope, $location, State) {

$scope.showView = false;
 
$scope.signIn = function() {
    State.signIn();
}

$scope.initializeView = function() {
    State.loadLoginMeta();
}

//if user is logged in
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
            }
        }
    );
    

   
 



}); 



