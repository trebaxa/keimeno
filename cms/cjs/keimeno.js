var $ = $.noConflict();

var http = null;

$.extend({
  getUrlVars: function(iurl){
    var vars = {}, hash;
    var hashes = iurl.substring(iurl.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
      hash = hashes[i].split('=');
      vars[hash[0]] = hash[1];
     
    }
    return vars;
  }
  
});

function show_msge(msg,duration) {
    $('#savedresult').remove();
    $('body').prepend('<div id="savedresult" class="alert"></div>');
    $('#savedresult').removeClass('alert-success').addClass('alert-danger');
    $('#savedresult').html(msg);                
    show_saved_msg(duration); 
}

function show_msg(msg,duration) {
    $('#savedresult').remove();
    $('body').prepend('<div id="savedresult" class="alert"></div>');
    $('#savedresult').removeClass('alert-danger').addClass('alert-success');
    $('#savedresult').html(msg);                
    show_saved_msg(duration); 
}

function show_saved_msg(duration) { 
        if (duration == "" || duration == 0 || duration == null) duration = 3000;
        $("#statusinfo").hide();
        $('#savedresult').css("position","fixed");
        $('#savedresult').css("z-index","1066");
        $('#savedresult').css("top", '50%');
        $('#savedresult').css("left",'50%');
        $('#savedresult').css("transform", 'translate(-50%, -50%)');
        $('#savedresult').fadeIn();
        setTimeout('$("#savedresult").fadeOut();', duration);
        //$("#feedbackmsg").hide();
}

function before_axform_submit(formData, jqForm, options) {
        if (options.showspinner==true) {
            $(options.target).html('<i class="fa fa-spinner fa-spin"></i>');
        }
        var queryString = $.param(formData);
        return true;
}

var axform_success = function ax_form_response(responseText, statusText, xhr, $form) {
    fwstart();
}


function init_auto_ax_submit() {    
        $('.axform').unbind('submit');
        $('.axform').submit(function() {
            var axform_options = {
                    type: 'POST',
                    forceSync: true,
                    target:$(this).data('target'),
                    showspinner:$(this).data('showspinner'),
                    beforeSubmit: before_axform_submit,
                    success: axform_success
            };            
                        
            $(this).ajaxSubmit(axform_options);
            if ($(this).parent().hasClass('modal-content')) {
               var modalid = $(this).parent().parent().parent().attr('id');
                $('#'+modalid).modal('hide');
            }            
            return false;
        });     
       
}

function set_ajax_links() {   
       $('.ajax-link').unbind('click');
       $('.ajax-link').css('cursor','pointer');
       $('.ajax-link').click(function(event) {        
            event.preventDefault();
            var target_container = 'wrapper';
            if ($(this).data('target')!="") {
                target_container = $(this).data('target');                                
            }
            if (target_container==undefined) target_container = 'wrapper';                 
            simple_load(target_container, $(this).attr('href'),true);
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
                                        fwstart();
                                }
                                $('input[type=file]').val('');
                        }
                }
        });
}

function simple_load(id,url_link,gif) {     
  if (gif != "" && gif != null && typeof gif !== 'undefined' && $("#" + id).length > 0) {      
     $("#"+id).html('<img class="axloader" src="'+gif+'" border="0">');
  } else {
    $("#"+id).html('<img class="axloader" src="/images/axloader.gif">');    
  }	     
$.ajax({
  url: url_link+'&axcall=1',
  cache: true,
  success: function(html){
    if (id!="") $("#" + id).html(html);
     fwstart();
  }
});
}

