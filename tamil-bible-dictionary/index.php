<?php
    include 'counter.php';
?>
<!DOCTYPE html>
<html>
<head lang="en">
	<meta charset="utf-8" />
	<meta name="format-detection" content="telephone=no" />
	<meta name="msapplication-tap-highlight" content="no" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Tamil Bible Dictionary</title>
  <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"/>
  <link rel="stylesheet" type="text/css" href="css/textAngular.css"/>
  <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css"/>
	<link rel="stylesheet" type="text/css" href="css/app.css"/>
	<style type="text/css">
	/*body { background: #40bce7 !important; }*/
	</style>

</head>
<body class="quizzler">
				<!-- second menu bar -->
				<nav class="navbar navbar-default navbar-static">
					<div class="navbar-header">
							<button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#b-menu-2">
									<span class="sr-only">Toggle navigation</span>
									<span class="icon-bar"></span>
									<span class="icon-bar"></span>
									<span class="icon-bar"></span>
							</button>
							<a class="navbar-brand" href="http://wordofgod.in/bibledictionary/tamil-bible-dictionary">Tamil Bible Dictionary</a>
					</div>

					<!-- submenu elements for #b-menu-2 -->
					<div class="collapse navbar-collapse" id="b-menu-2">
						<ul class="nav navbar-nav">
							<li class="active"><a ui-sref="index"><span class="glyphicon glyphicon-home"></span> Home</a></li>
							<li><a href="https://wordofgod.in/bibledictionary/" target="_blank"><span class="glyphicon glyphicon-book"> </span> Other Dictionaries</a> </li>
							<li><a ui-sref="aboutUs"><span class="glyphicon glyphicon-certificate"></span> About Us</a></li>
							<li><a ui-sref="contactUs"><span class="glyphicon glyphicon-envelope"></span> Contact Us</a></li>
							<li><a href="sitemap.xml" target="_blank"><span class="glyphicon glyphicon-link"> </span> Sitemap</a> </li>
						</ul>
					</div><!-- /.nav-collapse-->
				</nav>

	    <!-- main container -->
	    <div class="container">

	      <!-- 2-column layout -->
	      <div class="row row-offcanvas row-offcanvas-right">
	        <div class="col-xs-12 col-sm-12">
							<div ui-view></div>
					</div>
	      </div> <!-- /column 2 -->

	    </div><!--/.container-->
<p>&nbsp;</p>
<p>&nbsp;</p>
<footer>
	<nav class="navbar navbar-default navbar-fixed-bottom" role="navigation">
		<p class="navbar-text">&nbsp; &nbsp; &copy Copyright since 2016 <a href="http://www.wordofgod.in/">Word of God Ministries</a> 
		| <a href="sitemap.php" target="_blank">Sitemap</a> 
		| Visitors: <?= $visitors2 ?></p>
	</nav>
</footer>

	<script type="text/javascript" src="js/config.js"></script>
	<script type="text/javascript" src="js/angular.min.js"></script>
	<script type="text/javascript" src="js/app.js"></script>
	<script type="text/javascript" src="js/controllers.js"></script>
	<script type="text/javascript" src="js/services.js"></script>
	<script type="text/javascript" src="js/angular-ui-router.min.js"></script>
	<script type="text/javascript" src="js/angular-resource.min.js"></script>
	<script type="text/javascript" src="js/jquery-1.12.2.min.js"></script>
	<script type="text/javascript" src="js/textAngular-rangy.min.js"></script>
	<script type="text/javascript" src="js/textAngular-sanitize.min.js"></script>
	<script type="text/javascript" src="js/textAngular.min.js"></script>
	<script type="text/javascript" src="js/spin.min.js"></script>
	<script type="text/javascript" src="js/angular-spinner.min.js"></script>
	<script type="text/javascript" src="js/index.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/underscore-min.js"></script>
	<script type="text/javascript" src="js/supplant.js"></script>
</body>
</html>
