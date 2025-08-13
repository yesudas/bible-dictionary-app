angular.module('bookApp.controllers', []).controller('BookController', function($scope, $stateParams, BookFactory, $filter, $rootScope, PagerService) {
  if($rootScope.Words==undefined){
    BookFactory.allChapters().then(function(data) {
      //alert('allQuizes: '+JSON.stringify(data));
      $scope.Words = [];
      for (var i = 0; i < Object.keys(data.words).length; i++) {
        $scope.Words.push(data.words[i]);
      }
      $rootScope.Words = $scope.Words;
      initPagination();
    });
  }else{
    $scope.Words = $rootScope.Words;
  }

  $scope.searchDictionary = function(){
    if($scope.searchString==="" || $scope.searchString == undefined){
      $scope.Words = $rootScope.Words;
      $rootScope.vm = undefined;
    }else{
      $scope.Words = $filter('filter')($rootScope.Words, { word: $scope.searchString });
      $rootScope.vm = undefined;
    }
    initPagination();
  }

  function initPagination(){
    if($rootScope.vm==undefined){
      $scope.vm = [];
      $scope.vm.dummyItems = $scope.Words;
      $scope.vm.pager = {};
      $scope.vm.setPage = setPage;
      setPage(1);
      $rootScope.vm = $scope.vm;
    }else{
      $scope.vm = $rootScope.vm;
    }
  }
  function setPage(page) {
      //if (page < 1 || page > vm.pager.totalPages) {
        //  return;
      //}
      // get pager object from service
      $scope.vm.pager = PagerService.GetPager($scope.vm.dummyItems.length, page);
      // get current page of items
      $scope.vm.items = $scope.vm.dummyItems.slice($scope.vm.pager.startIndex, $scope.vm.pager.endIndex + 1);
  }

}).controller('ChapterController', function($scope, $stateParams, BookFactory, $filter, usSpinnerService, $rootScope, $location, $window) {
  BookFactory.chapterById($stateParams.id).then(function(data) {
    $scope.Word = data
    usSpinnerService.stop('spinner-1');
  });
  $scope.goBack = function() {
        $window.history.back();
  };
}).controller('ContactUsController',function($scope,$state,$stateParams,BookFactory,$timeout){
    $scope.yourname = '';
    $scope.youremail = '';
    $scope.yourmobile = '';
    $scope.yourmessage = '';
    $scope.message = '';
    $scope.sendContactUs=function(){
        $scope.addSuccess = false;
        $scope.addError = false;
        var dataObj = {
    				fullname : $scope.yourname,
    				email : $scope.youremail,
    				mobile : $scope.yourmobile,
    				message : $scope.yourmessage,
            bookname : $APP_NAME,
            bookurl : $APP_WEB_URL
    		};
        var res = BookFactory.sendContactUs(dataObj);
        res.success(function(data, status, headers, config) {
          $scope.addSuccess = true;
		  $scope.yourname = '';
          $scope.youremail = '';
          $scope.yourmobile = '';
          $scope.yourmessage = '';
          $state.go('contactUs');
		  $timeout(function(){
			$scope.addSuccess = false;
		  }, 3000); // maybe '}, 3000, false);' to avoid calling apply
		});
        res.error(function(data, status, headers, config) {
          $scope.addError = true;
          $scope.message = JSON.stringify({data: data});
        });
    }

}).controller('AboutUsController', function($scope, $stateParams, BookFactory, $filter, $rootScope) {

});
