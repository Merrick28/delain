
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<html>
<head>
<link rel="stylesheet" href="js/jquery/css/ui-lightness/jquery-ui-1.8rc1.custom.css" type="text/css" media="all" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="js/jquery/jquery-ui-1.8rc1.custom.min.js" type="text/javascript"></script>

<style type="text/css">

.container {
	width:100px;
	height:100px;
	background-color:red;
	position:relative;
}

.topleft, .topright, .bottomleft, .bottomright{
	position:absolute;
	width:20px;
	height:20px;
	cursor:pointer;
}

.topleft{
	top:0px;
	left:0px;
	background: transparent url('images/reparer.png') 0px 0px no-repeat;
}
.topright{
	top:0px;
	right:0px;
	background: transparent url('images/equiper.png') 0px 0px no-repeat;
}
.bottomleft{
	bottom:0px;
	left:0px;
	background: transparent url('images/identifier.png') 0px 0px no-repeat;
}
.bottomright{
	bottom:0px;
	right:0px;
	background: transparent url('images/abandonner.png') 0px 0px no-repeat;
}

	</style>
	<script type="text/javascript">
	$(function() {
		$(".topleft").click(function(){ alert('Réparer');});
		$(".topright").click(function(){ alert('Equiper');});
		$(".bottomleft").click(function(){ alert('Identifier');});
		$(".bottomright").click(function(){ alert('Abandonner');});
	});
	</script>


</head>
<body>

<div class="container">
	<div class="topleft"></div>
	<div class="topright"></div>
	<div class="bottomleft"></div>
	<div class="bottomright"></div>
	<img src="images/demiplate.png" />
</div>


</body>
</html>

