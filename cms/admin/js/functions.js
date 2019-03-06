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
        $( "#"+id_target ).focus();		
	}
}

function set_ajax_links() {   
        // ajax links
       $('.ajax-link').unbind('click');
       $('.ajax-link').css('cursor','pointer');
       $('.ajax-link').click(function(event) {
            event.preventDefault();
            var target_container = 'admincontent';
            if ($(this).data('target')!="") {
                target_container = $(this).data('target');                                
            }
            if (target_container==undefined) target_container = 'admincontent';                 
            $('.tooltip').tooltip('destroy');
            simple_load(target_container, $(this).attr('href'),true);
        });
}

function simple_load(id,url_link,spinner) {     
  if (spinner==true || (spinner != "" && spinner != null && typeof spinner !== 'undefined') && $("#" + id).length > 0) {      
     $("#"+id).html('<i class="fa fa-spinner fa-spin"></i>');
  }	 

    $.ajax({
      url: url_link+'&axcall=1',
      cache: true,
      success: function(html){
        if (id!="") $("#" + id).html(html);
        /*set_ajaxdel_icon();
        init_autojson_submit();
        */
        fwstart();
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
	$('#'+id_target).html('<img src="images/opt_loader.gif" border="0"> ' + etext);
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


function sendRequest2InnerHTML(id_base,id_target,aktion,php,to_add,clear_base,gif) {
	doRequest(id_base,id_target,aktion,php,to_add,'innerhtml',gif);
	if (clear_base==1) document.getElementById(id_base).value = "";
}


function GetRequest2InnerHTML(id_target,aktion,php,to_add,gif) {
 doRequest('',id_target,aktion,php,to_add,'innerhtml',gif);
}


function GetRequest2Value(id_target,aktion,php,to_add) {
 doRequest('',id_target,aktion,php,to_add,'tovalue',''); 
}


function sendRequest2InnerHTMLWithLimit(id_base,id_target,aktion,php,to_add,min_letter,gif) {
 if (document.getElementById(id_base).value.length >= min_letter) doRequest(id_base,id_target,aktion,php,to_add,'innerhtml',gif);  
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


function showPageLoadInfo() {
        var conid = 'statusinfo';
        $('#' + conid).css("position", "absolute");
        $('#' + conid).css("z-index", "100000");
        $('#' + conid).css("top", (($(window).height() - $('#' + conid).outerHeight()) / 2) + $(window).scrollTop() + "px");
        $('#' + conid).css("left", (($(window).width() - $('#' + conid).outerWidth()) / 2) + $(window).scrollLeft() + "px");
        $('#' + conid).css("height", 'auto');
        $('#' + conid).fadeIn();
}

function markAllRows(FormName, container_id, setchk) {
        var checkbox;
        var el = document.getElementById(FormName);
        for (var i = 0; i < el.elements.length; i++) {
                checkbox = el.elements[i];
                if (checkbox && checkbox.type == 'checkbox' && checkbox.id == container_id) {
                        checkbox.checked = setchk;
                }
        }
}




var bName = navigator.appName;

function taLimit(taObj, maxL, e) {
        whKey = e ? e.which : event.keyCode; // check for which is supported
        // alert (whKey);
        if (whKey == 0 || whKey == 8) return true; // ENTF und BACKSP Taste erlauben
        if (taObj.value.length == maxL) return false;
        return true;
}

function taCount(taObj, Cnt, maxL) {
        objCnt = createObject(Cnt);
        objVal = taObj.value;
        if (objVal.length > maxL) objVal = objVal.substring(0, maxL);
        if (objCnt) {
                if (bName == "Netscape") {
                        objCnt.textContent = maxL - objVal.length;
                } else {
                        objCnt.innerText = maxL - objVal.length;
                }
        }
        return true;
}

function createObject(objId) {
        if (document.getElementById) return document.getElementById(objId);
        else if (document.layers) return eval("document." + objId);
        else if (document.all) return eval("document.all." + objId);
        else
        return eval("document." + objId);
}

function rollover() {
        //http://icant.co.uk/articles/flexible-css-menu/step3.html
        if (!document.getElementById || !document.createTextNode) {
                return;
        }
        var n = document.getElementById('tabbed_nav');
        if (!n) {
                return;
        }
        var lis = n.getElementsByTagName('li');
        for (var i = 0; i < lis.length; i++) {
                lis[i].onmouseover = function() {
                        this.className = this.className ? 'cur' : 'over';
                }
                lis[i].onmouseout = function() {
                        this.className = this.className == 'cur' ? 'cur' : '';
                }
        }
}
window.onload = rollover;

function hidePageLoadInfo() {
        $('#statusinfo').fadeOut();
}

function moveup(it_id) {
        var it = document.getElementById('it' + it_id);
        it.value = parseInt(it.value) - 11;
        return true;
}

function movedown(it_id) {
        var it = document.getElementById('it' + it_id);
        it.value = parseInt(it.value) + 11;
        return true;
}

function pausecomp(millis) {
        var date = new Date();
        var curDate = null;
        do {
                curDate = new Date();
        }
        while (curDate - date < millis);
}


function mark_checkboxes(className, status) {
        $("." + className).attr('checked', status);
}

function set_mark_all_checkboxes() {
    $('.js-selecctallbox').unbind('click');    
    $('.js-selecctallbox').click(function(event) {  //on click
        var class_indent = $(this).data('class');
        if(this.checked) { // check select status
            $('.'+class_indent).each(function() { //loop through each checkbox
                this.checked = true;  //select all checkboxes with class "checkbox1"              
            });
        }else{
            $('.'+class_indent).each(function() { //loop through each checkbox
                this.checked = false; //deselect all checkboxes with class "checkbox1"                      
            });        
        }
    });    
}

function toggle_off() {
try {
    if (typeof tinymce !== "undefined") {
        tinymce.triggerSave();        
     }   
   } catch (e) {
      console.log(e.message);   
   }        
}

function showRequest(formData, jqForm, options) {
        showPageLoadInfo();
        var queryString = $.param(formData);
        //  alert('About to submit: \n\n' + queryString); 
        return true;
}

function showResponse(responseText, statusText, xhr, $form) {
        show_saved_msg();
        $("input:file").val('');
}

function isUndefined(what) {
        return (typeof what == 'undefined');
}

function show_saved_msg(duration) {
        $('#savedresult').removeClass('faultboxajax').addClass('bg-success');        
        if (duration == "" || duration == 0 || duration == null) duration = 1000;
        $("#statusinfo").hide();
        $('#savedresult').css("position", "absolute");
        $('#savedresult').css("top", (100 + $(window).scrollTop()) + 'px');
        $('#savedresult').css("z-index", 100000);
        $('#savedresult').css("height", 'auto');
        $('#savedresult').css("width", 300);
        $('#savedresult').css("left", (($(window).width() - $('#savedresult').outerWidth()) / 2) + $(window).scrollLeft() + "px");
        $('#savedresult').fadeIn();
        setTimeout('$("#savedresult").fadeOut();', duration);
        $("#feedbackmsg").hide();
}

function msg(msg,duration) {
        if (duration == "" || duration == 0 || duration == null) duration = 3000;
        $('#simplemsg').remove();
        $('body').prepend('<div id="simplemsg" class="okbox rounded bg-success text-center">'+msg+'</div>');        
        $('#simplemsg').css("top", (($(window).height()/2) + $(window).scrollTop()) + 'px');        
        $('#simplemsg').css("left", (($(window).width() - $('#simplemsg').outerWidth()) / 2) + $(window).scrollLeft() + "px");
        $('#simplemsg').fadeIn();
        setTimeout('$("#simplemsg").fadeOut();', duration);
}

function tab_visi(tabcon,id) {
   $('#'+tabcon).next('.tabs').find('.tabvisi').hide();
   $('#tab' +id).fadeIn(); 
}

function tab_visi_by_ident(tabcon,ident) {   
  if ( $('#'+ident).is(':hidden')) { 
    $('#'+tabcon).next('.tabs').find('.tabvisi').hide();
    $('#'+ident).fadeIn(); 
   }
}

function show_black_bg() {
        $('.global_black').remove();
        $('#adminheader').css('z-index', '-2');
        $('body').prepend('<div class="global_black"></div>');
        $('.global_black').css('height', ($(document).height() + 1000) + 'px');
        $('.global_black').css("z-index", 999);
        $('.global_black').show();
}

function hide_black_bg() {
        $('.global_black').remove();
}

function dc_show(conid, fwidth) {
        show_black_bg();
        if (fwidth != "") {
                $('#' + conid).width(fwidth);
        }
        $('#' + conid).css("position", "absolute");
        $('#' + conid).css("z-index", 1000);
        $('#' + conid).css("top", (($(window).height() - $('#' + conid).outerHeight()) / 2) + $(window).scrollTop() + "px");
        $('#' + conid).css("left", (($(window).width() - $('#' + conid).outerWidth()) / 2) + $(window).scrollLeft() + "px");
        $('#' + conid).prepend('<div style="float:right;"><a href="javascript:void(0)" title="close" onClick="dc_closeLink(this);"><img id="closeicon-divframe" src="./images/close.png" border="0"></a></div>');
        if ($('#' + conid).outerHeight() > $(window).height()) {
                $('#' + conid).css("height", $(window).height() - 300);
                $('#' + conid).css("top", (($(window).height() - $('#' + conid).outerHeight()) / 2) + $(window).scrollTop() + "px");
                $('#' + conid).css("overflow", 'auto');
        } else {
                $('#' + conid).css("height", 'auto');
        }
        $('#' + conid).fadeIn();
        $('.global_black').click(function() {
                dc_close(conid);
        });
}

function dc_close(conid) {
        $('#' + conid).fadeOut();
        $('.global_black').remove();
        $('.formErrorContent').remove();
        $('#adminheader').css('z-index', '1');
}

function dc_closeLink(obj) {
        var conid = obj.parentNode.parentNode.id;
        $('#' + conid).fadeOut();
        $('.global_black').remove();
        $('.formErrorContent').remove();
        $('#adminheader').css('z-index', '1');
        $('.maincontent').fadeIn(200);
}

function dc_init() {
        $('.divframe').hide();
}

function close_show_box() {
        $('#show_box').fadeOut();
        $('#ah_content_table').show();
        $('.global_black').remove();
        $('#adminheader').css('z-index', '5');
        $('#show_box').remove();
        $('.maincontent').fadeIn(200);
}

function print_show_box() {
        $("#show_box_iframe").get(0).contentWindow.print();
}

function add_show_box(srclink, width, height, noprinter) {
        $('.global_black').remove();
        $('#show_box').remove();
        var docheight = $(document).height() + 1000;
        $('#ah_content_table').hide();
        $('#adminheader').css('z-index', '-2');
        $('body').prepend('<div class="global_black"></div><div id="show_box" style="display:none"><a href="javascript:void(0);" style="align:right" title="print" onclick="print_show_box()"><img id="showboxprintericon" src="./images/icon_printer.png" /> </a><a href="#" style="float:right;"  onclick="close_show_box()"><img src="./images/close.png" id="closeicon" border="0"></a><iframe style="width:100%;border:0px;height:96%;" id="show_box_iframe"></iframe></div>');
        $('.global_black').css('z-index', '-1');
        $('.global_black').css('height', docheight + 'px');
        $('#show_box').css('width', width);
        $('#show_box').css('height', height);
        $('#show_box_iframe').attr('src', srclink);
        $('#show_box').css('opacity', 1);
        $('.global_black').show();
        $('.maincontent').hide();
        $('#show_box').fadeIn();
        $('.global_black').click(function() {
                close_show_box();
        });
        if (noprinter == 1) $('#showboxprintericon').hide();
        return false;
}

function add_show_box_tpl(srclink, title) {
        $('#modal_frame').remove();
        $('body').prepend('<div class="modal fade" id="modal_frame" tabindex="-1" role="dialog" aria-labelledby="modal_frameLabel" aria-hidden="true"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><h4 class="modal-title" id="modal_frameLabel">'+title+'</h4></div><div class="modal-body" id="showboxcontent"></div></div></div></div>');
        $('#modal_frame').modal('show');
        simple_load("showboxcontent", srclink);
        return false;
}

function ace_fullscreen(id) {
     var editor = ace.edit(id);
     editor.execCommand('Toggle Fullscreen');        
}



function set_script_editor() {
       try {        
        $(".script-editor").remove();  
        ace.require("ace/ext/emmet");
        var dom = ace.require("ace/lib/dom");
        var commands = ace.require("ace/commands/default_commands").commands;
        // add command for all new editors
        commands.push({
        	name: "Toggle Fullscreen",
        	bindKey: "F11",
        	exec: function(editor) {
        		dom.toggleCssClass(document.body, "fullScreen")
        		dom.toggleCssClass(editor.container, "fullScreen-editor")
        		editor.resize()
        	}
        })
        $(".se-html").each(function() {
                var id = Math.floor((Math.random() * 1000000) + 1);
                var textarea = $(this);
                if ($(this).attr('id')!="" && $(this).attr('id')!=undefined) id =$(this).attr('id');
                var mode = 'html';
                if (textarea.attr('class').indexOf("css") >= 0) {
                        mode = 'css';
                }
                if (textarea.attr('class').indexOf("js") >= 0) {
                        mode = 'javascript';
                }
                var readonly = false;
                if (textarea.attr('class').indexOf("se-readonly") >= 0) {
                        readonly = true;
                }
                var theme = "chrome";
                if (textarea.data('theme')!="") {
                        theme =textarea.data('theme'); 
                }                
                textarea.hide();
                textarea.after('<div class="script-editor" id="editor-' + id + '"></div>');
                
                var editor = ace.edit('editor-' + id);
                $('#editor-' + id + ' .ace_scroller').prepend('<div class="ace-fullscreen-btn"><a class="btn btn-default" href="javascript:void(0)" onclick="ace_fullscreen(\'editor-'+id+'\')"><i class="fa fa-arrows-alt"></i></a></div>');
                editor.setReadOnly(readonly);
                editor.setTheme("ace/theme/"+theme);
                editor.getSession().setMode("ace/mode/" + mode);
                editor.setOption("enableEmmet", true);                                                
                editor.getSession().setTabSize(2);
                editor.getSession().setUseWorker(false);
                editor.getSession().setValue(textarea.val());
                
                var heightUpdateFunction = function() {
                        var lineheight = (editor.renderer.lineHeight == 1) ? 14 : editor.renderer.lineHeight;
                        var newHeight =
                        editor.getSession().getScreenLength() * lineheight + editor.renderer.scrollBar.getWidth();
                        newHeight = (newHeight < 100) ? 300 : newHeight;
                        newHeight = (newHeight > 600) ? 600 : newHeight;
                        $('#editor-' + id).height(newHeight.toString() + "px");
                        $('#editor-' + id + '-section').height(newHeight.toString() + "px");
                        editor.resize();
                };
                heightUpdateFunction();
                
                
                
                editor.getSession().on("change", function() {
                        textarea.val(editor.getSession().getValue());
                        heightUpdateFunction();
                });
               
                editor.commands.addCommand({
                        name: "save",
                        bindKey: {
                                win: "Ctrl-S",
                                mac: "Command-s"
                        },
                        exec: function() {
                                var jsVar = editor.session.getValue();
                                textarea.closest("form").submit();
                        }
                        
                });
                
        });
        $(window).keypress(function(event) {
                if ((event.which == 115 && event.ctrlKey)) {
                        event.preventDefault();
                }
        });
        } catch (e) {
       //  console.log(e.message);   
        }
}

function remove_all_tinymce() {
 try {
    if (typeof tinymce !== "undefined") {
        tinymce.triggerSave();
        for (var i = 0; i < tinymce.editors.length; i++) {
                tinyMCE.execCommand('mceRemoveControl', false, tinymce.editors[i].id);
        }
        for (var i=tinymce.editors.length; i>0; i--) {
            tinyMCE.editors[i-1].remove();
        }
     }   
   } catch (e) {
      // console.log(e.message);   
   }     
}

function before_json_submit(formData, jqForm, options) {
        toggle_off();
        showPageLoadInfo();
        var queryString = $.param(formData);
        return true;
}

function show_msge(msg,duration) {
    $('#savedresult').removeClass('bg-success').addClass('faultboxajax');
    $('#savedresult').html(msg);                
    show_saved_msg(duration); 
}

function show_msg(msg,duration) {
    $('#savedresult').removeClass('faultboxajax').addClass('bg-success');
    $('#savedresult').html(msg);                
    show_saved_msg(duration); 
}

function show_json_answer(responseText, statusText, xhr, $form) {
        var obj = jQuery.parseJSON(responseText);
        if (obj.msge != "") {
                show_msge(obj.msge,3000);                
        } else {
                show_msg(obj.msg,3000);                
                if (obj.jsfunction != "") {
                        eval(obj.jsfunction + '(' + obj.jsparams + ')');
                }
                $('input[type=file]').val('');
        }
}

function init_autojson_submit() {
        $('.jsonform').unbind('submit');
        var options = {
                type: 'POST',
                forceSync: true,
                beforeSubmit: before_json_submit,
                success: show_json_answer
        };
        $('.jsonform').submit(function() {
                $(this).ajaxSubmit(options);
                return false;
        });
}

function jsonexec(url, showok) {
        if (showok!=false) showok=true;
        $.getJSON(url, function(data) {
                if (data.msge != "") {
                        show_msge(data.msge,3000);                        
                        
                } else {
                        if (showok == true) {
                                show_msg(data.msg,3000);                                
                                if (data.jsfunction != "") {
                                        eval(data.jsfunction + '()');
                                        set_ajaxdel_icon();
                                        init_autojson_submit();
                                }
                                $('input[type=file]').val('');
                        }
                }
        });
}

function set_ajaxdel_icon() {
        $('.deljson').unbind('click');
        $(".deljson").click(function() {                
                var r = true;
                if ($(this).data('confirm') == '1') {
                        var r = confirm($(this).data('ctext'));
                }
                if (r == true) {
                        var url = $(this).data('phpfile') + '?epage=' + $(this).data('epage') + '&cmd=' + $(this).data('cmd') + '&ident=' + $(this).data('ident') + '&' + $(this).data('toadd');
                        var icon = $(this);
                        $.getJSON(url, function(data) {
                                if (data.msge != "") {                                        
                                        show_msge(data.msge);
                                } else {
                                        icon.closest('tr').fadeTo(100, 0, function() {
                                                $(this).remove();
                                        });
                                    if (data.jsfunction != "") {
                                        eval(data.jsfunction + '()');                                        
                                    }
                                }
                        });
                }
                return false;
        });
}

function set_ajaxdelete_icons(confirmtxt, epage) {
        set_ajaxdel_icon();
        $('.delete').unbind('click');
        $(".delete").click(function() {
                var r = true;
                var rel = $(this).attr('rel');
                var parts = rel.split("|");
                if (parts[1] == 'confirm') {
                        var r = confirm(confirmtxt);
                }
                var cmd = "axdelete_item";
                if (parts[0] != "") {
                        cmd = parts[0];
                }
                var phpfile = parts[2];
                var toadd = parts[3];
                if (r == true) {
                        simple_load('axdelresult', phpfile + '?epage=' + epage + '&cmd=' + cmd + '&id=' + $(this).attr('id') + '&' + toadd);
                        $(this).closest('tr').fadeTo(100, 0, function() {
                                $(this).remove();
                        });
                }
                return false;
        });
}

function set_ajaxapprove_icons() {
        $('.axapprove').unbind('click');
        $(".axapprove").click(function() {
                var cmd = $(this).data('cmd');
                if ($(this).data('cmd') == "") cmd = 'axapprove_item';
                var url = $(this).data('phpself') + '?epage=' + $(this).data('epage') + '&cmd=' + cmd + '&id=tmp-' + $(this).data('ident') +'&ident=' + $(this).data('ident') + '&value=' + $(this).data('value') + '&' + $(this).data('toadd');
                execrequest(url);
                if ($(this).data('value') == 1) {
                       // $(this).attr("src", './images/page_visible.png');
                        $(this).find('i').addClass('fa-green').removeClass('fa-red');
                        $(this).data('value', '0');
                } else {
                        $(this).find('i').removeClass('fa-green').addClass('fa-red');
                        $(this).data('value', '1');
                }
                return false;
        });
}

function scrollToAnchor(aid) {
        $('html,body').animate({
                scrollTop: $('#' + aid).offset().top
        }, 'slow');
}

// FLOT CHART FUNCTIONS


var chartError = function(req, status, err) {
        console.log('An error occurred: ', status, err);
};

function labelFormatter(label, series) {
		return "<div class='flot-pie-label'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
}    
    
function load_flot_pie(url, id, width, height, show_legend, show_labels) {
 $("#"+id).css('width',width);
 $("#"+id).css('height',height);
  $.ajax({
        type:'GET',
        dataType:"json",
        url:url,
        method: 'GET',
        dataType: 'json',
        success: function(data) {            
            $.plot($("#"+id),data,{
                series: {
                    pie: {
                        show: true,
                        radius: 1,
                        label: {
                            show: show_labels,
                            radius: 2/3,
                            formatter: labelFormatter,
                            background: {
                                opacity: 0.8
                            },
                            threshold: 0.1
                        }
                    }
                },
                legend: {
                    show: show_legend
                },
                grid: {
                        hoverable: true,
                        clickable: false
                }
              });
              },
        error: chartError
}); 
}

function load_flot_chart(url, id, width, height, tooltip, unit) {
        $.getJSON(url, function(series) {
                $('#' + id + '-cont').height(height);
                $('#' + id + '-cont').width(width);
                $.plot("#" + id, series.serielist, series.foptions);
                if (tooltip == 1) {
                        flot_activate_tooltip(id, unit);
                }
        });
}

function flot_show_tooltip(x, y, contents) {
        $("<div id='tooltip'>" + contents + "</div>").css({
                position: "absolute",
                display: "none",
                top: y + 5,
                left: x + 5,
                border: "1px solid #fdd",
                padding: "2px",
                "background-color": "#fee",
                opacity: 0.80
        }).appendTo("body").fadeIn(200);
}

function flot_activate_tooltip(id, unit) {
        var previousPoint = null;
        $("#" + id).bind("plothover", function(event, pos, item) {
                if (item) {
                        if (previousPoint != item.dataIndex) {
                                previousPoint = item.dataIndex;
                                $("#tooltip").remove();
                                var x = item.datapoint[0].toFixed(2),
                                    y = item.datapoint[1].toFixed(2);
                                // flot_show_tooltip(item.pageX, item.pageY, item.series.label + " of " + x + " = " + y);
                                flot_show_tooltip(item.pageX, item.pageY, item.series.label + " " + y + unit);
                        }
                } else {
                        $("#tooltip").remove();
                        previousPoint = null;
                }
        });
}

/* FORM DATA via JSON */
function load_json_form(url, formid) {
    $('#'+formid).trigger("reset");
    $.getJSON( url, function( data ) {
        $.each( data, function( key, val ) {            
            if ($('#'+formid+' .'+key).length>0) {                
                $('#'+formid+' .'+key).val(val);
                if ($('#'+formid+' .'+key).hasClass('se-html')) {
                     var editor = ace.edit($('#'+formid+' .'+key).next().attr('id'));
                     editor.getSession().setValue($('#'+formid+' .'+key).val());                                 
                }
            }
        });
        
        for (var i = 0; i < tinymce.editors.length; i++) {            
                tinymce.editors[i].setContent($('textarea[name="'+tinymce.editors[i].id+'"]').val());
        };
    });
}

var live_search_thread = null;
function init_live_search() {    
    function live_search(obj) {
        if (obj.data('target')!=null) {
            var target=obj.data('target'); 
        }else {
            var target = 'ls-target';
        }
        if (obj.data('php')!=null) {
            var php=obj.data('php'); 
        }else {
            var php = 'run';
        }   
        if (obj.data('epage')!=null) {
            var epage='epage='+obj.data('epage'); 
        }else {
            var epage = '';
        }    
        if (obj.data('addon')!=null) {
            var addon='&addon='+obj.data('addon'); 
        }else {
            var addon = '';
        }                  
        if ($("#"+target).length == 0){
            obj.after('<div id="'+target+'" class="live_search_target"></div>')   
        }     
        $("#"+target).fadeIn('fast');  
       // alert(php+'.php?'+epage+'&cmd='+obj.data('cmd')+'&term='+obj.val()+addon);
        simple_load_nocache(target,php+'.php?'+epage+'&cmd='+obj.data('cmd')+'&term='+obj.val()+'&'+obj.data('addon'));
    }
    $('.live_search').unbind('click');
    $('.live_search').click(function() {
        if ($(this).val()!="") $(this).trigger('keyup');
    });    
    $('.live_search').unbind('keyup');
    $('.live_search').keyup(function() {
      clearTimeout(live_search_thread);
      var $this = $(this); live_search_thread = setTimeout(function(){live_search($this)}, 100);
    });  
}

function set_vertical_menu() {
            $('a.vertmenuclick').unbind('click');
            $('a.vertmenuclick').click(function(e) {
                e.preventDefault();
                $('.vertmenuclick').parent().removeClass('active');
                $(this).parent().addClass('active');
                $('.vertmenulayer').hide();
                $('#layer'+$(this).data('layer')).fadeIn();
        });
}

function set_stdform() {
        var options = {
                type: 'POST',
                forceSync: true,
                beforeSubmit: showRequest,
                success: showResponse // post-submit callback 
        };    
    $('.stdform').unbind('submit');
    $('.stdform').submit(function() {
                $(this).ajaxSubmit(options);
                return false;
    });
}

function cancelbnt() {
    $('.cancelbtn').unbind('click');
    $('.cancelbtn').click(function(e) {
            e.preventDefault();
            show_black_bg();
            window.location.href='/admin/welcome.html';
    }); 
    $(".cancelbtn").closest('form').bind("keypress", function(e) {              
              if (e.keyCode == 13) {                
                 e.preventDefault();
                 $(".cancelbtn").closest('form').find( ':submit').click();
                 return false;
              }
    });     
}

function fwstart() {
    init_autojson_submit();
    init_live_search();
    set_ajaxdel_icon();
    set_vertical_menu();
    set_script_editor();
    set_stdform();
    cancelbnt();
    set_ajax_links();
    set_ajaxapprove_icons();
    $('.autosubmit').unbind('change');
    $('.autosubmit').change(function() {
          var btn = $(this).closest('form').find('.btn-primary:first');
          if (btn.length>0){
            btn.click();
          } else {
            btn = $(this).closest('form').find('.btn-default:first');
            btn.click();
          }
    });    
    $('.tc-link').unbind('click');
    $('.tc-link').click(function(e) {
            e.preventDefault(); 
            $(this).closest('.tc-tabs-box').children().find('li').removeClass('active');           
            $(this).parent().addClass('active');          
            //$(this).closest('.tc-tabs-box').parent().parent().find('.tabs:first').find('.tabvisi').hide();  
            $(this).closest('.tc-tabs-box').next('.tabs:first').find('.tabvisi').hide();
            if ( $($(this).data('ident')).is(':hidden')) {                                
                $($(this).data('ident')).fadeIn('fast');
                
                if ($(this).data('function')!="" && $(this).data('function')!=undefined) {
                    eval($(this).data('function')+'()');
                }    
            }    
    });   
    set_mark_all_checkboxes();  
}

function std_load_gbltpl(tid,langid,reload) {
    load_orga_tree(reload);
    if (langid=="") langid=1;    
    $('#websearchresult').fadeOut();
    tab_visi_by_ident('webtreetabs','orgatab');    
    simple_load('admincontent','/admin/run.php?epage=gbltemplates.inc&id='+tid+'&uselang='+langid+'&cmd=load_gbltpl_ax');
    
    var ref = $('#gbltpltreeul').jstree(true),sel = ref.get_selected();
    sel = sel[0];
    if (sel!='gbltreenode-'+tid){
        $("#gbltpltreeul").jstree("close_all");
        $('#gbltpltreeul').jstree('select_node', 'gbltreenode-'+tid);
    }
    hide_black_bg();     
}

function expand_node_webtree(nodeID,tree) {
    while (nodeID != 'webtreeroot') {
        $("#"+tree).jstree("open_node", nodeID);
        var thisNode = $("#"+tree).jstree("get_node", nodeID);
        nodeID = $("#"+tree).jstree("get_parent", thisNode);
        if (nodeID==false) {
            break;
            }
}
}



function load_orga_tree(reload) {
    var doreload=0;
    if (reload!=undefined && reload!="") {
        doreload=1;
    }
    if ($('#orga-gbltpl').html()=="" || doreload==1) {    
        simple_load('orga-gbltpl','/admin/run.php?epage=websitemanager.inc&cmd=load_gbltpl_tree');
        simple_load('orga-toplevel','/admin/run.php?epage=tplmgr.inc&cmd=load_toplevel_tree');        
        simple_load('orga-inlays','/admin/run.php?epage=inlayadmin.inc&cmd=load_inlay_tree');
        reload_usertpl_tree();
        reload_flextpl_tree();
        reload_gblvar_tree(0);
    }
}

function reload_flextpl_tree() {
    simple_load('orga-flextemplates','/admin/run.php?epage=flextemp.inc&cmd=load_tpl_tree');
}

function reload_usertpl_tree() {
    simple_load('orga-usertemplates','/admin/run.php?epage=tplvars.inc&cmd=load_tpl_tree');
}

function reload_gblvar_tree(opentree) {
var doopentree=0;
    if (opentree!=undefined && opentree!="") {
        doopentree=1;
    }    
    simple_load('orga-gblvars','/admin/run.php?epage=gblvars.inc&cmd=load_var_tree&doopentree='+doopentree);    
}

function escapeHtml(raw) {
    return raw.replace(/[&<>"']/g, function onReplace(match) {
        return '&#' + match.charCodeAt(0) + ';';
    });
}

function scroll_content_table(prop){
    $('html,body').animate({scrollTop: $("#"+prop).offset().top 
+ parseInt($("#"+prop).css('padding-top'),10) 
+ parseInt($("#"+prop).css('margin-top'),10) - 100 },'slow');
}



$(document).ready(function() {

        var textareasave_options = {
                type: 'POST',
                forceSync: true,
                success: show_saved_msg // post-submit callback 
        };

        $('.textareasave').submit(function() {
                $(this).ajaxSubmit(textareasave_options);
                return false;
        });
        fwstart();
        tab_visi(1);
        // Image Preview Hover
        var offsetX = 20;
        var offsetY = 10;
        $('a.galhover').hover(function(e) {
                var imglink = $(this).attr('href');
                $(' <img id="largeImage" src="' + imglink + '" / > ').css({
                        'top': e.pageY + offsetY,
                        'left': e.pageX + offsetX
                }).appendTo('body');
        }, function() {
                $('#largeImage').remove();
        });
        $('a.galhover').mousemove(function(e) {
                $('#largeImage').css({
                        'top': e.pageY + offsetY,
                        'left': e.pageX + offsetX
                });
        });
        $('a.galhover').click(function(e) {
                e.preventDefault();
        });
        
        // Hover width clickable
        $('a.galhoverclick').hover(function(e) {
                var imglink = $(this).attr('rel');
                $(' <img id="largeImage" src="' + imglink + '" / > ').css({
                        'top': e.pageY + offsetY,
                        'left': e.pageX + offsetX
                }).appendTo('body');
        }, function() {
                $('#largeImage').remove();
        });
        $('a.galhoverclick').mousemove(function(e) {
                $('#largeImage').css({
                        'top': e.pageY + offsetY,
                        'left': e.pageX + offsetX
                });
        });

        $("#statusinfo").hide();

        $('#websearchresult').mouseleave(function() {
            $(this).fadeOut('fast');
        }); 

    
});

        /**
        * this workaround makes magic happen
        * thanks @harry: http://stackoverflow.com/questions/18111582/tinymce-4-links-plugin-modal-in-not-editable
        * http://jsfiddle.net/e99xf/198/
        * fix editing in tinymce in bootstrap modal window: source code, links etc
        */
        $(document).on('focusin', function(e) {
            if ($(e.target).closest(".mce-window").length) {
                e.stopImmediatePropagation();
            }
        }); 
        
/*
 * ContextMenu - jQuery plugin for right-click context menus
 *
 * Author: Chris Domigan
 * Contributors: Dan G. Switzer, II
 * Parts of this plugin are inspired by Joern Zaefferer's Tooltip plugin
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 * Version: r2
 * Date: 16 July 2007
 *
 * For documentation visit http://www.trendskitchens.co.nz/jquery/contextmenu/
 *
 */
(function($) {
        var menu, shadow, trigger, content, hash, currentTarget;
        var defaults = {
                menuStyle: {
                        listStyle: 'none',
                        padding: '1px',
                        margin: '0px',
                        backgroundColor: '#fff',
                        border: '1px solid #999',
                        width: '100px'
                },
                itemStyle: {
                        margin: '0px',
                        color: '#000',
                        display: 'block',
                        cursor: 'default',
                        padding: '3px',
                        border: '1px solid #fff',
                        backgroundColor: 'transparent'
                },
                itemHoverStyle: {
                        border: '1px solid #0a246a',
                        backgroundColor: '#b6bdd2'
                },
                eventPosX: 'pageX',
                eventPosY: 'pageY',
                shadow: true,
                onContextMenu: null,
                onShowMenu: null
        };
        $.fn.contextMenu = function(id, options) {
                if (!menu) { // Create singleton menu
                        menu = $('<div id="jqContextMenu"></div>').hide().css({
                                position: 'absolute',
                                zIndex: '500'
                        }).appendTo('body').bind('click', function(e) {
                                e.stopPropagation();
                        });
                }
                if (!shadow) {
                        shadow = $('<div></div>').css({
                                backgroundColor: '#000',
                                position: 'absolute',
                                opacity: 0.2,
                                zIndex: 499
                        }).appendTo('body').hide();
                }
                hash = hash || [];
                hash.push({
                        id: id,
                        menuStyle: $.extend({}, defaults.menuStyle, options.menuStyle || {}),
                        itemStyle: $.extend({}, defaults.itemStyle, options.itemStyle || {}),
                        itemHoverStyle: $.extend({}, defaults.itemHoverStyle, options.itemHoverStyle || {}),
                        bindings: options.bindings || {},
                        shadow: options.shadow || options.shadow === false ? options.shadow : defaults.shadow,
                        onContextMenu: options.onContextMenu || defaults.onContextMenu,
                        onShowMenu: options.onShowMenu || defaults.onShowMenu,
                        eventPosX: options.eventPosX || defaults.eventPosX,
                        eventPosY: options.eventPosY || defaults.eventPosY
                });
                var index = hash.length - 1;
                $(this).bind('contextmenu', function(e) {
                        // Check if onContextMenu() defined
                        var bShowContext = ( !! hash[index].onContextMenu) ? hash[index].onContextMenu(e) : true;
                        if (bShowContext) display(index, this, e, options);
                        return false;
                });
                return this;
        };

        function display(index, trigger, e, options) {
                var cur = hash[index];
                content = $('#' + cur.id).find('ul:first').clone(true);
                content.css(cur.menuStyle).find('li').css(cur.itemStyle).hover(

                function() {
                        $(this).css(cur.itemHoverStyle);
                }, function() {
                        $(this).css(cur.itemStyle);
                }).find('img').css({
                        verticalAlign: 'middle',
                        paddingRight: '2px'
                });
                // Send the content to the menu
                menu.html(content);
                // if there's an onShowMenu, run it now -- must run after content has been added
                // if you try to alter the content variable before the menu.html(), IE6 has issues
                // updating the content
                if ( !! cur.onShowMenu) menu = cur.onShowMenu(e, menu);
                $.each(cur.bindings, function(id, func) {
                        $('#' + id, menu).bind('click', function(e) {
                                hide();
                                func(trigger, currentTarget);
                        });
                });
                menu.css({
                        'left': e[cur.eventPosX],
                        'top': e[cur.eventPosY]
                }).show();
                if (cur.shadow) shadow.css({
                        width: menu.width(),
                        height: menu.height(),
                        left: e.pageX + 2,
                        top: e.pageY + 2
                }).show();
                $(document).one('click', hide);
        }

        function hide() {
                menu.hide();
                shadow.hide();
        }
        // Apply defaults
        $.contextMenu = {
                defaults: function(userDefaults) {
                        $.each(userDefaults, function(i, val) {
                                if (typeof val == 'object' && defaults[i]) {
                                        $.extend(defaults[i], val);
                                } else defaults[i] = val;
                        });
                }
        };
})(jQuery);
$(function() {
        $('div.contextMenu').hide();
});
jQuery.fn.reset = function() {
        $(this).each(function() {
                this.reset();
        });
}

// BOOTSTRAP TREE & OTHERS
$(document).ready(function () {
    $('label.tree-toggler').click(function () {
		$(this).parent().children('ul.tree').toggle(300);
	});
    
    
    $('#js-fixed-sidebar').css('width',$('#js-fixed-sidebar').parent().width()-10+'px');
    $("#js-sidebar-scroller").mCustomScrollbar({
    	setHeight:$(window).height()-100,
        setWidth: $('#js-fixed-sidebar').width(),        
    	theme:"minimal-dark"
    });
  
    $( window ).resize(function() {
        $('#js-fixed-sidebar').css('width',$('#js-fixed-sidebar').parent().width()-10+'px');       
    });
    
});



