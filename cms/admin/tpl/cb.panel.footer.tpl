<% if ($type=="") %>
    </div><!--panel-body-->
     <% if ($text!="")%>
          <div class="panel-footer">
            <%$text%>
          </div>
     <%/if%>
    </div>
<%/if%>    

<% if ($type=="info") %>
      </div><!-- /.panel-body -->
 </div><!-- /.panel panel-default -->
<%/if%>