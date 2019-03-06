
<div id="socialstream"> 
    <% foreach from=$socialmediastream item=row %>
    <div style="float:left;margin-left:6px;width:110px;">      
      <!--  <%$row.socialtype%> -->
        <a href="<%$row.link%>" target="_blank" title="<%$row.title|hsc%>">GO <%$row.socialtype%></a>
        
        <% if ($row.thumb!="") %>
            <img  src="<%$row.thumb%>" alt="<%$row.title|hsc%>" class="thumb">
        <%/if%>
        <% if ($row.title!="") %>
            <strong><%$row.title%></strong><br>
        <%/if%>
        <strong><%$row.date%></strong><br>
        <%$row.text|truncate:60|nl2br%>
        <div class="smstime">
        <% if ($row.beforexmin<60) %>
            vor <%$row.beforexmin%> Minuten veröffentlicht
        <%/if%>
        <% if ($row.beforexhours<24) %>
            vor <%$row.beforexhours%> Stunden veröffentlicht
        <%/if%>
        <% if ($row.beforexmonths<1) %>
            vor <%$row.beforexdays%> Tagen veröffentlicht
        <%/if%>  
        <% if ($row.beforexmonths>1) %>
            vor <%$row.beforexmonths%> Monate(n) veröffentlicht
        <%/if%>          
        </div>
    </div>        
    <%/foreach%>
<div class="clearer"></div>    
</div>