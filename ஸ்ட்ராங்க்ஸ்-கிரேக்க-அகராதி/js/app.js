angular.module('bookApp',['ui.router','ngResource','bookApp.controllers','bookApp.services','textAngular','angularSpinner'])
.run(function($rootScope){
  $rootScope.Quizes = [];
});

angular.module('bookApp').config(function($stateProvider,$httpProvider){
    $stateProvider.state('index',{
        url:'/index',
        templateUrl:'partials/book.html?v=' + $VERSION,
        controller:'BookController'
    }).state('word',{
       url:'/:title/:id',
       templateUrl:'partials/chapter.html?v=' + $VERSION,
       controller:'ChapterController'
    }).state('contactUs',{
        url:'/contactus',
        templateUrl:'partials/contact-us.html?v=' + $VERSION,
        controller:'ContactUsController'
    }).state('aboutUs',{
        url:'/aboutus',
        templateUrl:'partials/about-us.html?v=' + $VERSION,
        controller:'AboutUsController'
    });
}).run(function($state){
   $state.go('index');
});
