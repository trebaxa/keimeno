<script>
function fwrealodbl() {
    simple_load('fwblacklist','<%$PHPSELF%>?epage=<%$epage%>&cmd=load_blacklist');
}
</script>

<% if ($aktion=="") %>	
<div class="page-header"><h1>Firewall Einstellungen</h1></div>
<form action="<%$PHPSELF%>" method="post" class="form jsonform">    
	<div class="row">
                <% foreach from=$FW_SETTINGS item=litem  %>		 
              <div class="col-md-3">  
              <%include file="cb.panel.header.tpl" icon="fa-cubes" title="`$litem.fw_description`"%>                
                    <div class="checkbox">
                     <label><input type="checkbox" name="FORM[<%$litem.id%>][fw_active]" <% if ($litem.fw_active==1) %>checked<%/if%> value="1"> Aktiv</label>
                    </div>
                    <div class="form-group">
                        <label>Zugriff-Wiederholungen:</label>
                        <input maxlength="2" type="text" class="form-control" name="FORM[<%$litem.id%>][fw_recalls]" value="<%$litem.fw_recalls%>">
                    </div>
                    <div class="form-group">
                        <label>Zeitintervall:</label>
                        <input size="2" maxlength="2" type="text" class="form-control" name="FORM[<%$litem.id%>][fw_timespan]" value="<%$litem.fw_timespan%>">
                        <p class="help-block">Sekunden</p>
                    </div>                    
                  <%include file="cb.panel.footer.tpl"%>
                 </div> 
            <%/foreach%>
            </div>  		
	<input type="hidden" name="cmd" value="save">
	<input type="hidden" name="epage" value="<%$epage%>">
	<%$subbtn%>
    
	</form>
<%/if%>

<% if ($aktion=="details") %>	
    <div class="page-header"><h1>Firewall Blacklist</h1></div>
    <h3>IP manuell blocken</h3>
    <div class="row">
        <div class="col-md-6">
            <form action="<%$PHPSELF%>" method="post" class="jsonform form-inline">
            <table class="table table-striped table-hover">
            	<tr><td>IP:</td><td><input placeholder="z.B. <%$MYIP%>" size="16" maxlength="15" type="text" class="form-control" name="FORM[fw_ip]" value="<%$litem.fw_ip%>"> (Ihre IP als Beispiel: <%$MYIP%>)</td></tr>
            </table>		
            	<input type="hidden" name="cmd" value="addip">
            	<input type="hidden" name="epage" value="<%$epage%>">
            	<%$subbtn%>
            	</form>
            <div id="fwblacklist"></div>
            <script>fwrealodbl();</script>	
            
        </div>
        <div class="col-md-6">
            <h3>IP GeoTracking</h3>
            <div id="mapsarea">
            <%$MYIP%>
                <iframe name="gmapsip" marginwidth="0" marginheight="0" src="<%$GMPASIP.iframe_url%>" frame  scrolling="no" style="height:400px;border:0px;"></iframe>
            </div>
        </div>
    </div>
<%/if%>
