<!DOCTYPE html>
<html>
<head >
	<title></title>
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.1/angular.min.js"></script>
<style>
.loader {
  border: 6px solid #f3f3f3;
  border-radius: 50%;
  border-top: 6px solid #3498db;
  width: 50px;
  height: 50px;
  -webkit-animation: spin 2s linear infinite;
  animation: spin 2s linear infinite;
  position :absolute;
  top:50%;
  left :50%;
  margin:-25px 0 0 -25px;
}

@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
</head>
<body>

<div class="loader"></div>


<script type="text/javascript">


   setTimeout(function(){ 
   		window.opener.$scope.postsays = 'teapot'; 
   		window.value = true; 
   	}, 2000);

	
  
</script>

</body>
</html>




