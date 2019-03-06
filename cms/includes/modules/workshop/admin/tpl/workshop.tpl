<link rel="stylesheet" href="../includes/modules/workshop/admin/css/style.css" type="text/css"/>

<div class="page-header"><h1>Workshop Manager</h1></div>

<div class="btn btn-group">
    <a href="#" class="btn btn-default" onclick="reload_workshops(0);">Workshops</a>
    <a href="#" class="btn btn-default" onclick="reload_cities();">Städte</a>
    <a href="#" class="btn btn-default" onclick="reload_config();">Konfiguration</a>
    
</div>

<div id="js-ws-cont">
    <% if ($cmd=='editws')%>
        <%include file="workshop.editws.tpl"%>
    <%/if%>
    <% if ($cmd=='editcity')%>
        <%include file="workshop.cityedit.tpl"%>
    <%/if%>
</div>

<!-- Modal -->
<div class="modal fade" id="add_city" tabindex="-1" role="dialog" aria-labelledby="add_cityLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    <form action="<%$PHPSELF%>" method="POST" class="jsonform">
        <input type="hidden" name="cmd" value="add_city"/>
        <input type="hidden" name="epage" value="<%$epage%>"/>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="add_cityLabel">Stadt hinzufügen</h4>
      </div>
      <div class="modal-body">
        
        <div class="form-group">
            <label>Stadt Name</label>
            <input type="text" value="" autofocus="" name="FORM[c_city]" class="form-control" required="" autocomplete="off" />
        </div>
            
       
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">schließen</button>
        <button type="submit" class="btn btn-primary">speichern</button>
      </div>
       </form>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="add_workshop" tabindex="-1" role="dialog" aria-labelledby="add_workshopLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    <form action="<%$PHPSELF%>" method="POST" class="jsonform">
        <input type="hidden" name="cmd" value="add_workshop"/>
        <input type="hidden" name="epage" value="<%$epage%>"/>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="add_workshopLabel">Workshop hinzufügen</h4>
      </div>
      <div class="modal-body">
        
        <div class="form-group">
            <label>Workshop Name</label>
            <input type="text" value="" name="FORM[ws_title]" class="form-control" required="" autocomplete="off" />
        </div>
        <div class="form-group">
            <label>Stadt Filter:</label>
            <select name="FORM[ws_city]" class="form-control">
                <% foreach from=$WORKSHOP.cities item=row %>
                    <option value="<%$row.id%>"><%$row.c_city%></option>
                <%/foreach%>
            </select>
        </div>        
            
       
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">schließen</button>
        <button type="submit" class="btn btn-primary">speichern</button>
      </div>
       </form>
    </div>
  </div>
</div>


<script>
function reload_cities() {
    simple_load('js-ws-cont','<%$PHPSELF%>?epage=<%$epage%>&cmd=load_cities');
}
function reload_workshops(city) {
    simple_load('js-ws-cont','<%$PHPSELF%>?epage=<%$epage%>&cmd=reload_workshops&city='+city);
}
function reload_images(wsid) {
    simple_load('js-ws-bilder','<%$PHPSELF%>?epage=<%$epage%>&cmd=reload_images&wsid='+wsid);
}

function delete_img(wsid,img) {
    simple_load('js-ws-bilder','<%$PHPSELF%>?epage=<%$epage%>&cmd=delete_img&wsid='+wsid+'&img='+img);
}

function reload_config() {
    simple_load('js-ws-cont','<%$PHPSELF%>?epage=<%$epage%>&cmd=conf');
}
<% if ($section=='start')%>
    reload_workshops(0);
<%/if%>
</script>