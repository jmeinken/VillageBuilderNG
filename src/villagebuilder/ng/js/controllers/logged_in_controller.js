
app.controller('LoggedInController', function($scope, $location, State) {
    

    $scope.showPage = false;
    
    //check authentication and redirect or show page
    $scope.$watch(function() {return State.authenticated}, function (value) {
            //do if logged in
            if (typeof value !== 'undefined' && value === true) {
                $scope.showPage = true;
            //do if logged out    
            } else if (typeof value !== 'undefined' && value === false){
                State.intendedLocation = '/main/home';  //!!shoudn't be hard-coded
                $location.path( "/login" );
            }
    });

}); 


