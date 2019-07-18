<div class="page-header"><h1>{LBL_NEWSGROUPS}</h1></div>

<div class="btn-group"><a class="btn btn-primary" href="javascript:void(0);" onclick="shownewnesgroup();"><i class="fa fa-plus"></i> Neue Gruppe anlegen</a></div>

<div id="newgroup" style="display:none;">
    <form method="POST" action="<%$PHPSELF%>">
        <input type="hidden" value="<%$epage%>" name="epage"/>
        <input type="hidden" value="addgtab" name="cmd"/>
        <div class="row">
            <div class="form-group col-md-3">
                <label>Gruppenname:</label>
                <input type="text" class="form-control" placeholder="Gruppename eintragen" autocomplete="off" required="" value="" name="FORM[groupname]"/>            
            </div>
        </div>
        <%$subbtn%>
    </form> 
</div>


<% if (count($NEWSADMIN.ngroups)>0) %>
<%include file="cb.panel.header.tpl" title="{LBL_NEWSGROUPS}"%>
    <form class="stdform" method="POST" action="<%$PHPSELF%>">
        <input type="hidden" value="<%$epage%>" name="epage">
        <input type="hidden" value="savegtab" name="cmd">
        <table class="table table-striped table-hover" id="news-groups-table" >
        	<thead><tr>
             <th>Name</th>
             <th>Template</th>
        	 <th></th>
             </tr>
                </thead>	
            		<% foreach from=$NEWSADMIN.ngroups item=row %>
                        <tr>
                            <td><input type="text" class="form-control" value="<%$row.groupname|hsc%>" name="FORM[<% $row.id %>][groupname]"></td>
                    		<td><%$row.templselect%></td>
                    		<td class="text-right">
                            <% if ($row.id>1) %><%$row.icon_del%><%/if%></td>
                    	</tr>
            		<% /foreach %>
        		</table>
                <%* Tabellen Sortierungs Script *%>
                <%assign var=tablesortid value="news-groups-table" scope="global"%>
                <%include file="table.sorting.script.tpl"%>
        <%$subbtn%>
    </form>   
<%include file="cb.panel.footer.tpl"%>
<%else%>		
<div class="alert alert-info">{LBL_NOITEMS}</div>
<%/if%>

<script>
function shownewnesgroup() {
    $('#newgroup').slideDown();
}
</script>   	