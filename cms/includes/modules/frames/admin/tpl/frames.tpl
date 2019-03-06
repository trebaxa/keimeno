 <div class="page-header"><h1><i class="fa fa-file-code-o"><!----></i>Foto-Rahmen</h1></div>
<% if ($section=='start') %>
    <% include file="frame.colors.tpl"%>
<%/if%>

<% if ($section=='framedefs') %>
    <% include file="frame.framedefs.tpl"%>
<%/if%>

<% if ($section=='conf') %>
    <%$FRAMES.CONFIG%>
<%/if%>

<% if ($section=='addfoto') %>    
     <h3>Rahmen Foto - <%$FRAMES.fcolor.kname%></h3>
     <form method="post" action="<%$PHPSELF%>" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<%$GET.id%>">
    <input type="hidden" name="cmd" value="a_fotosave">
    <input type="hidden" name="epage" value="<%$epage%>">
    	<table class="table table-striped table-hover">
	<tr>
	<td valign="top">Foto Datei:</td>
	<td class="normal"><input type="file" name="datei" size="30" class="file_btn" value="durchsuchen"></td>
	</tr>
	</table>
    <%$subbtn%></form>
<%/if%>