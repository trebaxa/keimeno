function tcScroller(speed,counter,resize){

	var x 			= null;
	//var resize 	= true;
	//var counter = 60;
	//var speed 	= 1;
	
	var scroller_stop 	= 0;
	var content_height 	= 0;
	var orgcounter = counter;
	
	this.scroll = function(){

		if(content_height == 0){
			document.getElementById('tc_scroller').style.display = "block";
			content_height = Number(document.getElementById("tc_scroller").offsetHeight);
		}

		if(resize){
			document.getElementById('tc_scroller_container').style.height = ""+counter+"px";
			document.getElementById('tc_scroller').style.height = ""+counter+"px";
			resize = false;
		}

		clearTimeout(x);
		x = window.setTimeout("tcScroller.scroll()", speed);

		if(scroller_stop == 1) return;
		
		document.getElementById('tc_scroller').style.top = counter + 'px';
		counter--;

		if(counter == (content_height * (-1)) - 20){
			//counter = "+counter+";
			//alert('hallo');
			counter = orgcounter;
		}

	}
	
	this.start = function() {
		x = window.setTimeout("tcScroller.scroll()", speed);
	}
	
	this.set_stop = function(stop_val) {
		scroller_stop = stop_val;
	}
}