function simple_append(id,url_link) {     
$.ajax({
  url: url_link+'&axcall=1',
  cache: true,
  success: function(html){
     if (id!="") $("#" + id).append(html);
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
    fwstart();
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

$.getDocHeight = function(){
     var D = document;
     return Math.max(Math.max(D.body.scrollHeight,    D.documentElement.scrollHeight), Math.max(D.body.offsetHeight, D.documentElement.offsetHeight), Math.max(D.body.clientHeight, D.documentElement.clientHeight));
};


function dc_show(conid, fwidth) {
        $('.global_black').remove();
        $('body').prepend('<div class="global_black"></div>');
        $('.global_black').css('height', $.getDocHeight());
        $('.global_black').css("z-index", 999);
        $('.global_black').show();
        $('.global_black').click(function() {dc_remove(conid);});
        $('#' + conid).prepend('<div style="float:right;" class="dc-close-link"><a href="javascript:void(0)" title="close" onClick="dc_closeLink(this);"><img id="closeicon-divframe" src="/images/opt_close.png" border="0"></a></div>');
        $('#' + conid).width(fwidth);
        $('#' + conid).css("position", "absolute");
        $('#' + conid).css("z-index", 1000);
        $('#' + conid).css("top", (($(window).height() - $('#' + conid).outerHeight()) / 2) + $(window).scrollTop() + "px");
        $('#' + conid).css("left", (($(window).width() - $('#' + conid).outerWidth()) / 2) + $(window).scrollLeft() + "px");
        if ($('#' + conid).outerHeight() > $(window).height()) {
                $('#' + conid).css("height", $(window).height() - 300);
                $('#' + conid).css("top", (($(window).height() - $('#' + conid).outerHeight()) / 2) + $(window).scrollTop() + "px");
        } else {
                $('#' + conid).css("height", 'auto');
        }
        $('#' + conid).fadeIn();
        $('#' + conid).css("display", 'block');        
}

function close_show_box() {
        $('#show_box').fadeOut();
        $('#ah_content_table').show();
        $('.global_black').remove();
        $('#adminheader').css('z-index', '1');
}

function print_show_box() {
        $("#show_box_iframe").get(0).contentWindow.print();
}

function add_show_box(srclink, width, height, noprinter, title) {        
        width = width +'px';
        height = height +'px';
        add_show_box_iframe(srclink, title, width, height);     
        return false;
}

function add_show_box_tpl(srclink, title) {
        $('#modal_frame').remove();
        $('body').prepend('<div class="modal fade" id="modal_frame" tabindex="-1" role="dialog" aria-labelledby="modal_frameLabel" aria-hidden="true"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><h4 class="modal-title" id="modal_frameLabel">'+title+'</h4></div><div class="modal-body" id="showboxcontent"></div></div></div></div>');
        $('#modal_frame').modal('show');
        simple_load("showboxcontent", srclink);
        return false;
}

function add_show_box_iframe(srclink, title, width, height) {
        if (title=="" || title==null || !title) title="&nbsp;";
        $('#modal_frame').remove();
        $('body').prepend('<div class="modal fade" id="modal_frame" tabindex="-1" role="dialog" aria-labelledby="modal_frameLabel" aria-hidden="true"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><button type="button" class="btn btn-default pull-right" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><button class="btn btn-default pull-right" type="button" onclick="print_show_box()"><i class="fa fa-print"></i></button><h4 class="modal-title" id="modal_frameLabel">'+title+'</h4></div><div class="modal-body" id="showboxcontent"><iframe id="show_box_iframe" class="embed-responsive-itemx" src="'+srclink+'"></iframe></div></div></div></div>');
        if (width=="" || width==null || !width) {
             width=$(window).width() * 0.7+'px';       
        }        
        if (height=="" || height==null || !height) {
            height='auto';
            //$(window).height() * 0.7+'px';                             
        }
        $('.modal .modal-dialog').css('width',width);   
        $('.modal .modal-dialog').css('height','auto');
        $('.modal-content').css('height','auto');
        $('#show_box_iframe').css('height',height);
        $('#show_box_iframe').css('width','100%');
        $('#showboxcontent').css('height','100%');             
        $('#modal_frame').modal('show');
        return false;
}

function dc_remove(conid) {
        $('#' + conid).fadeOut();
        $('.global_black').remove();
        $('.dc-close-link').remove();
}

function dc_closeLink(obj) {
        var conid = obj.parentNode.parentNode.id;
        dc_remove(conid);
}

function dc_init() {
        $('.divframe').hide();
}

function changeClass(idElement, newClass) {
        document.getElementById(idElement).setAttribute("class", newClass);
}

function before_json_submit(formData, jqForm, options) {
        var queryString = $.param(formData);
        return true;
}

function show_saved_msg(duration) {
        if (duration == "" || duration == 0 || duration == null) duration = 1000;
        $("#statusinfo").hide();
        $('#savedresult').css("position","fixed");
        $('#savedresult').css("z-index","999");
        $('#savedresult').css("top", '50%');
        $('#savedresult').css("left",'50%');
        $('#savedresult').css("transform", 'translate(-50%, -50%)');        
        $('#savedresult').fadeIn();
        setTimeout('$("#savedresult").fadeOut();', duration);
        $("#feedbackmsg").hide();
}

function show_json_answer(responseText, statusText, xhr, $form) {
        var obj = jQuery.parseJSON(responseText);
        $('#savedresult').remove();
        $('body').prepend('<div id="savedresult"></div>');        
        if (obj.msge != "") {
                $('#savedresult').html(obj.msge);
                $('#savedresult').removeClass('alert-success').removeClass('text-success').addClass('alert-danger').addClass('text-danger');
                show_saved_msg(6000);
        } else {
                $('#savedresult').removeClass('alert-danger').removeClass('text-danger').addClass('alert-success').addClass('text-success');
                $('#savedresult').html(obj.msg);
                show_saved_msg();
                if (obj.jsfunction != "") {
                        eval(obj.jsfunction + '('+obj.jsparams+')');
                }
                $('input[type=file]').val('');
        }
}

function init_autojson_submit() {
        var options = {
                type: 'POST',
                forceSync: true,
                beforeSubmit: before_json_submit,
                success: show_json_answer
        };
        $('.jsonform').unbind('submit');
        $('.jsonform').submit(function() {
                $(this).ajaxSubmit(options);
                return false;
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
                        var url = $(this).data('phpfile') + '?page=' + $(this).data('page') + '&cmd=' + $(this).data('cmd') + '&ident=' + $(this).data('ident') + '&' + $(this).data('toadd');
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

function set_json_links() {   
       $('.json-link').unbind('click');
       $('.json-link').css('cursor','pointer');
       $('.json-link').click(function(event) {
            event.preventDefault();
            jsonexec($(this).attr('href'), true);
        });
}

function set_data_bgi() {
    $( "[data-bgi]" ).each(function() {
        $(this).css('background-image', 'url('+$(this).data('bgi')+')');
    });
    $( "[data-bgp]" ).each(function() {
        $(this).css('background-position', 'url('+$(this).data('bgp')+')');
    });
}

function fwstart() {
    init_autojson_submit();
    set_ajaxdel_icon();
    init_auto_ax_submit();
    set_ajax_links();
    set_json_links();
    set_data_bgi();
}

function load_json_form(url, formid) {
    $('#'+formid).trigger("reset");
    $.getJSON( url, function( data ) {
        $.each( data, function( key, val ) {
            if ($('.js-'+key).length>0) {
                $('#'+formid+' .js-'+key).val(val);
            }
        });
    });
}

$(document).ready(function() {
        fwstart();
});