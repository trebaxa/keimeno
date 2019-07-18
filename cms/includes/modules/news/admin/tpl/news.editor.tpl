<h3><%$news_obj.title%></h3>
<% if ($GBLPAGE.access.language==TRUE)%>

<%include file="cb.panel.header.tpl" title="Bearbeiten"%>
<form method="post" action="<%$PHPSELF%>" class="jsonform" enctype="multipart/form-data">
	<input type="hidden" name="cmd" value="a_save">
	<input type="hidden" name="id" value="<%$news_obj.NID%>">
	<input type="hidden" name="conid" value="<%$news_obj.CID%>">
	<input type="hidden" name="FORM_CON[lang_id]" value="<%$uselang%>">
	<input type="hidden" name="epage" value="<%$epage%>">
	<input type="hidden" name="FORM_CON[nid]" value="<%$news_obj.NID%>">
	
  <div class="row">
      <div class="col-md-6">
		<div class="form-group">
			<label>{LBL_ADMINWORKON}:</label>
			<br><%$news_obj.mitarbeiter_name%>
        </div>		
        <div class="form-group"><label>{LBL_TITLE}:</label><input class="form-control" value="<%$news_obj.title|sthsc%>" name="FORM_CON[title]"><span class="help-block">Achtung: Permalink wird dem Titel angepasst.</span></div>
		<div class="form-group"><label>{LBL_INTRODUCTION}:</label><textarea class="form-control" rows="6" cols="60" name="FORM_CON[introduction]"><%$news_obj.introduction|sthsc%></textarea></div>
		<div class="form-group"><label>{LBL_DATE}:</label><input type="text" class="form-control" value="<%$news_obj.ndate%>" name="FORM[ndate]"></div>
        <div class="form-group"><label>Externe URL:</label><input type="text" class="form-control" value="<%$news_obj.url|sthsc%>" name="FORM_CON[url]"></div>
      </div>  
      <div class="col-md-6">
        <div class="form-group"><label>Letzte &Auml;nderung:</label><br><%$news_obj.n_lastchange%>
          <div class="btn-group pull-right">
            <a class="btn btn-secondary" href="run.php?uselang=<%$uselang%>&cmd=remoteadd&type=news&id=<%$id%>&epage=newsletter.inc" title="{LBL_SENDTHISPERMAIL}"><i class="fa fa-envelope-o"></i></a>
            <a class="btn btn-secondary" href="<%$news_obj.link%>" target="_newsblank" title="view"><i class="fa fa-eye"></i></a>
            <a id="axapprove-<%$news_obj.NID%>" class="btn btn-secondary axapprove" data-phpself="<%$PATH_CMS%>admin/run.php" data-epage="news.inc" data-toadd="" data-cmd="axapprove_item" data-value="<% if ($news_obj.approval==1)%>0<%else%>1<%/if%>" data-ident="<%$news_obj.NID%>" href="javascript:void(0);">
            <i class="fa fa-circle <% if ($news_obj.approval==1)%>fa-green<%else%>fa-red<%/if%>"></i></a>
            </div>
        </div>
		<div class="form-group"><label>{LBL_LANGUAGE}:</label><br><%$langselect%></div>
		<div class="form-group"><label>{LBL_NEWSISVISINGROUPS}:</label><%$groupselect%></div>
		
		<div class="form-group"><label>Author (Kundenstamm):</label>
		<% if ($news_obj.kid>0)%><%$news_obj.nachname%>, <%$news_obj.vorname%>, <%$news_obj.kid%><%/if%>
		<br>Author festlegen:
        <input data-cmd="ax_ksearch" data-php="index" data-addon="&doaktion=setnewkid&orderby=nachname&epage=<%$epage%>&id=<%$id%>" type="text" class="form-control live_search" placeholder="{LBLA_CUSTOMER}" autocomplete="off">
        </div>		
        <div class="form-group"><label>Herausgeber:</label><input type="text" class="form-control" value="<%$news_obj.n_creator|sthsc%>" name="FORM[n_creator]"></div>
        <div class="form-group">
	       <label>Icon:</label>
	       <input type="file" name="dateiicon" class="autosubmit"> 
	       
	           <br><img src="<%$news_obj.n_icon%>" class="thumb newsicon" <% if ($news_obj.n_icon=="") %>style="display:none"<%/if%>>
	           <br><a class="btn btn-secondary remove_news_icon" href="javascript:void(0)" <% if ($news_obj.n_icon=="") %>style="display:none"<%/if%>><i class="fa fa-trash"><!----></i></a>
	       
        </div>		 
		<div class="form-group">
	       <label>Datei Anhang:</label>
	       <input type="file" name="datei" class="autosubmit">            
        </div>
        <div id="news-attachments">
            <%include file="news.attachments.tpl"%>
        </div>
</div><!-- COL -->
</div><!-- ROW -->
<div class="form-group"><label>{LBL_CONTENT}:</label><%$news_obj.fck%></div>
<% $subbtn %>
</form>
<%include file="cb.panel.footer.tpl"%>
<% else %>
    <%include file="no_permissions.admin.tpl" %>
<%/if%>


<script>
$('.remove_news_icon').click(function() {
    execrequest('<%$PHPSELF%>?epage=<%$epage%>&cmd=delicon&id=<%$news_obj.NID%>');
    $('.newsicon').fadeOut();
    $(this).fadeOut();
});

function reload_news_attachments() {
    simple_load('news-attachments','<%$PHPSELF%>?epage=<%$epage%>&cmd=reload_news_attachments&id=<%$news_obj.NID%>');
}

function reload_news_item() {
     var url = '<%$PHPSELF%>?epage=<%$epage%>&cmd=reload_news_item&id=<%$news_obj.NID%>';
      $.getJSON(url, function(data) {
        if (data.n_icon!=""){
                $('.newsicon').attr('src',data.n_icon+'?a='+Math.random(1000));
                $('.newsicon').fadeIn();
                $('.remove_news_icon').fadeIn();
            }
        reload_news_attachments();    
        });
}
</script>