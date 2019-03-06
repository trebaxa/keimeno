<% if ($gbl_config.tag_enabled==1 && count($tag_result)==0 && $PAGEOBJ.basis.tagable==1) %>
<% if ($cobj.id>0 && $cobj.gbl_template==0 && $cobj.c_type=="T" && $cobj.module_id=="content_page") %>
<div style="margin-top:30px;">
<form method="POST" action="<%$PHPSELF%>">
<input type="hidden" name="TAGFORM[pid]" value="<% $page %>">
<input type="hidden" name="aktion" value="savetagcloud">
<input type="hidden" name="last_aktion" value="<%$aktion%>">
<input type="hidden" name="page" value="<%$page%>">

<table >
    <tr>    <td colspan="2"><h2>{LBL_TAGCLOUDIN}</h2></td>    </tr>
<tr>
    <td><input type="text" name="TAGFORM[name]" size="30" value=""></td>
    <td><% html_subbtn class="sub_btn" value="{BTN_SENDEN}" %></td>
    </tr>
    <tr>    <td colspan="2">{LBL_TAGINFO}</td>    </tr>
    </table>
</form>
</div>
<%/if%>
<%/if%>
