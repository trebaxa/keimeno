        <div class="page-header">
            <h1><i class="fa fa-home"></i> Websitemanager</h1>
        </div><!-- /.page-header -->

<div class="btn-group">
    <a class="btn btn-default" href="javascript:void(0);" data-toggle="modal" data-target="#addpage" title="{LBL_NEW}">{LBLA_NEW_SETUP}</a>
    <a class="btn btn-default" href="javascript:void(0);" data-toggle="modal" data-target="#dfsearchre">{LBLA_SEARCHREPLACE}</a>
    <a class="btn btn-default" href="javascript:void(0);" data-toggle="modal" data-target="#csearch">{LBLA_SEARCH}</a>
</div><!-- /.btn-group -->

        <%if (count($WLIST.toplevel_links)>0) %>
            <div class="tc-tabs-box" id="webmtabs">
                <ul class="nav nav-tabs bar_tabs right" role="tablist">
                    <% foreach from=$WEBSITE.toplevel_tabs item=row %>
                        <% if ($row.approval==1) %>
                        <li <%if ($WEBSITE.seltopl==$row.id) %>class="active"<%/if%>><a <%if ($WEBSITE.seltopl==$row.id) %>class="active"<%/if%> href="<%$PHPSELF%>?epage=<%$epage%>&aktion=&toplevel=<% $row.id %>"><% $row.description %></a></li>
                        <%/if%>
                    <%/foreach%>
                 </ul>
            </div><!-- /#tc-tabs-box -->
        <%/if%>

        <form id="pagetabform" class="form-inline" action="<%$PHPSELF%>" method="post">
            <input type="hidden" value="<% $GET.toplevel %>" name="toplevel">
            <input type="hidden" value="<% $GET.tmsid %>" name="tmsid">
            <input type="hidden" value="<%$epage%>" name="epage">
            <div id="treetable" class="tab-content">
                <% include file="website.pagetable.tpl"%>
            </div><!-- /.treetable -->
            <div class="form-feet"><% $subbtn %></div><!-- /.form-feet -->
        </form><!-- /#pagetabform /.form-inline -->

        <div id="fix_box">
            <ul>
                <li><a class="btn btn-default" href="<% $PHPSELF %>?epage=<%$epage%>&aktion=rootact" title="Alle Root Trees f&uuml;r 'TOPLEVEL' aktivieren"><i class="fa fa-bolt"><!----></i></a></li>
                <li><a class="btn btn-default" href="<% $PHPSELF %>?epage=<%$epage%>&aktion=setallperm" title="Alle Seiten &ouml;ffentlich machen"><i class="fa fa-check-circle fa-green"><!----></i></a></li>
                <li><a class="btn btn-default" href="<% $PHPSELF %>?epage=<%$epage%>&cmd=writealltpls" title="Alle Seiten neu erzeugen"><i class="fa fa-recycle fa-green"><!----></i></a></li>
                <li><a class="btn btn-default" href="javascript:void(0);" onclick="show_all_visi();" title="Alle sichtbaren Seiten anzeigen"><i class="fa fa-eye"><!----></i></a></li>
            </ul>
        </div>
    
        <script>
        <% if ($GET.starttree>0) %>
            loadtree(<%$GET.toplevel%>,<%$GET.starttree%>);
        <%else%>
            loadtree(<%$GET.toplevel%>);
        <%/if%>    
            function loadtree(toplevelid,starttree) {
                simple_load('treetable','<%$PHPSELF%>?epage=<%$epage%>&toplevel='+toplevelid+'&starttree=' +starttree+'&cmd=loadtreepages');
            }
            
            function pagestab_response(responseText, statusText, xhr, $form) {
                show_saved_msg();
            }
            
            var poptions = {
                target: '#treetable',   
                type: 'POST',
                forceSync: true,
                success: pagestab_response
            };
            
            $('#pagetabform').submit(function() {
                $(this).ajaxSubmit(poptions);
                return false;
            });
            
            function mup(id,starttree) {
                 simple_load('treetable','<%$PHPSELF%>?epage=<%$epage%>&cmd=pmoveup&id='+id+'&starttree='+starttree+'&toplevel=<%$GET.toplevel%>');
            }
            
            function mdown(id,starttree) {
                simple_load('treetable','<%$PHPSELF%>?epage=<%$epage%>&cmd=pmovedown&id='+id+'&starttree='+starttree+'&toplevel=<%$GET.toplevel%>');
            }
            
            function show_all_visi() {
               scrollToAnchor('anchortop');
               simple_load('webpagemanager','<%$PHPSELF%>?epage=<%$epage%>&cmd=search&show_active=1&id=<% $TPLOBJ.formcontent.tid %>&uselang=<% $GET.uselang %>');  
            }
        </script>

        <div class="row">

            <div class="col-md-6">
                <h3>Homepage Inhalte auf andere Sprachen replizieren</h3>
                <p class="alert alert-info">Ausgew&auml;hlte Sprache auf alle anderen Sprachen &uuml;bertragen. Bestehende Homepage Inhalte werden 
                durch die ausgew&auml;hlte Sprache &uuml;berschrieben.</p>
                
                <form class="stdform" method="POST" action="<%$PHPSELF%>">
                    <input type="hidden" name="cmd" value="replicatealllang">
                    <input type="hidden" name="epage" value="<%$epage%>">
                     <div class="form-group">   
                        <label>Homepage Inhalte &uuml;berschreiben mit Inhalten aus Sprache:</label>
                        <div class="input-group">
                            <select name="langid" class="form-control">
                                <%foreach from=$WEBSITE.langselect item=lang %>
                                    <option value="<%$lang.id%>"><%$lang.post_lang%></option>
                                <%/foreach%>
                            </select>
                            <span class="input-group-btn"><%$btngo%></span>
                        </div>
                    </div>
                    
                </form><!-- /.stdform .form-inline -->

            </div><!-- /.col-md-6 -->
            <div class="col-md-6">
              <!--  <legend>Aufgaben</legend>
                <a href="<% $PHPSELF %>?epage=<%$epage%>&aktion=rootact">Alle Root Trees f&uuml;r "TOPLEVEL" aktivieren</a><br>
                <a href="<% $PHPSELF %>?epage=<%$epage%>&aktion=setallperm">Alle Seiten &ouml;ffentlich machen</a><br>
                <a href="<% $PHPSELF %>?epage=<%$epage%>&cmd=writealltpls">Alle Seiten neu erzeugen</a><br>
                <a href="javascript:void(0);" onclick="show_all_visi();">Alle sichtbaren Seiten anzeigen</a>
                -->
            </div><!-- /.col-md-6 -->            
        </div><!-- /.row -->
       