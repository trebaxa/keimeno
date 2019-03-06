<% if ($edit_form.event.EID>0) %>
<h1>Termin bearbeiten - <% $edit_form.FORM_CON.title %><h1>
<%else%>
<h1>Termin hinzufÃ¼gen - <% $seldate %><h1>
<%/if%>
<form action="<% $PHPSELF%>" method="post" enctype="multipart/form-data">
    <input type="hidden" name="FORM[group_id]" value="<% $edit_form.event.group_id %>">
    <input type="hidden" name="conid" value="<% $edit_form.FORM_CON.id %>">
    <input type="hidden" name="id" value="<% $edit_form.event.EID %>">
    <% if ($edit_form.event.EID==0) %>
    <input type="hidden" name="FORM[ndate]" value="<% $edit_form.event.date%>">
    <% /if %>
    <input type="hidden" name="page" value="<% $page %>">
    <input type="hidden" name="FORM_CON[lang_id]" value="<% $edit_form.uselang %>">
    <input type="hidden" name="aktion" value="a_save">
    <table class="tab_std"  width="100%">
   <tr><td><strong>Sprache:</strong><br><select onChange="location.href=this.options[this.selectedIndex].value">
        <% foreach from=$language_table item=lang %>
         <option <% if ($uselang==$lang.id) %>selected <%/if%>value="<%$PHPSELF%>?page=<%$page%>&aktion=<%$aktion%>&id=<%$edit_form.event.EID%>&uselang=<%$lang.id%>"><%$lang.post_lang%></option>
    <%/foreach%>
    </select>    </td></tr>
    <% if ($edit_form.event.EID>0) %>
    <tr><td class="tdlabel">Datum:<br>
        <input type="text" size="10" maxlength="10" name="FORM[ndate]" value="<% $edit_form.event.date%>">
        dd.mm.YYYY</td></tr>
    <% /if %>    
        <tr><td class="tdlabel">Uhrzeit:<br>Von<input size="5" value="<% $edit_form.event.time_from %>" name="FORM[time_from]">bis<input type="text" size="5" value="<% $edit_form.event.time_to %>" name="FORM[time_to]">hh:mm</td></tr>
        <tr><td class="tdlabel">Veranstaltungsort:<br><input size="30" value="<% $edit_form.event.place %>" type="text" name="FORM[place]"></td></tr>
        <tr><td class="tdlabel">GeoTagging<br><span class="small">(f&uuml;r GeoTagging / Google Maps / Bing Maps)</span>:<br><input size="30" value="<% $edit_form.event.c_gm_place %>" type="text" name="FORM[c_gm_place]"></td></tr>
        <tr><td class="tdlabel">GanztÃ¤gig:<input <% if ($edit_form.event.whole_day==1) %> checked <%/if%> type="checkbox" value="1" name="FORM[whole_day]"> Ja</td></tr>
        <tr><td class="tdlabel">Titel:<br><input size="30" value="<% $edit_form.FORM_CON.title %>" name="FORM_CON[title]"><span class="important"><%$form_err.title%></span></td></tr>
        <tr><td class="tdlabel">Einleitung:<br><textarea rows="6" cols="60" name="FORM_CON[introduction]"><% $edit_form.FORM_CON.introduction %></textarea><span class="important"><%$form_err.introduction%></span></td></tr>
        <tr>
    <td class="tdlabel">Icon:<br><input type="file" name="dateiicon"> <input type="image" src="<%$PATH_CMS%>/images/disk.png" class="subimg" title="speichern">
    <% if ($edit_form.event.c_icon!="") %>
     <br><img src="<% $edit_form.event.icon %>" >
     <br><a href="<%$PHPSELF%>?page=<%$page%>&aktion=delicon&id=<%$edit_form.event.EID %>">lÃ¶schen</a>
    <%/if%>
    </td>
</tr>   
    <tr>
    <td class="tdlabel">Datei Anhang:<br><input type="file" name="datei"> <input type="image" src="<%$PATH_CMS%>/images/disk.png" class="subimg" title="speichern"></td>
</tr>
<% if (count($edit_form.event.filelist) > 0)%>
<tr>
    <td class="tdlabel">Anh&auml;nge:<br>
    <table class="tab_std" border="1">
    <% foreach from=$edit_form.event.filelist item=afile %>
        <% if ($sclass=="row1") %> <% assign var=sclass value="row2" %> <% else %>   <% assign var=sclass value="row1" %>    <% /if %>
            <tr class="<%$sclass%>">
                <td><%$afile.uploadtime%></td>
                <td><a title="<%$afile.f_file%>" target="_afile" href="../<%$edit_form.event_PATH%><%$afile.f_file%>"><%$afile.f_file%></a></td>
                <td><%$afile.humanfilesize%></td>
                <td>
                <% if ($afile.thumbnail!="") %>
                    <img src="<%$afile.thumbnail%>" alt="<%$afile.f_file%> <%$afile.resu%>" title="<%$afile.f_file%> <%$afile.resu%>" ></td>
                <% else %>  
                 <%$afile.f_ext%>
                <%/if%>
                <td class="tdright">
                <% if ($customer.PERMOD.calendar.del==TRUE || $edit_form.event.c_kid==$customer.kid ) %>
<a onclick="return confirm('Sind Sie sicher?')" href="<%$PHPSELF%>?calgid=<%$edit_form.event.group_id%>&id=<%$edit_form.event.EID%>&page=<%$page%>&aktion=a_delfile&fileid=<%$afile.id%>">
<img src="<%$PATH_CMS%>images/page_delete.png" title="lÃ¶schen"  alt="lÃ¶schen"></a>
<%/if%> 
</td>
            </tr>
            <% /foreach %>
        </table>        
        </td>
</tr>
<% /if %>
        <tr><td class="tdlabel">Inhalt:<br><% $edit_form.event.fck %></td></tr>
        
    </table><% $subbtn %></form>
