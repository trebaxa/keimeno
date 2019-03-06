<% if ($type=="") %>
<div class="x_panel mt-lg <%$class%>">
      <div class="x_title">
         <h2 class=""><% if ($icon!="") %><i class="fa <%$icon%>"></i> <%/if%><%$title%> <small><%$title_addon%></small></h2>
            <div class="clearfix"></div>
      </div>
      <div class="x_content">
<%/if%>       

<% if ($type=="info") %>
<div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title"><%$title%></h3><!-- /.panel-title -->
                    </div><!-- /.panel-heading -->
                    
                    <div class="panel-body">
<%/if%>