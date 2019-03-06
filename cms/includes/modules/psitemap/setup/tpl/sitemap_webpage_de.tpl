
<ul id="psitemap">
<% function name="psitemap" %>
   <%foreach from=$items item=element%>   
   <li><a href="<%$element.catlink%>" title="<%$element.catlabel%>"><%$element.catlabel%></a>
   <%if !empty($element.children)%>
      <ul><%call name="psitemap" items=$element.children%></ul>
   <%/if%>
   </li>
   <%/foreach%>
<%/function%><%call name=psitemap items=$PSITEMAP.menu_arr%>
</ul>