
app.controller('GlobalController', function($scope, $state, State, Request, Utilities) {
    
    //the entire State service is globally available to all templates
    $scope.State = State;
    $scope.Request = Request;
    $scope.$state = $state;

    
    
    
    $scope.keyArray = Utilities.keyArray;
    
   
    

   
 



}); 


