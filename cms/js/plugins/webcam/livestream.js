function gE(d) { return document.getElementById(d); }

var live_obj = {
 
 stream_server          : 'http://www.astronation.net/streaming/stream/',
 pic_server             : 'http://www.astronation.net/js/plugins/webcam/',
 pic_offline            : 'cam_offline.jpg',
 stream_uid             : 0,
 stream_id              : 0,
 stream_pic             : "",
 bild                   : null,
 resample               : 100,
 old_width              : 0, 
 format                 : 'hor',
 banner                 : 'ON',

// INTERN
 timer_lag              : 1,
  
 init: function() {
  live_obj.bild = new Image();
  var html_obj = gE("astronation_live");
  
  var tab = document.createElement("table");
  var tb = document.createElement("tbody");
  var tb1 = document.createElement("tbody");
  var tr_lp = document.createElement("tr");
  var td_lp = document.createElement("td");
  var tr_pow = document.createElement("tr");
  var td_pow = document.createElement("td");
        
        // live pic
  tr_lp.appendChild(td_lp);
  tb1.appendChild(tr_lp);
  tab.appendChild(tb1);
  // powered by
  tr_pow.appendChild(td_pow);
  tb.appendChild(tr_pow);
  tab.appendChild(tb);  

        tab.style.border = "0px solid #000000";
        td_pow.style.textAlign = "center";
        td_lp.style.textAlign = "center";
        //td_lp.innerHTML='TEST';
          
  var live_img = document.createElement("img");
  live_img.id = "id_live_img";         
  td_lp.appendChild(live_img);  
  
  an_link = document.createElement("a");
  an_link.id = 'id_an_link';
  an_link.setAttribute("href", "http://www.astronation.net");
  an_link.setAttribute("target", "_blank");
  an_link.setAttribute("title", "Astronomic GO LIVE transmission");
  
 // ec_link = document.createElement("a");
 // ec_link.id = 'id_ec_link';
 // ec_link.setAttribute("href", "http://www.eclipse-city.com");
 // ec_link.setAttribute("target", "_blank");
 // ec_link.setAttribute("title", "Eclipse-City");
  
  // er_link = document.createElement("a");
  // er_link.id = 'id_ec_link';
  // er_link.setAttribute("href", "http://www.eclipse-reisen.de");
  // er_link.setAttribute("target", "_blank");
  // er_link.setAttribute("title", "Jetzt SoFi in China buchen");
  
  
  var an_img = document.createElement("img");
  an_img.id = "id_an_img";
  an_img.src=live_obj.pic_server + 'an_produced.jpg';
  an_img.style.border="0px";
  an_link.appendChild(an_img);
  
  // var ec_img = document.createElement("img");
  // ec_img.id = "id_ec_img";
  // ec_img.src=live_obj.pic_server + 'ec_powered.jpg';
  // ec_img.style.border="0px";
  // ec_link.appendChild(ec_img);
  
 //  var er_img = document.createElement("img");
  // er_img.id = "id_er_img";
  // er_img.src=live_obj.pic_server + 'er_powered.jpg';
  // er_img.style.border="0px";
  // er_link.appendChild(er_img);
  
  if (live_obj.banner=='ON') {
         var br1 = document.createElement("br");
         var br2 = document.createElement("br");
   td_pow.appendChild(an_link);
   if (live_obj.format=='ver') td_pow.appendChild(br1);
  // td_pow.appendChild(ec_link);
   if (live_obj.format=='ver') td_pow.appendChild(br2);
   // td_pow.appendChild(er_link);
        }  
 
  //margin-left:-100px;margin-right:-105px;margin-bottom:-52px;margin-top:-12px;
  
  html_obj.style.marginLeft="0px";
  html_obj.style.marginRight="0px";
  html_obj.style.marginBottom="0px";
  html_obj.style.marginTop="0px";
  html_obj.appendChild(tab);

  var frame_found = (top.location.href+'').indexOf('astronation.net', 0);

 // alert(frame_found);
  if (frame_found<0) {
  	 top.location.href="http://www.astronation.net";
  }
 // OLD IFRAME PROTECTION
  //if(parent != null && parent != self) {
   //     top.location.href="http://www.astronation.net"
 // }
 
 // gE("id_live_img").src=live_obj.pic_server + live_obj.pic_offline;
//  if (live_obj.resample<100) {
 //   gE("id_live_img").width = gE("id_live_img").width * (live_obj.resample  / 100);
  //  gE("id_live_img").Height = gE("id_live_img").Height * (live_obj.resample  / 100);
//  }  
  
  //html_obj.appendChild(live_img);
 },
 
 
 resizeImg: function()    {
         
  var _resizeWidth  = Math.round(live_obj.bild.width * (live_obj.resample / 100));
  var _resizeHeight = Math.round(live_obj.bild.height * (live_obj.resample / 100));
  gE("id_live_img").style.width = _resizeWidth + 'px'; 
  gE("id_live_img").style.height = _resizeHeight + 'px';   
 },
 
 loadingFinished : function() {
   gE("id_live_img").src = live_obj.bild.src;
   if (live_obj.resample < 100) {
    live_obj.resizeImg();
   }
   live_obj.timer_lag = 1;
 },
 
 loadingError : function() {
        if (gE("id_live_img").src!=live_obj.pic_server + live_obj.pic_offline) {
                gE("id_live_img").src = live_obj.pic_server + live_obj.pic_offline;           
        }
        live_obj.timer_lag = 1;
 },
 
 streaming: function() { 
  var a = Math.floor(Math.random()*10000000);
  live_obj.bild = new Image();
  live_obj.bild.onload = function(){live_obj.loadingFinished();}
  live_obj.bild.onerror = function(){live_obj.loadingError();}
  live_obj.bild.src = live_obj.stream_pic + a;    
  setTimeout(function(){live_obj.streaming();},(live_obj.refresh_sek * (1000 * live_obj.timer_lag)));
 } ,
 
 start: function(ovh_stream_uid, ovh_stream_id,ovh_refresh_sek,streamserver) { 
  if (streamserver!="") live_obj.stream_server=streamserver
  live_obj.stream_uid   = ovh_stream_uid;
  live_obj.stream_id            = ovh_stream_id;
  live_obj.refresh_sek  = ovh_refresh_sek;
  if (live_obj.stream_pic=="") {
        live_obj.stream_pic     = live_obj.stream_server + live_obj.stream_uid + '_' + live_obj.stream_id + '_stream.jpg?a=';
  }
  live_obj.init();  
  live_obj.streaming();
 } 

};
