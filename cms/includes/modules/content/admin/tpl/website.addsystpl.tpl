<style>
#systplul {
 list-style:none;
 background:none;
 margin:10px;
}

#systplul li {
 padding-left:15px; 
 list-style:none;
}
#systplul li span {
 cursor:pointer;
 font-size:130%;
}

</style>
 <ul id="systplul">
<% function name="systpltree" %>
   <%foreach from=$items item=element%>
   <li <% if ($WEBSITE.node.tm_plugform.systplid==$element.id) %>class="sysopen"<%/if%>>
   <% if ($element.id==0) %>
    <span><%$element.description%> <i class="fa fa-chevron-down"></i></span>
   <%else%>
    <div class="radio">
     <label>
        <input <% if ($WEBSITE.node.tm_plugform.systplid==$element.id) %>checked<%/if%> type="radio" name="PLUGFORM[systplid]" value="<%$element.id%>">
        <%$element.description%>
        </label>
    </div>
    

   <%/if%>
   <%if !empty($element.children)%>
      <ul><%call name="systpltree" items=$element.children%></ul>
   <%/if%>
   </li>
   <%/foreach%>
<%/function%><% call name=systpltree items=$WEBSITE.SYSTPL.pages %>
</ul> 
<%$sysopen%>
<script>
$(document).ready(function() {
    $("#systplul li ul").css("display","none");
    $("#systplul li span").click(function () {
        $(this).parent().children("ul").slideToggle("slow");
    });
    <% if ($WEBSITE.node.tm_plugform.systplid>0) %>
    $('.sysopen').parent().parent().children("ul").slideToggle("slow");
    <%/if%>
});
</script>