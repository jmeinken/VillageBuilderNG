app.config(function($stateProvider, $urlRouterProvider) {
    $urlRouterProvider.otherwise('/main/home');
    $stateProvider
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
        .state('create-account.map', {
            url: '/map',
            templateUrl: 'templates/map.html?x=16',
            controller: 'MapController'
        })
        .state('create-account.user-image', {
            url: '/user-image',
            templateUrl: 'templates/user-image.html?x=17',
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
            templateUrl: 'templates/home.html?x=2',
        })
        .state('main.manage-account', {
            url: '/manage-account',
            templateUrl: 'templates/manage-account.html?x=8',
        })
        .state('main.manage-password', {
            url: '/manage-password',
            templateUrl: 'templates/manage-password.html?x=9',
        })
        .state('main.info', {
            url: '/info',
            templateUrl: 'templates/main-info.html?x=5'
        })   
});


