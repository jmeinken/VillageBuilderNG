
app.controller('GlobalController', function($scope, State) {
    
    //the entire State service is globally available to all templates
    $scope.State = State;
    
    $scope.keyArray = function(obj) {  
        var arr = [];
        var i=0;
        for (var key in obj) {
            arr[i] = key;
            i++;
        }
        return arr;
    }
    
   
    

   
 



}); 


