<h6>Template Variablen</h6>
    <div class="well" >
    
        <ul style="list-style:none">
            <% foreach from=$FLEXTEMP.flextpl.flexvars item=row %>
                <%assign var="resrcfilterdb" value="0"%>
                <% if ($row.v_type=='resrc') %><hr>
                    <%*$row.resrcvars|echoarr*%>
                    <code>&lt;!-- Resource <%$row.resrcvars.resrc.f_name%> | ID: <%$row.resrcvars.resrc.id%> !YYY! --></code><br>
                    <li><code>
                    &lt;% foreach from=<%$row.varname_blank%> item=row %&gt;</code>
                    <%*$row.resrcvars.vars_structure|echoarr*%>
                        <ul style="list-style:none">
                           <% foreach from=$row.resrcvars.vars_structure item=resrc_var %>
                                <li>
                                <code>
                                   <% if ($resrc_var.v_type=='img')%>
                                        &lt;img alt="&lt;%$row.<%$resrc_var.v_varname%>%&gt;" class="img-responsive" src="&lt;%$row.<%$resrc_var.v_varname%>%&gt;"&gt;
                                   <%elseif ($resrc_var.v_type=='resid') %>
                                      <%assign var="resrcfilter" value="1"%>
                                     &lt;!-- RFILTER[v_vid]=<%$resrc_var.id%> !XXX! -->
                                   <%else%>
                                        &lt;%$row.<%$resrc_var.v_varname%>%&gt;
                                   <%/if%>                                   
                                   </code>
                                </li> 
                           <%/foreach%>
                           <li>
                            <code>                               
                               &lt;!-- RFILTER[v_value]=&lt;%$row.id%&gt; -->
                            </code>
                            </li>
                           <li>
                            <code>&lt;!-- Detail Link --><br>&lt;%$row.resrc_link%&gt;</code>
                           </li>
                           <% if (count($row.resrcvars.dataset_structure)>0) %>
                             
                             <% foreach from=$row.resrcvars.dataset_structure key=ftable item=rowds %>
                                     <li>
                                        <br>
                                        <code>&lt;!-- DB <%$row.resrcvars.tables.$ftable.f_name%> --></code><br>
                                        <code> &lt;% foreach from=$row.dataset.<%$ftable%> item=ds %&gt;</code>
                                            <%*$row.resrcvars.dataset_structure.$ftable|echoarr*%>
                                            <ul style="list-style:none">
                                            <% foreach from=$row.resrcvars.dataset_structure.$ftable item=ds_var %>
                                                <li>
                                                <code>
                                                    <% if ($ds_var.v_type=='img')%>
                                                        &lt;img alt="&lt;%$ds.<%$ds_var.v_varname%>.value%&gt;" class="img-responsive" src="&lt;%$ds.<%$ds_var.v_varname%>.thumb%&gt;"&gt;
                                                    <%elseif ($ds_var.v_type=='resid') %>
                                                        &lt;%$ds.<%$ds_var.v_varname%>.value%&gt; &lt;!-- !AAA! -->
                                                        <%assign var="resrcfilterdb" value="1"%>
                                                        <%assign var="resrcfilterdb_column" value=$ds_var.v_col%>
                                                    <%else%>
                                                        &lt;%$ds.<%$ds_var.v_varname%>.value%&gt;
                                                    <%/if%>   
                                                </code>
                                                </li> 
                                           <%/foreach%>
                                           </ul>
                                           <code>&lt;%/foreach%&gt;</code>
                                           
                                           <% if ($resrcfilterdb==1) %><br>
                                                Beispiel Link zum Filtern der DB "<%$row.resrcvars.tables.$ftable.f_name%>" in Relation zu einer Resource:<br>
                                                <code>&lt;a href=&quot;&lt;%$eurl%&gt;cmd=&lt;%$cmd%&gt;&amp;DBRFILTER[columns][<%$ftable%>][&lt;%$row.id%&gt;][col]=<%$resrcfilterdb_column%>&quot;&gt;&lt;%$row.fv_landname%&gt;&lt;/a&gt;</code>
                                            <%/if%> 
                                      </li> 
                               <%/foreach%>
                              
                           <%/if%>
                        </ul>
                        <code>&lt;%/foreach%&gt;</code>
                    </li>
                <% else%>
                    <li><code><%$row.varname%></code></li>
                <%/if%>
            <%/foreach%>
        </ul>
        <% if ($resrcfilter==1) %>
            Beispiel Link zum Filtern einer Resource in Relation zu einer Resource:<br>
            <code>&lt;a href=&quot;&lt;%$eurl%&gt;cmd=&lt;%$cmd%&gt;&amp;RFILTER[v_vid]=!XXX!&amp;RFILTER[v_value]=&lt;%$row.id%&gt;&amp;RFILTER[resrcid]=!YYY!&quot;&gt;&lt;%$row.fv_landname%&gt;&lt;/a&gt;</code>
        <%/if%>    
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