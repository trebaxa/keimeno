<h6>Template Variablen</h6>
    <div class="well" >
    
        <ul style="list-style:none">
            <% foreach from=$FLEXTEMP.flextpl.flexvars item=row %>
                <% if ($row.v_type=='resrc') %>
                    <li><code> &lt;% foreach from=<%$row.varname_blank%> item=row %&gt;</code>
                    <%*$row.resrcvars.vars_structure|echoarr*%>
                        <ul style="list-style:none">
                           <% foreach from=$row.resrcvars.vars_structure item=resrc_var %>
                                <li>
                                <code>
                                   <% if ($resrc_var.v_type=='img')%>
                                        &lt;img alt="&lt;%$row.<%$resrc_var.v_varname%>%&gt;" class="img-responsive" src="&lt;%$row.<%$resrc_var.v_varname%>%&gt;"&gt;
                                   <%else%>
                                        &lt;%$row.<%$resrc_var.v_varname%>%&gt;
                                   <%/if%>
                                   </code>
                                </li> 
                           <%/foreach%>
                           <li>
                            <code>
                                 &lt;%$row.resrc_link%&gt;
                            </code>
                           </li>
                           <% if (count($row.resrcvars.dataset_structure)>0) %>
                             <li><code> &lt;% foreach from=$row.dataset item=ds %&gt;</code>
                                    <ul style="list-style:none">
                                    <% foreach from=$row.resrcvars.dataset_structure item=ds_var %>
                                        <li>
                                        <code>
                                            <% if ($ds_var.v_type=='img')%>
                                                &lt;img alt="&lt;%$ds.<%$ds_var.v_varname%>.value%&gt;" class="img-responsive" src="&lt;%$ds.<%$ds_var.v_varname%>.thumb%&gt;"&gt;
                                            <%else%>
                                                &lt;%$ds.<%$ds_var.v_varname%>.value%&gt;
                                            <%/if%>   
                                        </code>
                                        </li> 
                                   <%/foreach%>
                                   </ul>
                                   <code>&lt;%/foreach%&gt;</code>
                              </li> 
                           <%/if%>
                        </ul>
                        <code>&lt;%/foreach%&gt;</code>
                    </li>
                <% else%>
                    <li><code><%$row.varname%></code></li>
                <%/if%>
            <%/foreach%>
        </ul>
        
        <%*$FLEXTEMP.flextpl.flexvars|echoarr*%>
    </div>    
    
   <% if (count($FLEXTEMP.flextpl.datasetvarsdb)>0)%> 
   <h6>Beispiel Datensatzverarbeitung</h6>
   <div class="well" >
        <code>
        <%*$FLEXTEMP.flextpl.datasetvarsdb|echoarr*%>
            &lt;% foreach from=$flxt.dataset.<%$FLEXTEMP.flextpl.group.g_ident%> item=row %&gt;<br>
            <% foreach from=$FLEXTEMP.flextpl.datasetvarsdb item=row %>
               <% if ($row.v_type=='seli') %>
                &nbsp;&nbsp;&nbsp;&lt;div data-ident="&lt;%$row.<%$row.v_col%>.vident%&gt;"&gt;&lt;%$row.<%$row.v_col%>.value%&gt;&lt;/div&gt;<br>
               <%elseif ($row.v_type=='img') %>
                &nbsp;&nbsp;&nbsp;&lt;img alt="&lt;%$row.<%$row.v_col%>.value|sthsc%&gt;" class="img-responsive" src="&lt;%$row.<%$row.v_col%>.thumb%&gt;"&gt;<br>
               <%else%>
                &nbsp;&nbsp;&nbsp;&lt;%$row.<%$row.v_col%>.value%&gt;<br>
               <%/if%> 
            <%/foreach%>
            &lt;%/foreach%&gt;
        </code> 
    </div>

     <%/if%>