<% if ($PERM.core_acc_orgatab==true) %>
<ul class="sub-menu knone" >
    <li id="orga-gbltpl"></li>
    <% if ($PERM.core_acc_inlay==true) %><li id="orga-inlays"></li><%/if%>
    <% if ($PERM.core_acc_usertemplates==true) %><li id="orga-usertemplates"></li><%/if%>
    <li id="orga-flextemplates"></li>
    <li id="orga-resource"></li>
    <% if ($PERM.core_acc_gblvars==true) %><li id="orga-gblvars"></li><%/if%>
    <li id="orga-toplevel"></li>
  </ul> 
<%/if%>
