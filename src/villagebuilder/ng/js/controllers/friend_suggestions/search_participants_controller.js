app.controller('SearchParticipantsController', function($scope, $http, $state, Ajax, State) {

    //State.participantSearchString = "";
    //State.participantSearchResults = [];
    
    $scope.searchParticipants = function() {
        parameters = { 'search_string' : State.participantSearchString };
        $http.get(Ajax.GET_COLLECTION_SEARCH_PARTICIPANTS, {params: parameters}).
            success(function(data, status, headers, config){
                State.participantSearchResults = data;
                //State.debug = data;
                $state.go('main.participant-search-results');
            }).
            error(function(data, status, headers, config) {
                State.debug = "search participants query failed";
            });
    }
   
 



}); 


