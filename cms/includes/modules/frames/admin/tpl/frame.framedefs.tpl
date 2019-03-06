 <h3>Rahmen definieren</h3>
 <div class="btn-group">
 <a class="btn btn-default" href="javascript:void(0)" onclick="$('.addframe').slideDown()">Neuer Rahmen</a>
 </div>
 
 <div class="addframe" style="display:none">
 <form method="post" action="<%$PHPSELF%>">
 <input type="text" class="form-control" name="FORM[fname]" placeholder="Rahmen Name">
 <input type="hidden" name="cmd" value="add_frame">
<input type="hidden" name="epage" value="<%$epage%>">
<%$subbtn%></form>   
 </div>
 
 
 
 <form method="post" name="form_color" action="<%$PHPSELF%>" class="stdform form-inline">
 <%$subbtn%>
        <table  width="99%" class="table table-striped table-hover" id="foto_rahmen-table">
    <thead>
        <tr>
            <td>#</td>
            <td>Rahmen Namen</td>
            <td>Breite (cm)</td>
            <td>H&ouml;he (cm)</td>
            <td></td>
        </tr>
    </thead>    
    <% foreach from=$FRAMES.framedefs item=row  %>
    <tr>
    <td><%$row.id%></td>
       <td> 
        <input type="hidden" name="fids[<%$row.id%>]" value="<%$row.id%>">
       <input type="text" class="form-control" name="FORM[<%$row.id%>][fname]" value="<%$row.fname|sthsc%>" size="36" >
            </td>                    
        <td><input type="text" class="form-control" value="<%$row.width_cm|sthsc%>" name="FORM[<%$row.id%>][width_cm]"></td>
        <td><input type="text" class="form-control" value="<%$row.height_cm|sthsc%>" name="FORM[<%$row.id%>][height_cm]"></td>
        <td> <% foreach from=$row.icons item=picon name=cicons %><% $picon %><%/foreach%></td>
    </tr>
    <%/foreach%>  
</table>


<%* Tabellen Sortierungs Script *%>
<%assign var=tablesortid value="foto_rahmen-table" scope="global"%>
<%include file="table.sorting.script.tpl"%>  


<input type="hidden" name="cmd" value="save_frame_defs">
<input type="hidden" name="epage" value="<%$epage%>">
<%$subbtn%></form>     