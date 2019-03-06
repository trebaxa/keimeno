<fieldset class="plugin">
<label>Newsgruppe:</label>
    <select class="form-control" name="PLUGFORM[groupid]">
        <% foreach from=$WEBSITE.PLUGIN.result.groups item=row %>
            <option <% if ($WEBSITE.node.tm_plugform.groupid==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
        <%/foreach%>
    </select>

<label>Template:</label>
    <select class="form-control" name="PLUGFORM[tplid]">
        <% foreach from=$WEBSITE.PLUGIN.result.templates item=row %>
            <option <% if ($WEBSITE.node.tm_plugform.tplid==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
        <%/foreach%>
    </select>

<label>News Anzahl:</label>
    <input size="3" maxlength="3" type="text" class="form-control" name="PLUGFORM[news_count]" value="<% $WEBSITE.node.tm_plugform.news_count %>">

<label>News Icon Breite:</label>
    <input size="3" maxlength="4" type="text" class="form-control" name="PLUGFORM[news_icon_width]" value="<% $WEBSITE.node.tm_plugform.news_icon_width|sthsc %>">

<label>News Icon H&ouml;he:</label>
    <input size="3" maxlength="4" type="text" class="form-control" name="PLUGFORM[news_icon_height]" value="<% $WEBSITE.node.tm_plugform.news_icon_height|sthsc %>">

<label>Icon Verkleinerung:</label>
      <select class="form-control" name="PLUGFORM[news_icon_type]">
            <option <% if ($WEBSITE.node.tm_plugform.news_icon_type=='resize') %>selected<%/if%> value="resize">resize</option>
            <option <% if ($WEBSITE.node.tm_plugform.news_icon_type=='crop') %>selected<%/if%> value="crop">crop</option>
    </select>

<label>Sortieren nach:</label>
      <select class="form-control" name="PLUGFORM[sort_column]">
            <option <% if ($WEBSITE.node.tm_plugform.sortdirec=='ndate') %>selected<%/if%> value="ndate">News Datum</option>
            <option <% if ($WEBSITE.node.tm_plugform.sortdirec=='title') %>selected<%/if%> value="title">Titel</option>
    </select>

<label>Sortierung:</label>
      <select class="form-control" name="PLUGFORM[sortdirec]">
            <option <% if ($WEBSITE.node.tm_plugform.sortdirec=='ASC') %>selected<%/if%> value="ASC">aufsteigend</option>
            <option <% if ($WEBSITE.node.tm_plugform.sortdirec=='DESC') %>selected<%/if%> value="DESC">absteigend</option>
    </select>    
</fieldset>