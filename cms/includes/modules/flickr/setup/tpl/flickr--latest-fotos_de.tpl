<div id="flickrimglist"> 
    <% foreach from=$flickrstream item=row %>
    <div style="float:left;margin-left:6px;width:110px;">      
        <img  src="<%$row.thumb%>" alt="<%$row.title|hsc%>" class="thumb">
        <strong><%$row.p_title%></strong><br>
        <strong><%$row.date%></strong><br>
        <%$row.p_comment|truncate:20|nl2br%>
    </div>        
    <%/foreach%>
  </div>
