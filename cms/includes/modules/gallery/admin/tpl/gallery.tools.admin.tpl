<%include file="cb.page.title.tpl" title="Tools" icon="far fa-image"%>

<div class="row">
    <div class="col-md-4">
 <%include file="cb.panel.header.tpl" title="{LA_ORIGINALBILDERVERKLEI}" class=""%>
<form method="post" action="<%$PHPSELF%>" enctype="multipart/form-data">
	<input type="hidden" name="aktion" value="gblresize">
	<input type="hidden" value="<%$epage%>" name="epage">
	<input type="hidden" value="1" name="start">
<table class="table table-striped table-hover"  >
	<tr>
		<td width="20%">{LA_MAXBREITE}</td>
		<td><input type="text" class="form-control" size="4" maxlength="4" name="ROPT[maxwidth]" value="1024"></td>
	</tr>
	<tr>
		<td>{LA_MAXHHE}</td>
		<td><input type="text" class="form-control" size="4" maxlength="4" name="ROPT[maxheight]" value="768"></td>
	</tr>
</table>
<% $btngo %>
</form>
<div class="alert alert-info">{LA_INFOORGBILDER}<br>
Zur Zeit sind <b><%$totalsize%></b> belegt.
</div>
<%include file="cb.panel.footer.tpl"%>
    </div>
    <div class="col-md-4">
        <%include file="cb.panel.header.tpl" title="{LA_WASSERZEICHEN}" class=""%>
        <form method="post" action="<%$PHPSELF%>" enctype="multipart/form-data">
        	<input type="hidden" name="cmd" value="change_watermark">
        	<input type="hidden" value="<%$epage%>" name="epage">
            <input type="file" value="" name="datei">
        <% $btngo %>
        </form>
        <% if ($watermark_exists==true) %>
            <img src="../images/watermark.png"  width="300" style="border:1px solid gray;">
        <%/if%><br>
        <a href="javascript:void(0);" onclick="clear_cache();">Bilder Cache leeren</a>
        <div class="p-3 mb-2 bg-success text-white" id="cacheclear" style="display:none">Cache geleert</div>
        <%include file="cb.panel.footer.tpl"%>
    </div>
    <div class="col-md-4">
        <%include file="cb.panel.header.tpl" title="{LA_BILDERVORSCHAUENNEUER}" class=""%>
        <form method="post" action="<%$PHPSELF%>" enctype="multipart/form-data">
        	<input type="hidden" name="aktion" value="genpiccache">
        	<input type="hidden" value="<%$epage%>" name="epage">
        	<input type="hidden" value="1" name="start">
        <% $btngo %>
        </form>
<div class="alert alert-info">{LA_INFOTHUMBN}</div>
        <%include file="cb.panel.footer.tpl"%>
    </div>
    <div class="col-md-6">
        <%include file="cb.panel.header.tpl" title="{LA_VALIDIERUNG}" class=""%>
        <% if ($VRES.count_fnote>=0 && count($VRES)>0) %>
            Letztes Validierungsresultat:
            <table class="table table-striped table-hover">
            		<tr>
            			<td>{LBL_NOTEXISTSFILE}</td><td class="text-right"><strong><%$VRES.count_fnote%></strong></td>
            		</tr>		
            		<tr>
            			<td>{LBL_FILESIZE} fixed</td><td class="text-right"><strong><%$VRES.count_fs%></strong></td>
            		</tr>
            		<tr>
            			<td>Nicht in DB gefunden</td><td class="text-right"><strong><%$VRES.count_notindb%></strong></td>
            		</tr>		
            		<tr>
            			<td>Defekte Bilder</td><td class="text-right"><strong><%$VRES.count_nullfiles%></strong></td>
            		</tr>				
            		
            		</table>		
		<%/if%>
        <form method="post" action="<%$PHPSELF%>" enctype="multipart/form-data">
        	<input type="hidden" name="aktion" value="a_picval">
        	<input type="hidden" value="<%$epage%>" name="epage">
        <% $btngo %>
        </form>
        <div class="alert alert-info">{LA_INFOVALID}</div>
        <%include file="cb.panel.footer.tpl"%>
    </div>
    <div class="col-md-6">
        <%include file="cb.panel.header.tpl" title="Permalinks neu erstellen" class=""%>
        <form method="post" action="<%$PHPSELF%>" class="jsonform" enctype="multipart/form-data">
        	<input type="hidden" name="cmd" value="rebuild_perma">
        	<input type="hidden" value="<%$epage%>" name="epage">
        <% $btngo %>
        </form>
        <%include file="cb.panel.footer.tpl"%>
    </div>
</div>


<script>
    function clear_cache() {
        execrequest('<%$PHPSELF%>?epage=<%$epage%>&cmd=clearcache');
        $('#cacheclear').fadeIn();
        setTimeout('$("#cacheclear").fadeOut();',3000);
    }
</script>


