var http = null;

$.extend({
  getUrlVars: function(iurl){
    var vars = {}, hash;
   // var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    var hashes = iurl.substring(iurl.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
      hash = hashes[i].split('=');
      vars[hash[0]] = hash[1];
     
    }
    return vars;
  }
  
});

function URLToArray(url) {
	var request = {};
	var pairs = url.substring(url.indexOf('?') + 1).split('&');
	for (var i = 0; i < pairs.length; i++) {
		var pair = pairs[i].split('=');
		request[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1]);
	}
	return request;
}

function doRequest(id_base,id_target,aktion,php,to_add,kindof,gif) {
	
	var add="";
	if (id_base!=""){
		add = "&setvalue=" +document.getElementById(id_base).value;
	}
	var set_url = php + ".php?cmd="+aktion+"&aktion="+aktion+to_add+add;
	var url_data = $.getUrlVars(set_url);

	if (kindof == "innerhtml") {
		if (gif != "") {
			document.getElementById(id_target).innerHTML = "";
			document.getElementById(id_target).innerHTML = '<img src="'+gif+'" border="0">';
			//	alert(document.getElementById(id_target).innerHTML);
		}

		$.ajax({
			mode: "abort",
			type: "POST",
			dataType: "html",
			data: url_data,
			url: php + ".php",
			cache: false,
			async:true,
			success: function(html){
				$("#"+id_target).html(html);
			}
		});
	}


	if (kindof == "tovalue") {
		var set_url = php + ".php?cmd="+aktion+"&aktion="+aktion+to_add+add;
	  var url_data = $.getUrlVars(set_url);
		$.ajax({
			mode: "abort",
			type: "POST",
			dataType: "html",
			url: php + ".php",
			data: url_data,
			cache: false,
			async:true,
	 		error:function (xhr, ajaxOptions, thrownError){
                    alert(xhr.status);
                    alert(thrownError);
                }  ,
			success: function(html){
				$("#"+id_target).val(html);
						
			}
		});
		setfocus(id_target);
	}
}

function simple_load(id,url_link,gif) {     
  if (gif != "" && gif != null && typeof gif !== 'undefined' && $("#" + id).length > 0) {      
     $("#"+id).html('<img class="axloader" src="'+gif+'" border="0">');
  } else {
    $("#"+id).html('<img class="axloader" src="./images/axloader.gif" border="0">');    
  }	     
$.ajax({
  url: url_link+'&axcall=1',
  cache: true,
  success: function(html){
    if (id!="") $("#" + id).html(html);
     init_autojson_submit();
  }
});
}

function execrequest(url_link) {
$.ajax({
  url: url_link+'&axcall=1',
  cache: true,
  success: function(html){
      //alert(html);
  }
});
}


function simple_load_sync(id,url_link) {
$.ajax({
  url: url_link+'&axcall=1',
  cache: false,
  async		: false,
  success: function(html){
    if (id!="") $("#" + id).html(html);
  }
});
}

function simple_load_nocache(id_target,url_link) {
$.ajax({
  url: url_link+'&axcall=1',
  cache: false,
  success: function(html){
    if($("#" + id_target).length > 0) $("#" + id_target).html(html);
  }
});
}

function simple_get(id_target,php,query,gif) {
  if (gif != "" && gif != null && typeof gif !== 'undefined' && $("#" + id_target).length > 0) {      
     $("#"+id_target).html('<img src="'+gif+'" border="0">');
  }	
  var set_url = php + '?' + query;
  simple_load_nocache(id_target,set_url);
}

function simple_post(id_target,php,query,gif) {
			
  if (gif != "") {      
   // 	document.getElementById(id_target).innerHTML = ;
    	$("#"+id_target).html('<img src="'+gif+'" border="0">');
  }	
	var set_url = php + '?' + query;
	var url_data = $.getUrlVars(set_url);
	//alert(php);
		$.ajax({
			type: "POST",
			dataType: "html",
			data: url_data,
			url: php,
			cache: false,
			async:false,
			success: function(html){
				if (id_target!="") $("#"+id_target+"").html(html);
			}
		});
}

