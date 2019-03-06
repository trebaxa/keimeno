<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>jQuery Horizontal Scrollbars</title>
		
		<link rel="stylesheet" type="text/css" href="jquery.horizontal.scroll.css" />
		<link rel="stylesheet" type="text/css" href="demo_style.css" />
		<link href='http://fonts.googleapis.com/css?family=Dancing+Script:regular,bold&v1' rel='stylesheet' type='text/css'>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
		<script src="jquery.horizontal.scroll.js" type="text/javascript"></script>
		
		<script type="text/javascript">
		
			$(document).ready(function(){
				$('#horiz_container_outer').horizontalScroll();
			
			});
		
		</script>
		
		
				
	</head>
	<body>
	
	<h1>jQuery Horizontal Scrollbars</h1>
	<div id="createdby">Created by: <a href="#">Tony Lea</a></div>
					
	<ul id="horiz_container_outer">
		<li id="horiz_container_inner">
			<ul id="horiz_container">
				<li><img src="images/toystory.png" width="500px" height="300px" alt="Toy Story" /></li>
				<li><img src="images/up.png" width="500px" height="300px" alt="UP" /></li>
				<li><img src="images/ratatouille.png" width="500px" height="300px" alt="Ratatouille" /></li>
				<li><img src="images/findingnemo.png" width="500px" height="300px" alt="Finding Nemo" /></li>
				<li><img src="images/cars.png" width="500px" height="300px" alt="Cars" /></li>
				<li><img src="images/monstersinc.png" width="500px" height="300px" alt="Monsters Inc" /></li>
				<li><img src="images/bugslife.png" width="500px" height="300px" alt="A Bugs Life" /></li>
				<li><img src="images/incredibles.png" width="500px" height="300px" alt="The Incredibles" /></li>
			</ul>
		</li>		
	</ul>			
	
						
	<div id="scrollbar">
		<a id="left_scroll" class="mouseover_left" href="#"></a>
		<div id="track">
		     <div id="dragBar"></div>
		</div>
		<a id="right_scroll" class="mouseover_right" href="#"></a></div>
	</div>
	
	<div id="buttons">
		<div class="button">Download It!</div>
		<div class="button">How To Use It?</div>
	</div>
					
</body>

</html>