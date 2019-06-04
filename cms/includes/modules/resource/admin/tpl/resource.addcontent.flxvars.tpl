<%*$RESOURCE.flextpl|echoarr*%>
<% if (count($RESOURCE.flextpl.flexvars)>0)%>
    <% if ($RESOURCE.active_lang>1 && count($RESOURCE.languages)>0) %>
        <div class="well">
            <div class="form-group">
                <label>Sprache</label>
                <select class="form-control" id="js-lang-resrc-change">
                    <% foreach from=$RESOURCE.languages item=row %>
                        <% if ($row.approval==1) %>
                            <option <% if ($GET.langid==$row.id) %>selected<%/if%> value="<%$row.id%>"><%$row.post_lang%></option>
                        <%/if%>
                    <%/foreach%> 
                </select>
            </div>
        </div>    
        
        <script>
            $( "#js-lang-resrc-change" ).change(function() {
              simple_load('js-resrc-content', '<%$eurl%>cmd=show_add_content_by_lang&flxid=<%$RESOURCE.flextpl.FID%>&content_matrix_id=<%$GET.content_matrix_id%>&langid='+$(this).val());
            });
        </script>
    <%/if%>

<div id="js-resrc-content">
    <%include file="resource.addcontent.form.tpl"%>
</div>

  
<%/if%>