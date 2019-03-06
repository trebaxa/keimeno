<html>
<head>
<link rel="stylesheet" href="<% $PATH_CMS %>js/plugins/webcam/layout.css" type="text/css">
</head>
<body >
<!--
onmouseover="return true" onkeypress="return true" ondragstart="return false" onselectstart="return false" oncontextmenu="return false"
-->
<h1>Ihre Kommentare</h1>
      <table class="tab_std"  width="100%" cellpadding="3">
      <% foreach from=$wc_comments item=wc name=mt %>
        <tr><td><% $wc.datum %> - <% $wc.zeit %> </td>
            <td><font color="#E3492B"> <% $wc.content %></font></td></tr>
      <%/foreach%>
</table>


</body>
</html>
