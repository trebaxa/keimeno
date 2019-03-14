<% if (count($RESOURCE.flextpl.tpls)>0)%>
 <form action="<%$PHPSELF%>" method="POST" class="jsonform">
  <input type="hidden" value="save_html_table" name="cmd" />
  <input type="hidden" value="<%$epage%>" name="epage" />
  <input type="hidden" value="<%$RESOURCE.flextpl.id%>" name="t_ftid" />
       
      <table class="table table-striped table-bordered table-hover" id="feedback-table">
            <thead>
                <tr>
                    <th>HTML Vorlage</th>
                    <th class="text-center">verwenden</th>
                    <th>Zeigen auf Seite</th>
                    <th></th>
                </tr>
            </thead>
            
            <% foreach from=$RESOURCE.flextpl.tpls item=row %>
                <tr>
                    <td><a href="javascript:void(0)" onclick="simple_load('js-tpledit','<%$eurl%>cmd=edittpl&flxid=<%$RESOURCE.flextpl.FID%>&id=<%$row.id%>');"><% $row.t_name%></a></td>
                    <td class="text-center">
                        <div class="radio">
                            <label>
                                <input <% if ($row.t_use==1) %>checked<%/if%> type="radio" name="FORM[t_use]" value="<%$row.id%>" />
                            </label>
                        </div>
                    </td> 
                    <td>
                        <div class="input-group">
                            <select class="form-control" name="FORMSET[<%$row.id%>][t_pageid]">
                                   <% foreach from=$RESOURCE.menu_selectox item=rvol key=rkey %>                            
                                        <option <% if ($rkey==$row.t_pageid) %>selected<%/if%> value="<%$rkey%>"><%$rvol%></option>
                                   <%/foreach%>              
                            </select>
                                <div class="input-group-btn"><button type="submit" class="btn btn-primary"><i class="fa fa-save"></i></button></div>
                            </div>
                    </td>
                    <td class="text-right">
                        <div class="btn-group">
                            <% foreach from=$row.icons item=picon %><% $picon %><%/foreach%>
                            <a class="btn btn-default" href="javascript:void(0)" onclick="simple_load('js-tpledit','<%$eurl%>cmd=edittpl&flxid=<%$RESOURCE.flextpl.FID%>&id=<%$row.id%>');"><i class="fa fa-pencil-square-o"></i></a>
                        </div>
                    </td>
                </tr>
            <%/foreach%>
        </table>
     <button class="btn btn-primary" type="submit">speichern</button>
     </form>   
<%else%>
    <div class="alert alert-info">Keine Templates angelegt</div>
<%/if%>  