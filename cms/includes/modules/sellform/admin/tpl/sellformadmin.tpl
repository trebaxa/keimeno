<!--<div class="page-header"><h1>Verkaufsformular</h1></div> -->


<!--  Olsi Bearbeitet -->
    <div class="page-header"><h1><i class="fa fa-file-code-o"></i>Verkaufsformular</h1></div>
<!-- /.page-header -->


<% if ($SELLFORM.rediinstalled==false) %>
    <div class="bg-danger">ACHTUNG! Redimero API App muss installiert sein.</div>
<%/if%>

<% if ($SELLFORM.invalidredi==true) %>
    <div class="bg-danger">Ungültige Redimero Verbindung</div>
<%/if%>

<% if ($section=='conf') %>
    <% $SELLFORM.CONFIG %>
<%/if%>

<% if ($section=='' || $section=='start') %>
<div class="btn-group">
    <a class="btn btn-default" href="<%$PHPSELF%>?epage=<%$epage%>&cmd=edit&section=editor">Neu anlegen</a>
</div>


<% if (count($SELLFORM.sellforms)>0) %>
<table class="table table-striped table-hover">
<thead><tr>
    <th>Name</th>
    <th>Nutzung</th>
    <th></th>
</tr></thead>
 <% foreach from=$SELLFORM.sellforms item=row %>
 <tr>
    <td><%$row.fo_name%></td>
    <td><%$row.tpl_inlay%></td>    
    <td class="text-right"> <% foreach from=$row.icons item=picon name=cicons %><% $picon %><%/foreach%></td>
 </tr>
 <%/foreach%>
</table>
<%/if%>


<% if (count($SELLFORM.zahlweisen)>0) %>
<div style="width:900px">  
 <fieldset>	
  <legend>Verf&uuml;gbare Zahlweisen</legend>
<table class="table table-striped table-hover">
<thead><tr>
    <th>Zahlweise</th>    
</tr></thead>
 <% foreach from=$SELLFORM.zahlweisen item=row %>
 <tr>
    <td><%$row.admin_label%></td>        
 </tr>
 <%/foreach%>
</table>
 </fieldset>	
</div> 
<%/if%>

<%/if%>


<% if ($section=='editor') %>
<form  onSubmit="showPageLoadInfo();" method="POST" name="qform" action="<%$PHPSELF%>">
<div style="width:900px">  
<fieldset>	
<legend>Verkaufsformular neu anlegen</legend>  
     <table >
        <tr>
            <td>Name:</td>
            <td><input type="text" class="form-control" value="<%$SELLFORM.sform.fo_name|hsc%>" name="FORM[fo_name]"></td>
        </tr>
        <tr>
            <td>Template:</td>
            <td><select class="form-control" name="FORM[fo_tpl]">
             <% foreach from=$SELLFORM.templates item=row %>
              <option <% if ($row.id==$SELLFORM.sform.fo_tpl) %>selected<%/if%> value="<%$row.id%>"><%$row.description%></option>
             <%/foreach%>
            </select>
            </td>
        </tr>        
     </table>
     <div class="subright"><%$subbtn%></div>
</fieldset>	
</div> 
  <input type="hidden" name="section" value="<%$REQUEST.section%>">
  <input type="hidden" name="cmd" value="save_sform">
  <input type="hidden" name="id" value="<%$GET.id%>">
  <input type="hidden" name="epage" value="<%$epage%>">
</form>

<div style="width:900px">  
 <fieldset>	
  <legend>Produkte hinzuf&uuml;gen</legend>
    Suchbegriff: <input id="sform-psearch" name="sword" type="text" class="form-control" value="">
    <div id="sfps"></div> 
 </fieldset>	
</div> 

<% if (count($SELLFORM.sform.products)>0) %>
<div style="width:900px">
<form class="jsonform form-inline" method="POST" action="<%$PHPSELF%>">
  <input type="hidden" name="cmd" value="save_order">
  <input type="hidden" name="id" value="<%$GET.id%>">
  <input type="hidden" name="epage" value="<%$epage%>">
 <fieldset>	
  <legend>Produkte, die zum Verkauf angeboten werden</legend>
<table class="table table-striped table-hover">
 <thead><tr>
    <th>Produkt</th>
    <th>Sort</th>
    <th>Tarif</th>
    <th class="text-right"></th>
 </tr></thead>
 <% foreach from=$SELLFORM.sform.products item=row %>
 <tr>
    <td><%$row.pname%></td>
    <td><input size="3" name="FORM[<%$row.pid%>][fm_order]" type="text" class="form-control" value="<%$row.fm_order%>"></td>
    <td><select class="form-control" name="FORM[<%$row.pid%>][fm_tarifid]">
        <option value="0">- keine Zuordnung -</option>
        <% foreach from=$SELLFORM.sform.abo_traife item=tarif %>
            <option <% if ($tarif.id==$row.fm_tarifid) %>selected<%/if%> value="<%$tarif.id%>"><%$tarif.tarif_title%></option>
        <%/foreach%>
    </select>
    </td>
    <td class="text-right"> <% foreach from=$row.icons item=picon name=cicons %><% $picon %><%/foreach%></td>
 </tr>
 <%/foreach%>
</table>
 </fieldset>	
 <%$subbtn%>
 </form>
</div> 
<%/if%>


<script>
$("#sform-psearch").keypress(function() {
  var sword = $(this).val();
  if (sword.length>2) {
    simple_load('sfps','<%$PHPSELF%>?epage=<%$epage%>&id=<%$GET.id%>&cmd=searchproducts&word=' + sword);
  }
});
</script>
<%/if%>

<% if ($section=='modstylefiles') %>
<% include file="modstylefiles.tpl"%>
<% /if %>