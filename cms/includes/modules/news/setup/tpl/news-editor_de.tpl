<% if ($aktion=='edit') %>
<% if ($id > 0) %>
<h1><% $news_obj.date %> - <% $news_obj.title %></h1>
<div style="text-align:right;width:100%;">
<a title="{LBL_SHOW}" href="<%$news_obj.detail_link%>"><img title="{LBL_SHOW}" alt="{LBL_SHOW}" src="<%$PATH_CMS%>images/page_view.png" ></a>
</div>
<%else%>
<h1>Anlegen</h1>
<%/if%>
<form role="form" method="post" action="<%$PHPSELF%>" enctype="multipart/form-data">
    <input type="hidden" name="aktion" value="a_save">
    <input type="hidden" name="id" value="<%$news_obj.NID%>">
    <input type="hidden" name="conid" value="<%$news_obj.CID%>">
    <input type="hidden" name="FORM_CON[lang_id]" value="<%$uselang%>">
    <input type="hidden" name="page" value="<%$page%>">
    <input type="hidden" name="FORM_CON[nid]" value="<%$news_obj.NID%>">
    <table class="tab_std"  width="600">
    <tr><td colspan="2"><strong>Sprache:</strong><br><select class="form-control" onChange="location.href=this.options[this.selectedIndex].value">
        <% foreach from=$language_table item=lang %>
         <option <% if ($uselang==$lang.id) %>selected <%/if%>value="<%$PHPSELF%>?page=<%$page%>&aktion=<%$aktion%>&id=<%$news_obj.NID%>&uselang=<%$lang.id%>"><%$lang.post_lang%></option>
    <%/foreach%>
    </select>    </td></tr>
        <tr><td colspan="2"><strong>Letzte &Auml;nderung:</strong><br><%$news_obj.n_lastchange%></td></tr>
        <% if ($id > 0) %>
        <tr><td><strong>Author:</strong><br><%$news_obj.nachname%>, <%$news_obj.vorname%>, <%$news_obj.kid%></td></tr>      
        <%else%>
        <tr><td><strong>Author:</strong><br><%$customer.nachname%>, <%$customer.vorname%>, <%$customer.kid%></td></tr>      
        <%/if%>
        <tr><td colspan="2"><strong>veröffentlicht in:</strong><br><%$groupselect%></td></tr>
        <tr><td colspan="2"><strong>Titel:</strong><br><input value="<%$news_obj.title%>" name="FORM_CON[title]"><span class="important"><%$form_err.title%></span></td></tr>
        <tr><td colspan="2"><strong>Datum:</strong><br><input class="form-control" type="text" value="<%$news_obj.ndate%>" name="FORM[ndate]"></td></tr>
        <tr><td colspan="2"><strong>Einleitung:</strong><br><textarea class="form-control"  rows="6" cols="60" name="FORM_CON[introduction]"><%$news_obj.introduction%></textarea><span class="important"><%$form_err.introduction%></span></td></tr>
        <tr><td colspan="2"><strong>Inhalt:</strong><br><%$news_obj.fck%><span class="important"><%$form_err.content%></span></td></tr>
        <% if ($news_obj.picture!="") %>
         <tr><td colspan="2"><strong>Inhalt:</strong><br><img  src="<%$news_obj.aicon%>"></td></tr>
         <%/if%>
<tr>
    <td>Icon:</td>
    <td><input type="file" name="dateiicon"> 
    <% if ($news_obj.n_icon!="") %>
     <br><img src="<%$news_obj.n_icon%>" >
     <% if ($customer.PERMOD.newslist.del==TRUE || $news_obj.n_kid==$customer.kid ) %>
     <br><a href="<%$PHPSELF%>?page=<%$page%>&aktion=delicon&id=<%$news_obj.NID%>">löschen</a>
     <%/if%>
    <%/if%>
    </td>
</tr>     
        <tr>
    <td>Datei Anhang:</td>
    <td><input type="file" name="datei"> </td>
</tr>
</table>
<% html_subbtn class="btn btn-primary" value="speichern" %>
</form>




<% if (count($news_obj.filelist) > 0)%>
<br><br>
<h3>Anhänge</h3><br>
        <table class="tab_std" border="1">
        <% foreach from=$news_obj.filelist item=afile %>
                <% if ($sclass=="row1") %> <% assign var=sclass value="row2" %> <% else %>   <% assign var=sclass value="row1" %>    <% /if %>
                        <tr class="<%$sclass%>">                                
                                <td><a title="<%$afile.f_file%>" target="_afile" href="<%$PATH_CMS%><%$NEWS_PATH%><%$afile.f_file%>"><%$afile.f_file%></a></td>
                                <td><%$afile.humanfilesize%></td>
                                <td>
                                <% if ($afile.thumbnail!="") %>
                                        <img src="<%$PATH_CMS%><%$afile.thumbnail%>" alt="<%$afile.f_file%> <%$afile.resu%>" title="<%$afile.f_file%> <%$afile.resu%>" >
<%/if%>
</td>
                                <td class="text-right">
<% if ($customer.PERMOD.newslist.del==TRUE || $news_obj.n_kid==$customer.kid ) %>
<a onclick="return confirm('Sind Sie sicher?')" href="<%$PHPSELF%>?gid=<%$news_obj.group_id%>&id=<%$news_obj.NID%>&page=<%$page%>&aktion=a_delfile&fileid=<%$afile.id%>">
<img src="<%$PATH_CMS%>images/page_delete.png" title="löschen"  alt=""></a>
<%/if%> 
</td>
                        </tr>
                        <% /foreach %>
                </table>          
<% /if %>

<%/if%>