app.config(function($stateProvider, $urlRouterProvider) {
    $urlRouterProvider.otherwise('/main/home');
    $stateProvider
        .state('tester', {
            url: '/tester',
            templateUrl: 'templates/tester.html?x=111',
            controller: 'TesterController'
        })
        .state('login', {
            url: '/login',
            templateUrl: 'templates/login.html?x=1',
            controller: 'LoggedOutController'
        })
        .state('create-account', {
            url: '/create-account',
            templateUrl: 'templates/create-account.html?x=7',
            controller: 'LoggedOutController'
        })
        .state('create-account.account-info', {
            url: '/account-info',
            templateUrl: 'templates/create-account/account-info.html?x=26'
        })
        .state('create-account.map', {
            url: '/map',
            templateUrl: 'templates/create-account/map.html?x=16',
            controller: 'MapController'
        })
        .state('create-account.personal-info', {
            url: '/personal-info',
            templateUrl: 'templates/create-account/personal-info.html?x=17',
            controller: 'UserImageController'
        })
        .state('reset-password', {
            url: '/reset-password',
            templateUrl: 'templates/reset-password.html?x=10',
            controller: 'LoggedOutController'
        })
        .state('activate-account', {
            url: '/activate-account/:code',
            templateUrl: 'templates/activate-account.html?x=6',
            controller: 'ActivateAccountController'
        })
        .state('activate-reset-password', {
            url: '/activate-reset-password/:code',
            templateUrl: 'templates/activate-reset-password.html?x=11',
            controller: 'ActivateResetPasswordController'
        })
        .state('guest', {
            url: '/guest/:code',
            templateUrl: 'templates/guest.html?r=2',
            controller: 'GuestController'
        })
        .state('info', {
            url: '/info',
            templateUrl: 'templates/info.html?x=5'
        })   
        //main is a wrapper for all views available only to logged-in users
        .state('main', {
            url: '/main',
            templateUrl: 'templates/main.html?x=8',
            controller: 'LoggedInController'
        })
        .state('main.home', {
            url: '/home',
            templateUrl: 'templates/main/home.html?x=2'
        })
        .state('main.friends', {
            url: '/friends',
            templateUrl: 'templates/main/friends.html?q=2'
        })
        .state('main.members', {
            url: '/members',
            templateUrl: 'templates/main/members.html?q=2'
        })
        .state('main.manage-account', {
            url: '/manage-account',
            templateUrl: 'templates/main/manage-account.html?x=8'
        })
        .state('main.manage-password', {
            url: '/manage-password',
            templateUrl: 'templates/main/manage-password.html?x=9'
        })
        .state('main.info', {
            url: '/info',
            templateUrl: 'templates/main/main-info.html?x=5'
        })   
        .state('main.create-group', {
            url: '/create-group',
            templateUrl: 'templates/main/create-group.html?j=8',
            controller: 'LoggedInController'
        })
        .state('main.create-group.account-info', {
            url: '/account-info',
            templateUrl: 'templates/create-account/account-info.html?l=16'
        })
        .state('main.create-group.map', {
            url: '/map',
            templateUrl: 'templates/create-account/map.html?l=16',
            controller: 'MapController'
        })
        .state('main.create-group.personal-info', {
            url: '/personal-info',
            templateUrl: 'templates/create-account/personal-info.html?m=17',
            controller: 'UserImageController'
        })
        .state('main.participant-search-results', {
            url: '/participant-search-results',
            templateUrl: 'templates/main/participant-search-results.html?m=17',
            controller: 'SearchParticipantsController'
        })
        .state('main.participant_view', {
            url: '/user/:participant_id',
            templateUrl: 'templates/participant-view.html?x=6',
            controller: 'ParticipantViewController'
        })
        .state('main.add-friends', {
            url: '/add-friends',
            templateUrl: 'templates/main/add-friends.html?x=6',
            controller: 'CreateGuestFormController'
        })
});


