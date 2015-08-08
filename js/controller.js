var labeler = angular.module('lablerApp', []);

labeler.filter('unsafe', function($sce) {
	return function(val) {
		return $sce.trustAsHtml(val);
	};
});


labeler.controller('emrList', function($scope, $http){
	$http.get('getFolderList.php').success(function(data){
		$scope.folders = data;
	})
	//$http.get('getFileList.php').success(function(data){
	//	$scope.files = data;
	//})
	$scope.getFiles = function(folderSel){
		var foSelected = document.getElementById('folder');
		var folder = (folderSel == null)? 'diepdt': folderSel;
		$http.get('getFileList.php?folder='+folder).success(function(data){
			$scope.files = data;
		})
	}
	
	$scope.content = [];
	$scope.mentions = [];
	$scope.relations = [];

	$scope.filename = "";

	$scope.getContent = function(folder, filename){
		$scope.filename = filename;
		$http.get('getFileContent.php?folder='+folder+'&file='+filename).success(function(data){
			$scope.content = data.content;
			$scope.mentions = data.mention;
			$scope.msg = "";
			$scope.relations = data.relation;
			// console.log(data);
		})
	}

	$scope.postMention = function(){
		if($scope.filename != ""){
			var html = [];
			$(".sentence").each(function(){
				html.push($(this).html());
			})
			var request = $http({
				method: "post",
				url: "postMention.php",
				data:{
					file: $scope.filename,
					mentions: $scope.mentions,
					html: html,
					relations: $scope.relations
				},
				headers: { 'Content-Type': 'application/json;' },
			})
			request.success(function(res){
				if(res=="Success"){
					mType = "success";
				} else {
					mType = "error"
				}
				new PNotify({
					title: 'Update',
					text: res,
					type: mType,
					delay: 1000
				});
			})
		}
	}

	$scope.clearAllMention = function(){
		if($scope.filename != ""){
			var request = $http({
				method: "post",
				url: "clearMention.php",
				data:{
					file: $scope.filename,
				},
				headers: { 'Content-Type': 'application/json;' },
			})
			request.success(function(res){
				if(res=="Success"){
					mType = "success";
				} else {
					mType = "error"
				}
				new PNotify({
					title: 'Clear',
					text: res,
					type: mType,
					delay: 1000
				});
				location.reload();
			})
		}
	}
})