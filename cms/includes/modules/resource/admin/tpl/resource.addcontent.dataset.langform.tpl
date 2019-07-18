<% if ($RESOURCE.active_lang>1 && count($RESOURCE.languages)>0) %>
    <div class="well">
        <div class="form-group">
            <label>Sprache</label>
            <select class="form-control custom-select" id="js-lang-resrc-change">
                <% foreach from=$RESOURCE.languages item=row %>
                    <% if ($row.approval==1) %>
                        <option <% if ($GET.langid==$row.id) %>selected<%/if%> value="<%$row.id%>"><%$row.post_lang%></option>
                    <%/if%>
                <%/foreach%> 
            </select>
        </div>
    </div>
    <hr />
    <script>
        $( "#js-lang-resrc-change" ).change(function() {
          simple_load('js-resrc-content', '<%$eurl%>cmd=show_add_datasets_by_lang&table=<%$GET.table%>&flxid=<%$RESOURCE.flextpl.FID%>&content_matrix_id=<%$GET.content_matrix_id%>&langid='+$(this).val(),1);
        });
    </script>
<%/if%>

<div id="js-resrc-content">
 <%include file="resource.addcontent.dataset.form.tpl"%>
</div>