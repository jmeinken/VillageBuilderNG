
app.service("State", function($http, $location, $state, $window, Ajax) {
    
    this.debug = "no messages";
    this.showPage = false;
    
    //used by '#/info' view to give simple message to user
    this.infoTitle = "";
    this.infoMessage = "";
    this.infoLinks = [];

    //true/false if user logged in/out; undefined if check has not been performed
    this.authenticated; 
    //increments each time authentication is run (used by $scope.$watch to 
    //update related data
    
    
    this.userDataChanged = 0;
    this.participantDataChanged = 0;
    
    this.userId = "";
    
    //if user tries to access a protected page and is forced to log in, they
    // will be redirected back to the page set here after successful login
    this.intendedLocation = '/main/home';
    
    //user information
    this.allParticipants = [];
    this.activeParticipant = {};
    this.activeParticipantId = "";  //the active account is auto-updated when activeParticipantId is set
    
    //used by Manage Account view to show/hide editing for specific fields
    this.accountDataEditToggle = {};
    
    //used to store info about most recently uploaded image
    this.uploadedImageData = {};
    
    this.changedAddress = false;
    

    

    
/***AJAX Requests**************************************************************/
    
    //used by success and error functions to access this Data object
    var self = this;

    this.authenticate = function() {   
        $http.get(Ajax.CHECK_LOGIN_STATUS).
            success(function(data, status, headers, config) {
                self.authenticated = data.logged_in;
                self.userId = data.userId;
                self.allParticipants = [];
                self.allParticipants[0] = data.personalAccount;
                self.allParticipants = self.allParticipants.concat(data.groupAccounts);
                if (self.activeParticipantId == "") {
                    self.activeParticipantId = data.personalAccount.participantId;
                }
                for (var i=0; i<self.allParticipants.length; i++) {
                    if (self.allParticipants[i].participantId == self.activeParticipantId) {
                        self.activeParticipant = self.allParticipants[i];
                    }
                }
                //this should be the last thing that happens since it will trigger
                //other events that depend on this data
                self.participantDataChanged++;
                self.userDataChanged++;
                //self.debug = data;
            }).
            error(function(data, status, headers, config) {
                //self.debug = data;
            });
    };

    this.signOut = function() {   
        $http.get(Ajax.POST_LOG_OUT).
            success(function(data, status, headers, config) {
                //reload will erase all stored data and will also redirect to
                //login since the user is now signed out
                $window.location.reload();
            }).
            error(function(data, status, headers, config) {
                self.debug = "log out failed";
            });
    };


});