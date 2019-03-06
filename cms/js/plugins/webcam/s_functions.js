function livestream_fullscreen(PopupUrl) {
 var a= Math.floor(Math.random()*10000000);
 window.open(PopupUrl, a, "width="+screen.width+",height="+screen.height+",scrollbars=yes,resizable=yes,menubar=no,location=no,toolbar=no"); 
 self.close();
}

function livestream_pop(PopupUrl) {
 var a= Math.floor(Math.random()*10000000);
 window.open(PopupUrl, a, "width=850,height=760,scrollbars=yes,resizable=yes,menubar=no,location=no,toolbar=no"); 
 self.close();
}

function livestream_stdpop(PopupUrl) {
 var a= Math.floor(Math.random()*10000000);
 window.open(PopupUrl, a, "width=850,height=760,scrollbars=yes,resizable=yes,menubar=no,location=no,toolbar=no"); 
}


function uhr() {
  var acttime = new Date();
  window.status = acttime.toString();
  window.setTimeout('uhr()', 1000);
  }

function uhr_text() {
  var acttime = new Date();
  var x= acttime.getHours();
  if  (x <10) {x="0"+x;}
  var y= acttime.getMinutes();
  if  (y <10) {y="0"+y;}
  var z= acttime.getSeconds();
  if  (z <10) {z="0"+z;}
//  document.clock_form.clock_field.value = x+":"+y+":"+z;
  document.clock_form.clock_field.value = acttime.toString();
  document.clock_form.clock_field.disabled=true;
  window.setTimeout('uhr_text()', 1000);
}
  
        
function startStream(imgname,refresh_sek){ 
 var a= Math.floor(Math.random()*10000000);
 if (document.getElementById('stream_picture')) {
  document.stream_picture.src=imgname+a; 
 }
 setTimeout(function(){startStream(imgname,refresh_sek);},(refresh_sek*1000));
} 

function refreshDialog(event_id, stream_id){
 var a= Math.floor(Math.random()*10000000);
 if (document.getElementById('stream_dialog_'+event_id)) { 
 document.frames["stream_dialog_"+event_id].location.href="http://www.astronation.net/streaming/stream_dialog.php?streamid="+stream_id+"&aktion=read&event_id="+event_id+"&rnd="+a;
 //setTimeout("refreshDialog()",(1000*10));   // 10 SEK
 setTimeout(function(){refreshDialog(event_id, stream_id);},(1000 * 10));
}
}

function bodyStart(event_id, stream_id,imgname,refresh_sek) { 
 startStream(imgname,refresh_sek);
 uhr_text();
 refreshDialog(event_id, stream_id);
// btn_start.disabled=true;
}

function bodyStartFullScreen(imgname,refresh_sek) {
 startStream(imgname,refresh_sek);
 uhr_text();
}

function refreshStreamingFrame(event_id, user_id) {
 http = null;
 if (window.XMLHttpRequest) { // Mozilla, Safari,...
         http = new XMLHttpRequest();
         if (http.overrideMimeType) {
            http.overrideMimeType('text/html');
         }
      } else if (window.ActiveXObject) { // IE
         try {
            http = new ActiveXObject("Msxml2.XMLHTTP");
         } catch (e) {
            try {
               http = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {}
         }
      }  
  http.onreadystatechange = function()  {
   if(http.readyState == 4 && http.status == 200){
        var html = http.responseText;
        var blocks = html.split("!#!");
        if (document.getElementById('other_cams')) {
         document.getElementById('other_cams').innerHTML = blocks[1];
        }
        if (document.getElementById('whoiswatching')) {
         document.getElementById('whoiswatching').innerHTML = blocks[0];
        }
        $('#webcam-sender-comments').html(blocks[3]);
        //if (document.getElementById('live_pic')) {
        // document.getElementById('live_pic').innerHTML = blocks[2];
      //}
    }
  };
  
  http.open('POST', 'http://www.astronation.net/streaming/streaming.php', true);
  http.setRequestHeader('Content-Type',  'application/x-www-form-urlencoded');
  http.send("aktion=ax_framerefresh&event_id=" + event_id + "&streamid=" + user_id);
}
