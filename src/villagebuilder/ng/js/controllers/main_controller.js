app.controller('MainController', function($scope, $state, State, Request, Utilities) {
    

   $scope.$watch(function() {return State.activeId}, function(value) {
       for (var i=0; i<State.currentUserAccounts.length; i++) {
           if (State.currentUserAccounts[i].participantId == State.activeId) {
               State.activeAccount = State.currentUserAccounts[i];
           }
       }
   });
 



}); 

