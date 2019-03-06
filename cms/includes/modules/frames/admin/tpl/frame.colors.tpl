 <h3>Farbdefinitionen</h3>
    <form action="<%$PHPSELF%>" method="post" class="stdform form-inline">
    <table class="table table-striped table-hover" id="farb-table">
        <thead>
            <tr><th></th>
                <th>Farbedefinition</th>
                <th>Innere Farbe</th>
                <th>&auml;u&szlig;ere Farbe</th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        
<% foreach from=$FRAMES.colors item=row  %>
    <tr>
            <td><input type="checkbox" id="idcolor" name="color_id[<%$row.id%>]" value="<%$row.id%>"></td>
			<td><input type="text" class="form-control" size="41"  name="kname[<%$row.id%>]" value="<%$row.kname|hsc%>"><br>
                <br>
    			<%$row.icon_addfoto%>            
			</td>
		
            <td><input id="fcolor_2_<%$row.id%>field" type="text" class="form-control" size="6" value="<%$row.fcolor_2%>" name="fcolor_2[<%$row.id%>]"></input>
                <div style="width:30px;height:30px;background-color:#<%$row.fcolor_2%>">
                </div>
            </td>
           	 <td><input id="fcolor_1_<%$row.id%>field" type="text" class="form-control" size="6" value="<%$row.fcolor_1%>" name="fcolor_1[<%$row.id%>]"></input>
                <div style="width:30px;height:30px;background-color:#<%$row.fcolor_1%>"></div>
            </td>
           <!-- <td><input id="fcolor_3_<%$row.id%>field" type="text" class="form-control" size="6" value="<%$row.fcolor_3%>" name="fcolor_3[<%$row.id%>]"></input></td>
            <td><input id="fcolor_4_<%$row.id%>field" type="text" class="form-control" size="6" value="<%$row.fcolor_4%>" name="fcolor_4[<%$row.id%>]"></input></td>
            -->
			<td><% if ($row.thumb!="") %><img src="<%$row.thumb%>?a=<%$row.random%>" class="img-thumbnail"><%/if%></td>
			<td><img src="<%$row.preview%>?a=<%$row.random%>"></td>
    </tr>
            
<%/foreach%>  
</table>
<%* Tabellen Sortierungs Script *%>
<%assign var=tablesortid value="farb-table" scope="global"%>
<%include file="table.sorting.script.tpl"%>  
<input type="hidden" name="cmd" value="a_msave">
<input type="hidden" name="epage" value="<%$epage%>">
<input type="checkbox" name="dograb" value="1">Rahmenfarben von markierten Rahmen neu bestimmen<br>
<%$subbtn%></form>       