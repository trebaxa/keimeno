<link rel="stylesheet" href="../includes/modules/ramicronews/admin/css/style.css" type="text/css"/>

<div class="page-header"><h1>RA-Micro News</h1></div>

<p class="alert alert-info">
Lassen Sie Ihre lizensierte RA-Mirco News XML Datein per FTP in folgendes Verzeichnis laden:<br>
<br>
/file_data/ramirconews/
</p>

<%$RAMICRONEWS.CONFIG%>

<div class="row">
    <% foreach from=$RAMICRONEWS.news item=row name=gloop %>
        <div class="col-md-4">
            <div class="pull-right">
                <small><b><%$row.category%> <%$row.publishingdate%></b></small>
            </div>
            <a href="javascript:void(0);" onclick="add_show_box_tpl('<%$PHPSELF%>?epage=<%$epage%>&cmd=load_news&id=<%$row.id%>', '<%$row.title|sthsc%>')"><h3><%$row.title%></h3></a>            
            <% if ($row.subtitle!="") %><h5><%$row.subtitle%></h5><%/if%>            
            <p><%$row.introduction|st%></p>
            <br><i><%$row.reference%></i>
        </div>    
        <% if ($smarty.foreach.gloop.iteration % 3 == 0  )%></div><hr><div class="row"><%/if%>
    <%/foreach%>
</div>