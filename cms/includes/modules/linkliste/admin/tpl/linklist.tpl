<div class="page-header"><h1>Links Manager</h1></div>

<div class="btn-group">
<a class="btn btn-secondary" href="<%$PHPSELF%>?epage=<%$epage%>&cmd=edit_link">Neuer Link</a>
  <a class="btn btn-secondary" href="<%$PHPSELF%>?epage=<%$epage%>&cmd=groupedit">Neue Kategorie</a>
</div>

<% if ($cmd=='' || $cmd=='search') %>
<form action="<%$PHPSELF%>" method="post">
<input type="hidden" name="epage" value="<%$epage%>">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="cid" value="<%$REQUEST.cid%>">
 <div style="width:400px">
	<fieldset>
  <table class="table table-striped table-hover" >
          <tr>
            <td>Wort:</td><td><input type="text" class="form-control" value="<% $BALINK.PFILTER.wort|sthsc %>" size="15" name="PFILTER[wort]"></td>
          </tr>
          <tr>
            <td>Toplevel:</td><td>
                <select class="form-control custom-select" name="PFILTER[toplevel]">
                    <option value="0">-all-</option>
                    <% foreach from=$BALINK.toplevel item=row %>
                        <option <% if ($BALINK.PFILTER.toplevel==$row.id) %>selected<%/if%> value="<%$row.id%>"><%$row.description%></option>
                    <%/foreach%>
                </select>
            </td>
          </tr>
          <tr>
            <td>Country:</td><td>
                <select class="form-control custom-select" name="PFILTER[country]">
                    <option value="0">-all-</option>
                    <% foreach from=$BALINK.countries item=row %>
                        <option <% if ($BALINK.PFILTER.country==$row.id) %>selected<%/if%> value="<%$row.id%>"><%$row.land%></option>
                    <%/foreach%>
                </select>
            </td>
          </tr>  
          <tr>
            <td>Type:</td><td>
                <select class="form-control custom-select" name="PFILTER[type]">
                    <option <% if ($BALINK.PFILTER.type=='') %>selected<%/if%> value="">-all-</option>
                    <option <% if ($BALINK.PFILTER.type=='U') %>selected<%/if%> value="U">Banner</option>
                    <option <% if ($BALINK.PFILTER.type=='F') %>selected<%/if%> value="F">Flash</option>
                    <option <% if ($BALINK.PFILTER.type=='S') %>selected<%/if%> value="S">Script</option>           
                </select>
            </td>
          </tr>   
          <tr>
            <td>Kategorie:</td><td>
                <select class="form-control custom-select" name="PFILTER[cat]">
                    <option value="0">-all-</option>
                    <% foreach from=$BALINK.linklist_groups item=row %>
                        <option <% if ($BALINK.PFILTER.cat==$row.id) %>selected<%/if%> value="<%$row.id%>"><%$row.lc_title%></option>
                    <%/foreach%>
                </select>
            </td>
          </tr>   
    </table>    

    
    <div class="subright"><%$btnsearch%></div>
</fieldset>
</div>
</form>

<form action="<%$PHPSELF%>" method=post id="daform" name="daform">
<input type="hidden" name="epage" value="<%$epage%>">
<% include file="mark_all_checkboxes.tpl" %>
<% include file="linklist.table.tpl" %>
		
</form>	

<div class="alert alert-info">
Verwenden Sie das Tempalte "Banner Rotation", um die Banner entsprechend zu platzieren.<br>
<br>Beispiel Variable:<br>
$banner.C.T.picture_thumb <br>
Hier wird ein Banner im Bereich "Center" und "Top" dargestellt. Die Verwendung und Integration ist frei. Erzeugen Sie soviele Banner Rotation-Templates wie Sie brauchen.
<br>Legende:<br>
L = Links<br>
C = Center<br>
R = Right<br><br>
T = Top/Oben<br>
M = Mitte<br>
B = Bottom/Unten<br>
</div>

<%/if%>

<% if ($cmd=='edit_link') %>
	<% include file="linklist.editor.tpl" %>
<%/if%>

<% if ($cmd=='show_meta_import') %>
	<% include file="linklist.metaimport.tpl" %>
<%/if%>

<% if ($cmd=='groupman' || $cmd=='groupedit') %>
	<% include file="linklistman.tpl" %>
<%/if%>

<% if ($cmd=='conf' ) %>
<h3>{LBL_CONFIG}</h3>
	<% $BALINK.CONFIG%>
<h3>Rotation Settings</h3>    
<form class="stdform form-inline" action="<%$PHPSELF%>" method=post id="daform" name="daform">
<input type="hidden" name="epage" value="<%$epage%>">
<input type="hidden" name="cmd" value="save_toplevel_settings">
    <table  class="table table-striped table-hover">
    <thead><tr>
        <th>Toplevel</th>
        <th></th>
        <th></th>
    </tr></thead>
    <% foreach from=$BALINK.toplevel item=row %>
        <tr>
            <td><%$row.description%></td>
            <td><input <% if ($row.ts_random==0) %>checked<%/if%> type="radio" name="FORM[<%$row.id%>][ts_random]" value="0">definierte Sortierung</td>
            <td><input <% if ($row.ts_random==1) %>checked<%/if%> type="radio" name="FORM[<%$row.id%>][ts_random]" value="1">Zufall</td>
        </tr>
    <%/foreach%>
    </table>
    <%$subbtn%>
    </form>	
<%/if%>
