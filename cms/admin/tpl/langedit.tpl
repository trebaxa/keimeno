<% if ($REQUEST.admin=='no')%>
        <%assign var=langlabel value="{LBLA_TRANSLATION}"%>
<%else%>
        <%assign var=langlabel value="{LBLA_TRANSLATION_CUST}"%>
<%/if%>

<div id="csvupdate" style="display:none">
<h3>{LBLA_ADDJOKER}:</h3>
    <form class="jsonform form-inline" action="<%$PHPSELF%>" method="post" enctype="multipart/form-data">
    <input type="hidden" name="admin" value="<%$REQUEST.admin%>">
    <input type="hidden" name="cmd" value="csvupdate">
    <input type="hidden" name="epage" value="<%$epage%>">
    <table class="table table-striped table-hover"> 
    <tr>
        <td>CSV Datei</td>
        <td><input type="file" name="datei">
    <tr><td colspan="2"><%$subbtn%></td></tr>
    </table>
    </form> 
</div>

<div class="modal fade" id="addjoker" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
     <form action="<%$PHPSELF%>" method="post">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">{LBLA_ADDJOKER}</h4>
      </div>
      <div class="modal-body">
            <input type="hidden" name="admin" value="<%$REQUEST.admin%>">
            <input type="hidden" name="cmd" value="add_keys">
            <input type="hidden" name="epage" value="<%$epage%>">
            <table class="table table-striped table-hover"> 
            <%for $foo=1 to 6%>    
                <tr>
                    <td>{LBL_NEWJOKER} <%$foo%>:</td><td><input <%if ($foo==1)%>autofocus <%/if%>type="text" class="form-control"  name=FELD[<%math equation="x - y" x=$foo y=1%>]></td>
                </tr>    
            <%/for%>
            </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <%$addbtn%>
      </div>
      </form> 
    </div>
  </div>
</div>

<% if ($section=="") %>
    <%include file="cb.panel.header.tpl" icon="fa-language" title="Übersetzungstabelle bearbeiten" title_addon="`$langlabel`"%>
        
       <div class="btn-group">
                    <%if ($LANGEDIT.canadd==true) %><a class="btn btn-default" href="javascript:void(0);" data-toggle="modal" data-target="#addjoker"><i class="fa fa-plus"></i> {LBLA_ADDJOKER}</a><%/if%>
                </div>
            <div id="langtable" class="form-inline"><%include file="langedit.table.tpl"%></div>
            <div class="form-feet">
                <div class="btn-group">
                    <%if ($LANGEDIT.canadd==true) %><a class="btn btn-default" href="javascript:void(0);" data-toggle="modal" data-target="#addjoker"><i class="fa fa-plus"></i> {LBLA_ADDJOKER}</a><%/if%>
                    <a class="btn btn-default" href="javascript:void(0);" onclick="$('#csvupdate').slideDown();">CSV Update</a>
                </div>
       
            </div>
       
        
    <%include file="cb.panel.footer.tpl"%> 
<%/if%>   

<script>
function reloadlangtable() {
    $('#csvupdate').hide();
    simple_load('langtable','<%$PHPSELF%>?epage=<%$epage%>&cmd=reloadtab&admin=<%$REQUEST.admin%>','');
}
</script> 