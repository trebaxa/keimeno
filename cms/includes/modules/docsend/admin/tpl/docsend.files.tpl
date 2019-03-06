<% if (count($DOCSEND.files)>0) %>
<form method="post" action="<%$PHPSELF%>" class="ajaxform" data-target="js-ds-files-table">
        <input type="hidden" name="cmd" value="preview" /> 
        <input type="hidden" name="epage" value="<%$epage%>" />
        <input type="hidden" name="kid" value="0" id="js-customer-kid" />
 
  <% include file="cb.panel.header.tpl" title="Ihre Dateien"%>
  <table class="table table-hover table-striped">
    <thead>
      <tr>
        <th class="col-md-1"></th>
        <th>Datei</th>
        <th class="col-md-1">Größe</th>
        <th class="col-md-2">Datum</th>
        <th class="col-md-2"></th>
      </tr>
    </thead>
    <tbody>
  <% foreach from=$DOCSEND.files item=row %>
      <tr>
          <td>
           <input onclick="ds_check();" class="js-dscheckbox" type="checkbox" name="FORM[files][]" value="<%$row.hash%>" />
          </td>
          <td><a title="Download <%$row.file%>" href="<%$eurl%>cmd=ds_file_download&kid=<%$GET.kid%>&hash=<%$row.hash%>"><%$row.file%></a></td>
          <td><%$row.size%></td>
          <td><%$row.date%></td>
          <td class="text-right">
          <div class="btn-group">
                <% foreach from=$row.icons item=picon name=cicons %><% $picon %><%/foreach%>
                <a title="Download <%$row.file%>" href="<%$eurl%>cmd=ds_file_download&kid=<%$GET.kid%>&hash=<%$row.hash%>" class="btn btn-default"><i class="fa fa-download"></i></a>
            </div>    
          </td>
      </tr>
  <%/foreach%>
  </tbody>
  </table>
   <% include file="cb.panel.footer.tpl"%>
   
    <% include file="cb.panel.header.tpl" title="E-Mail Vorlage"%>
   <div class="form-group">
          <label>Email Vorlage:</label>
          <select name="FORM[mailid]" class="form-control">
            <% foreach from=$DOCSEND.mails item=row %>
                <option value="<%$row.id%>"><%$row.title%></option>
            <%/foreach%>
            </select>
        </div>
   
   <div class="form-group">
    <label class="sr-only" for="wort">{LA_KUNDENSUCHEN}</label>
    <input data-epage="<%$epage%>" data-cmd="cusearch" placeholder="{LA_KUNDENSUCHEN}" autocomplete="off" type="text" id="js-ls-ds" class="form-control live_search"  value="" name="wort" >
    <small id="js-customer"></small>
</div><!-- /.form-group -->    
       
        <div class="text-right">
            <button type="submit" disabled="" id="js-btn-dssend" class="btn btn-primary"><i class="fa fa-eye"></i> Vorschau</button>
        </div>
        <% include file="cb.panel.footer.tpl"%>
        </form>
  <%/if%>