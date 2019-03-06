function tcCountDown(tagid_days,tagid_details,cyear,cmonth,cday,chour,cmin,csec) {
	var speed					= 50;
	var tagid_details	= tagid_details;
	var tagid_days 		= tagid_days;	
	var end_date 			= new Date(cyear,(cmonth-1),cday,chour,cmin,csec);
	
this.countdown=function() {

	var tcf = {
	strformat_sec:function(n) {
 	 s = "";
 	 if (n < 10) s += "0";
 	 return (s + n).toString();
	},

	strformat_milsec:function(n) {
 	 s = "";
 	 if (n < 10) s += "00";
 	 else if (n < 100) s += "0";
 	 return (s + n).toString();
	}
	}
	
  d = new Date();
  count = Math.floor(end_date.getTime() - d.getTime());
  if(count > 0) { 
    miliseconds = tcf.strformat_milsec(count%1000); 
    count = Math.floor(count/1000);
    seconds = tcf.strformat_sec(count%60); 
    count = Math.floor(count/60);
    minutes = tcf.strformat_sec(count%60); 
    count = Math.floor(count/60);
    hours = tcf.strformat_sec(count%24); 
    count = Math.floor(count/24);
    days = count;
    document.getElementById(tagid_days).innerHTML = days;
    document.getElementById(tagid_details).innerHTML = hours + ':' + minutes + ':' + seconds + '.' + miliseconds;
		thisObj = this;
		setTimeout(function(thisObj) { thisObj.countdown(); }, speed, this);
  }
}

};