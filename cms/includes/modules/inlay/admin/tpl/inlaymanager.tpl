<% if ($aktion=='' || $cmd=='ax_show_all') %>
    
    
        <div class="page-header"><h1><i class="fa fa-puzzle-piece"><!----></i> Inlays</h1></div>
    
    <%include file="cb.panel.header.tpl" title="Inlays"%>
       <div class="btn-group">        
        <a class="btn btn-default ajax-link" href="<%$eurl%>cmd=ax_show_all"><i class="fa fa-table"></i> Alle anzeigen</a>
        <a class="btn btn-default" data-toggle="modal" data-target="#meinModal"><i class="fa fa-plus"></i> Neues Inlay anlegen</a>
       </div>         
        
    
        <form class="stdform form-inline" method="post" action="<%$PHPSELF%>" enctype="multipart/form-data">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>{LBLA_DESCRIPTION}</th>
                        <th>Inlay Platzhalter</th>
                        <th>Smarty Implementierung</th>
                        <th>Option</th>
                    </tr>
                </thead>
                
                <% foreach from=$inlay_list item=inlay %>
                    <tr>
                        <td><input type="text" class="form-control" size="21" name="FORM[<% $inlay.id %>][description]" value="<% $inlay.description|hsc %>"></td>
                        <td><% $inlay.block_name %></td>
                        <td><code><% $inlay.blocktpl %></code></td>
                        <td class="text-right"><div class="btn-group"><% foreach from=$inlay.icons item=picon name=cicons %><% $picon %><%/foreach%></div></td>
                    </tr>
                <%/foreach%>
            </table>
            <input type="hidden" value="a_msave" name="aktion">
            <input type="hidden" name="epage" value="<% $epage %>"> 
            <input type="hidden" name="tmsid" value="<% $GET.tmsid %>"> 
            <div class="form-feet">
                <div class="btn-group">                    
                <% $subbtn %>
                </div><!-- /.btn-group -->
                
            </div>
        </form>
        <p class="alert alert-info mt-lg">Verwenden Sie den Smarty Implementierungscode, um das jeweilige Inlay entsprechend zu platzieren. Inlays k&ouml;nnen an beliebiger Stelle (z.B. Globale Templates) mehrfach eingebunden werden. Es spielt keine Rolle, ob Sie den "Inlay Platzhalter" verwenden oder direkt die SMARTY Implementierung. Den Platzhalter kann im Online-Editor verwendet werden, ohne dass dieser in den Textmodus umschaltet.</p>        
    <%include file="cb.panel.footer.tpl"%>


<%include file="cb.panel.header.tpl" title="System Templates auf andere Sprachen replizieren"%>
    

        <form class="stdform form-inline" method="POST" action="<%$PHPSELF%>">
            <input type="hidden" name="cmd" value="replicate_lang">
            <input type="hidden" name="epage" value="<%$epage%>">
            
            <div class="form-group">
                <label for="relaung">Inlays &uuml;berschreiben mit Inhalt aus Sprache:</label>
                <select id="relaung"class="form-control" name="langid">
                    <% foreach from=$langselect item=lang %><option value="<%$lang.id%>"><%$lang.post_lang%></option><%/foreach%>
                </select>
            </div><!-- /.form-group -->
            
            <%$btngo%>
        </form> 
            <p class="alert alert-info">Ausgew&auml;hlte Sprache auf alle anderen Sprachen &uuml;bertragen. Bestehende Inlays werden durch die ausgew&auml;hlte Sprache &uuml;berschrieben.</p>
<%include file="cb.panel.footer.tpl"%>


<%/if%>

<% if ($aktion=='edit' || $cmd=='ax_edit') %>

        <div class="page-header"><h1><i class="fa fa-puzzle-piece"><!----></i> Inlay Bearbeiten</h1></div>

 <%include file="cb.panel.header.tpl" title=$TPLOBJ.description%>
   

        <form class="stdform" method="post" action="<%$PHPSELF%>" enctype="multipart/form-data">
            <input type="hidden" name="tid" value="<% $GET.id %>">
            <input type="hidden" name="cmd" value="save_inlay">
            <input type="hidden" name="uselang" value="<% $GET.uselang%>">
            <input type="hidden" name="FORMCON[lang_id]" value="<% $GET.uselang %>">
            <input type="hidden" name="FORMCON[tid]" value="<% $GET.id %>">
            <input type="hidden" name="id" value="<% $TPLOBJ.formcontent.id %>">
            <input type="hidden" name="epage" value="<% $epage %>"> 
            <input type="hidden" name="tmsid" value="<% $GET.tmsid %>"> 
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="inlaytitle">Name</label>
                        <input id="inlaytitle" type="text" class="form-control" value="<% $TPLOBJ.description|hsc %>" name="FORM_TEMPLATE[description]">
                    </div><!-- /.form-group -->
                </div><!-- /.col-md-6 -->
                <div class="col-md-6">
                
                    <div class="form-group">
                        <label for="">{LBL_LANGUAGE}</label>
                        <% $ADMIN.langselect %>
                    </div><!-- /.form-group -->
                    
                    <div class="form-group">
                        <label for="">Implentierungs Code</label>
                        <p class="form-control-static"><code><% $TPLOBJ.blocktpl %></code></p>
                    </div><!-- /.form-group -->
                    
                    <div class="checkbox">
                        <label>
                            <input <% if ($TPLOBJ.approval==1) %>checked<%/if%> type="checkbox" name="FORM_TEMPLATE[approval]" value="1"> {LBL_INLAYPUBLISHED}
                        </label>
                    </div>
                    
                </div><!-- /.col-md-6 -->
            </div><!-- /.row -->

            <fieldset>  
                <legend>{LBL_CONTENT}</legend>
                <% $TPLOBJ.oeditor %>
            </fieldset> 
            <div class="form-feet">
                <a class="btn btn-default" href="<%$PHPSELF%>?epage=<%$epage%>&aktion=replicateland&id=<% $TPLOBJ.formcontent.tid %>&uselang=<% $GET.uselang %>">Inhalt auf alle Sprachen replizieren</a>
                <%$subbtn%>
            </div><!-- /.form-feet -->
        </form>

        <script>
            $( ".hplinks" ).change(function(e) {
                $('.hpurl').val($(this).val());
            });
        </script>
<%include file="cb.panel.footer.tpl"%>

    <% include file="inlay.connections.tpl" %>

<%/if%>

<!-- Modal: Neues Inlay -->
<div class="modal fade" id="meinModal" tabindex="-1">
    <form method="post" class="form" action="<%$PHPSELF%>" enctype="multipart/form-data">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Schließen</span></button>
                    <h4 class="modal-title">Neues Inlay anlegen</h4>
                </div><!-- /.modal-header -->
                <div class="modal-body">
                    
                    
                    <input type="hidden" name="epage" value="<% $epage %>"> 
                    <input type="hidden" name="tmsid" value="<% $GET.tmsid %>">
                    <input type="hidden" name="aktion" value="a_new">
                            
                    <div class="form-group">
                        <label for="titel">Inlay Name</label>
                        <input id="titel" type="text" class="form-control" value="<% $TPLOBJ.description|hsc %>" name="FORM[description]">
                    </div><!-- /.form-group -->
    
                </div><!-- /.modal-body -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
                    <% $subbtn %>
                </div><!-- /.modal-footer -->
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </form>
</div><!-- /.modal -->
<!-- /Modal: Neues Inlay -->