function setLoaderIcon(id_target,etext) {
	document.getElementById(id_target).innerHTML = '<img src="images/opt_loader.gif" border="0"> ' + etext;
}

function checkKeyPress(evt, id_button) {
if (
        ( evt.which && evt.which == 13 )
        ||
        ( evt.keyCode && evt.keyCode == 13 )
        ) {
         document.getElementById(id_button).click();
         return( false );
        }
        else {
         return( true );
        };
}

function submitOnKeyPress(id_button) {
  document.getElementById(id_button).click();
}

function addValueOverID(obj_id,new_value) {
 document.getElementById(obj_id).value = document.getElementById(obj_id).value + new_value;
}

function setValueOverID(obj_id,new_value) {
 document.getElementById(obj_id).value = new_value;
}

function sendRequest2InnerHTML(id_base,id_target,aktion,php,to_add,clear_base,gif) {
	doRequest(id_base,id_target,aktion,php,to_add,'innerhtml',gif);
	if (clear_base==1) document.getElementById(id_base).value = "";
}

function sendRequest2InnerHTMLTimed(id_base,id_target,aktion,php,to_add,clear_base,after_secs,gif) {
	doRequest(id_base,id_target,aktion,php,to_add,'innerhtml',gif);
	if (clear_base==1) document.getElementById(id_base).value = "";
	if (after_secs > 0) cleanInnerHTMLTimed(id_target, after_secs);
}

function GetRequest2InnerHTML(id_target,aktion,php,to_add,gif) {
 doRequest('',id_target,aktion,php,to_add,'innerhtml',gif);
}

function GetRemoteHTML(id_target,htmlurl,gif) {
 doRequest('',id_target,'',htmlurl,'','innerhtml',gif);
}

function GetRequest2Value(id_target,aktion,php,to_add) {
 doRequest('',id_target,aktion,php,to_add,'tovalue',''); 
}

function sendRequest2ValueWithLimit(id_base,id_target,aktion,php,to_add,min_letter) {
 if (document.getElementById(id_base).value.length >= min_letter) doRequest(id_base,id_target,aktion,php,to_add,'tovalue','');  
}

function sendRequest2InnerHTMLWithLimit(id_base,id_target,aktion,php,to_add,min_letter,gif) {
 if (document.getElementById(id_base).value.length >= min_letter) doRequest(id_base,id_target,aktion,php,to_add,'innerhtml',gif);  
}

function cleanInnerHTML(id_tar) {
  document.getElementById(id_tar).innerHTML = "";
}

function cleanInnerHTMLTimed(id_target, after_secs) {
 setTimeout("cleanInnerHTML('"+id_target+"')", after_secs *1000);
}

function setfocus(id_base) {
  document.getElementById(id_base).focus();
}


function doRequestFromValue(svalue,svalue2,svalue3,id_target,aktion,php,to_add,gif) {
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
     	document.getElementById(id_target).innerHTML = "";
    	document.getElementById(id_target).innerHTML = http.responseText;    	
    }
  };
  if (gif != "") {      
    	document.getElementById(id_target).innerHTML = '<img src="'+gif+'" border="0">';
  }
  var myRegExp = /.html/;
  var Ergebnis = php.search(myRegExp);
  if (Ergebnis != -1) {
   http.open('GET', php, true);
   http.setRequestHeader('Content-Type',  'application/x-www-form-urlencoded');
   http.send(null);
  } else {
  http.open('POST', php+'.php?aktion='+aktion+to_add, true);
  http.setRequestHeader('Content-Type',  'application/x-www-form-urlencoded');
  http.send("aktion="+aktion+encodeURI(to_add) + "&setvalue=" +encodeURI(svalue)+ "&setvalue2=" +encodeURI(svalue2)+ "&setvalue3=" +encodeURI(svalue3));
  }
}


