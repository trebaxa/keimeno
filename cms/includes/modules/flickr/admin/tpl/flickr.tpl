<div class="page-header"><h1>Flickr API</h1></div>
<div class="btn-group"><a class="btn btn-default" href="#" onclick="syncstream();">Foto Stream von "<%$gbl_config.fli_youruser%>" herunterladen</a></div>
<% if ($section=='start') %>
  <div id="flickrimglist" class="row"> 
    <% foreach from=$FLICKR.fotostram item=row %>
    <div class="col-md-2">      
        <div class="thumbnail" style="height:300px">
            <img src="<%$row.thumb%>" alt="<%$row.title|hsc%>" class="img-thumbnail">
            <div class="caption">
                <h3><%$row.p_title%></h3>
            </div>    
            <p><strong><%$row.date%></strong><br>
            <%$row.p_comment|truncate:20|nl2br%></p>
        </div>    
    </div>        
    <%/foreach%>
  </div>  

<%/if%>

<% if ($section=='conf') %>
    <%$FLICKR.CONFIG%>
    <% if ($gbl_config.fli_token=="") %>    
        <a href="<%$PHPSELF%>?epage=<%$epage%>&cmd=get_token" target="_blank" title="get token">Access Token anfordern</a> |    
        <a href="javascript:void()" onclick="save_token();" title="save token">Save Token</a>
    <%/if%>
<%/if%>

<script>
function save_token() {
    jsonexec('<%$PHPSELF%>?epage=<%$epage%>&cmd=save_token');    
}
function syncstream() {
    $('flickrimglist').html('Fotos werden geladen...<br>');
   simple_load('flickrimglist','<%$PHPSELF%>?epage=<%$epage%>&cmd=sync_stream');      
}
</script>