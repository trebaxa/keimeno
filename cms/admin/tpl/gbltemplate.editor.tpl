<div class="quick-menu text-right">
        <% if ($GET.id>1) %>
        <div class="btn-group">
            <a class="btn btn-secondary" href="javascript:void(0);" data-toggle="modal" data-target="#addgblpage" title="{LBLA_ADD}"><i class="fa fa-plus"></i></a>
            <%if ($GET.id>0 && $TPLOBJ.admin==0) %>
                    <a onclick="return confirm('Sind Sie sicher?')" class="btn btn-danger json-link" href="<%$eurl%>cmd=deltpljson&id=<%$GET.id%>"><i class="fa fa-trash"></i></a>
            <%/if%>            
        </div>
        <%/if%>
</div>


<!-- Modal ADDPAGE -->
<div class="modal fade" id="addgblpage" tabindex="-1" role="dialog" aria-labelledby="addpageLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    <form role="form" method="post" class="jsonform" action="<%$PHPSELF%>">
      <div class="modal-header">
        <h5 class="modal-title" id="addpageLabel">{LA_NEUEINHALTSSEITEANLEG}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <input type="hidden" name="cmd" value="add_gbltpl">
            <input type="hidden" name="epage" value="<%$epage%>">
            <input type="hidden" name="FORM[modident]" value="<%$TPLOBJ.modident%>">
            <div class="form-group">
                <label for="desc">{LBLA_DESCRIPTION}:</label>
                <input autofocus id="desc" type="text" class="form-control" name="FORM[description]" value="<% $FORM.description|hsc %>">
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <% $subbtn %>
      </div>
       </form>
    </div>
  </div>
</div>
    
      
<%if ($cmd=='edit' || $cmd=='load_gbltpl_ax') %>
<script>
    $('.changelanggbltpl').change(function(e) {
        std_load_gbltpl(<%$GET.id%>,$('.changelanggbltpl').val());
    });
</script>

<%include file="cb.panel.header.tpl" icon="fa-file-code-o" title=$TPLOBJ.description title_addon="[Page `$GET.id`]"%>

    <div class="tc-tabs-box" id="webmtabs">
        <ul class="nav" id="vertnav">
            <li class="active"><a class="tab-item-link ajax notloaded vertmenuclick" title="Inhalt" href="javascript:void(0);" data-layer="1" onClick="initEditor();">Inhalt</a></li>
            <li><a class="tab-item-link ajax notloaded vertmenuclick" title="Einstellung" href="javascript:void(0);" data-layer="0" >Einstellungen</a></li>
            <li><a class="tab-item-link ajax notloaded vertmenuclick" title="Inhalt" href="javascript:void(0);" data-layer="3" >Meta</a></li>
            <!--<li ><a class="tab-item-link ajax notloaded vertmenuclick" title="Mod Rewrite" href="javascript:void(0);" data-layer="2">Mod Rewrite</a></li>-->
            <li><a class="tab-item-link ajax notloaded vertmenuclick" title="Verwendung" href="javascript:void(0);" data-layer="4" >Verwendung</a></li>
            <li><a class="tab-item-link ajax notloaded vertmenuclick" title="Backup" href="javascript:void(0);" data-layer="5" onClick="simple_load('layer5','<%$PHPSELF%>?epage=<%$epage%>&cmd=loadbackups&tid=<% $GET.id %>');">Backup</a></li>
            <li><a class="tab-item-link ajax notloaded vertmenuclick" title="Org. Templates" href="javascript:void(0);" data-layer="6" onClick="simple_load('layer6','<%$PHPSELF%>?epage=<%$epage%>&cmd=show_org_tpl&tid=<% $GET.id %>');">Original Template</a></li>
        </ul>
        <div class="clearfix"></div>
    </div>

        <%if ($GBLPAGE.access.language==TRUE)%>

    <!-- LAYER0 //-->
    <div style="display:none" class="vertmenulayer" id="layer0">
        <form  class="jsonform form" method="post" action="<%$PHPSELF%>" enctype="multipart/form-data">
            <input type="hidden" name="tid" value="<% $GET.id %>">
            <input type="hidden" name="cmd" value="a_save">
            <input type="hidden" name="uselang" value="<% $GET.uselang%>">
            <input type="hidden" name="FORMCON[lang_id]" value="<% $GET.uselang %>">
            <input type="hidden" name="FORMCON[tid]" value="<% $GET.id %>">
            <input type="hidden" name="id" value="<% $TPLOBJ.formcontent.id %>">
            <input type="hidden" name="epage" value="<% $epage %>">
            <input type="hidden" name="tmsid" value="<% $GET.tmsid %>">
            <input type="hidden" name="configid" value="0">
               <div class="row">
                <div class="col-md-6">
                    <%include file="cb.panel.header.tpl" icon="fa-cog" title="Seiten Einstellungen"%>

                            <div class="form-group">
                                <label for="">Link</label>
                                <p class="form-control-static"><code><% $TPLOBJ.fixlink %></code></p>
                            </div><!-- /.form-group -->

                            <div class="form-group">
                                <label for="phpsrc">PHP Script:</label>
                                <input id="phpsrc"type="text" class="form-control" name="FORM_TEMPLATE[php]" value="<% $TPLOBJ.php|sthsc %>">
                            </div><!-- /.form-group -->

                            <div class="form-feet"><% $subbtn %></div>

                        <%include file="cb.panel.footer.tpl"%>
                    </div>
                    <div class="col-md-6">
                        <%include file="cb.panel.header.tpl" icon="fa-user" title="Zugriffsberechtigung"%>
                            <%if ($TPLOBJ.permboxes!="") %>
                                <div class="form-group">
                                    <%$TPLOBJ.permboxes%>
                                </div>
                            <%/if%>
                        <%include file="cb.panel.footer.tpl"%>
                    </div>
                </div>
                </form>
            </div><!-- .vertmenulayer #layer0 -->




<!-- LAYER1 //-->
<div style="display:visible" class="vertmenulayer" id="layer1">
    <div id="topofpage">

        <%include file="cb.panel.header.tpl" icon="fa-clone" title="System Template"%>

    <form class="jsonform form" method="post" action="<%$PHPSELF%>" enctype="multipart/form-data">
        <input type="hidden" name="tid" value="<% $GET.id %>">
        <input type="hidden" name="cmd" value="a_save">
        <input type="hidden" name="uselang" value="<% $GET.uselang%>">
        <%if ($gbl_config.nomultilang_systemtemplates==0) %>
            <input type="hidden" name="FORMCON[lang_id]" value="<% $GET.uselang %>">
        <%else%>
            <input type="hidden" name="FORMCON[lang_id]" value="<% $gbl_config.std_lang_id %>">
        <%/if%>
        <input type="hidden" name="FORMCON[tid]" value="<% $GET.id %>">
        <input type="hidden" name="id" value="<% $TPLOBJ.formcontent.id %>">
        <input type="hidden" name="epage" value="<% $epage %>">
        <input type="hidden" name="tmsid" value="<% $GET.tmsid %>">
        <input type="hidden" name="configid" value="1">

        <%if ($GET.id==0) %><input type="hidden" name="FORM_TEMPLATE[modident]" value="<% $GET.mod %>"><%/if%>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="gbltreetpltitle">Titel</label>
                    <input type="text" class="form-control" id="gbltreetpltitle" name="FORM_TEMPLATE[description]" value="<% $TPLOBJ.description|sthsc %>">
                </div><!-- /.form-group -->

                <%if ($gbl_config.nomultilang_systemtemplates==0) %>
                    <div class="form-group">
                        <label for="changelanggbltpl">{LBL_LANGUAGE}</label>
                        <select id="changelanggbltpl" class="form-control changelanggbltpl" name="FORMCON[lang_id]">
                            <%foreach from=$langselect item=row %>
                                <option <%if ($GET.uselang==$row.id) %>selected<%/if%> value="<%$row.id%>"><%$row.post_lang%></option>
                            <%/foreach%>
                        </select>
                    </div><!-- /.form-group -->
                <%/if%>

                <div class="form-group">
                    <label for="modident">App Connection</label>
                    <select id="modident" class="form-control" name="FORM_TEMPLATE[modident]">
                        <option <%if ($TPLOBJ.modident==$ml.mod_id) %>selected<%/if%> value="">{LA_GENERAL}</option>
                        <%foreach from=$mod_list item=ml %>
                            <%if ($ml.mod_allowed==TRUE) %>
                                <option <%if ($TPLOBJ.modident==$ml.mod_id) %>selected<%/if%> value="<%$ml.mod_id%>"><%$ml.mod_name%></option>
                            <%/if%>
                        <%/foreach%>
                    </select>
                </div><!-- /.form-group -->

            </div>
            <div class="col-md-6">
                <% include file="cb.radioswitch.tpl" label="Template Auswahl aktivieren" name="FORM_TEMPLATE[layout_group]" value=$TPLOBJ.layout_group %>
                <%if ($GET.id!=1 && $GET.id!=9670) %>
                    <% include file="cb.radioswitch.tpl" label="Framework Template" name="FORM_TEMPLATE[is_framework]" value=$TPLOBJ.is_framework %>
                <%/if%>

                <%if ($GET.id>0 && $TPLOBJ.is_framework==0) %>
                    <div class="row">
                        <div class="col-md-4">
                            Implementierung:
                        </div>
                        <div class="col-md-8 text-right">    
                            <code class="form-control-static"><% $TPLOBJ.smartytpl %></code>
                        </div>    
                    </div><!-- /.form-group -->
                <%/if%>
            


                <!--
                <%if ($gbl_config.nomultilang_systemtemplates==0) %>
                    <div class="form-group">
                        <label for="">Replizieren</label>
                        <a class="btn btn-secondary" href="<%$PHPSELF%>?epage=<%$epage%>&cmd=replicateland&id=<% $TPLOBJ.formcontent.tid %>&uselang=<% $GET.uselang %>">Inhalt auf alle Sprachen replizieren</a>
                    </div><!-- /.form-group -->
                <!--<%/if%>-->

  

                <%if ($GET.id>0 && $TPLOBJ.is_framework==1) %>
                    <div class="form-group">
                        <label for="">Admin GUI Framework</label>
                        <select class="form-control custom-select" name="FORM_TEMPLATE[gui_frame]">
                            <%foreach from=$TPLOBJ.guiframeworks item=row %>
                                <option <%if ($TPLOBJ.gui_frame==$row.fw_number) %>selected<%/if%> value="<%$row.fw_number%>">Framework <%$row.fw_number%></option>
                            <%/foreach%>
                        </select>
                    </div><!-- /.form-group -->
                <%/if%>
        </div>
       </div><!--row-->

                <div id="editor"></div><!-- /#editor -->
                <%$TPLOBJ.oeditor %>

                <div class="form-feet mt-lg">
                    <%if ($gbl_config.nomultilang_systemtemplates==0) %>
                        <a class="btn btn-secondary json-link" href="<%$PHPSELF%>?epage=<%$epage%>&cmd=replicateland&id=<% $TPLOBJ.formcontent.tid %>&uselang=<% $GET.uselang %>"><i class="fas fa-language" aria-hidden="true"></i> Inhalt auf alle Sprachen replizieren</a>
                    <%/if%>

                    <%$subbtn%>
                </div><!-- /.form-feet -->
            </form>
        <%include file="cb.panel.footer.tpl"%>

    </div><!-- #topofpage -->

    <%include file="gbltemplate.scriptatt.tpl"%>

    <div class="col-md-6">
        <% if (count($TPLINUSEINSIDE)>0) %>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th></th>
                    <th>Unterseiten</th>
                </tr>
            </thead>

            <% foreach from=$TPLINUSEINSIDE item=row %>
            <tr>
                <td><a title="<% $row.description|hsc %>" class="js-subtpl-click" data-tid="<%$row.tid%>" href="javascript:void(0)"><% $row.description|truncate:30 %></a></td>
                <td><% $row.label%></td>
            </tr>
            <%/foreach%>

        </table>
        <%/if%>
    <script>
       $('.js-subtpl-click').unbind('click');
       $('.js-subtpl-click').css('cursor','pointer');
       $('.js-subtpl-click').click(function(event) {
            event.preventDefault();
            $('.tooltip').tooltip('destroy');
            simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=gbltemplates.inc&id='+$(this).data('tid')+'&uselang=1&cmd=load_gbltpl_ax',true);
            $('#gbltpltreeul').jstree("deselect_all");
            $('#gbltpltreeul').jstree(true)
                .select_node('gbltreenode-'+$(this).data('tid'));
            });

    </script>
    </div><!-- .col-md-6 -->

</div><!-- .vertmenulayer #layer1 -->




<!-- LAYER3 //-->
<div style="display:none" class="vertmenulayer" id="layer3">

    <%include file="cb.panel.header.tpl" title="Meta-Beschreibungen"%>

        <form class="jsonform form" method="post" action="<%$PHPSELF%>" role="form" enctype="multipart/form-data">
            <input type="hidden" name="tid" value="<% $GET.id %>">
            <input type="hidden" name="cmd" value="a_save">
            <input type="hidden" name="uselang" value="<% $GET.uselang%>">
            <input type="hidden" name="FORMCON[lang_id]" value="<% $GET.uselang %>">
            <input type="hidden" name="FORMCON[tid]" value="<% $GET.id %>">
            <input type="hidden" name="id" value="<% $TPLOBJ.formcontent.id %>">
            <input type="hidden" name="epage" value="<% $epage %>">
            <input type="hidden" name="tmsid" value="<% $GET.tmsid %>">
            <input type="hidden" name="configid" value="3">

            <div class="form-body">
                <div class="row">
                    <div class="col-md-5">

                        <div class="form-group">
                            <label for="mtitle">Meta-Title</label>
                            <div class="input-group">
                                <input class="form-control" id="mtitle" value="<% $TPLOBJ.formcontent.meta_title %>" name="FORMCON[meta_title]">
                                <span class="input-group-btn"><button class="btn btn-secondary" type="button" onClick="GetRequest2Value('mtitle','genmetatitle','run','&uselang=<%$GET.uselang%>&epage=<%$epage%>&id=<% $TPLOBJ.id%>&conid=<% $TPLOBJ.formcontent.meta_title %>'); return false;"><i class="fa fa-refresh"></i> generieren</button></span>
                            </div><!-- /.input-group -->
                            <span class="help-block">Der Meta-Titel wird im Browserfester dargestellt.</span>
                        </div><!-- /.form-group -->

                    </div><!-- /.col-md-5 -->
                    <div class="col-md-7">

                        <div class="form-group">
                            <label for="mkeys">Meta-Keywords</label>
                            <div class="input-group">
                                <input id="mkeys"  type="text" class="form-control" name="FORMCON[meta_keywords]" value="<% $TPLOBJ.formcontent.meta_keywords|hsc %>">
                                <span class="input-group-btn"><button class="btn btn-secondary" type="button" onClick="GetRequest2Value('mkeys','genkeys','run','&uselang=<%$GET.uselang%>&epage=<%$epage%>&id=<% $TPLOBJ.id%>&conid=<% $TPLOBJ.formcontent.id %>'); return false;"><i class="fa fa-refresh"></i> generieren</button></span>
                            </div><!-- /.input-group -->
                            <span class="help-block">Der Meta-Titel wird im Browserfester dargestellt.</span>
                        </div><!-- /.form-group -->

                    </div><!-- /.col-md-7 -->
                </div><!-- /.row -->
                <div class="row">
                    <div class="col-md-12"><div class="form-group">
                        <label for="mdesc">Meta-Description</label>
                        <textarea data-theme="<%$gbl_config.ace_theme%>" class="form-control" id="mdesc" name="FORMCON[meta_desc]" rows="3" cols="90" onKeyPress="return taLimit(this,<% $gbl_config.metadesc_count%>,event)" onKeyUp="return taCount(this,'myCounter',<% $gbl_config.metadesc_count%>)"><% $TPLOBJ.formcontent.meta_desc|hsc %></textarea>
                        <span class="help-block">You have <b><span id="myCounter">254</span></b> characters remaining for your description...</span>
                        <!--<a href="javascript:void(0);" onClick="GetRequest2Value('mdesc','genmeta','run','&uselang=<%$GET.uselang%>&epage=<%$epage%>&id=<% $TPLOBJ.id%>&conid=<% $TPLOBJ.formcontent.id %>'); return false;">Meta Description generieren</a>-->
                    </div><!-- /.form-group --></div>
                </div>
                <!-- /.row -->
            </div><!-- /.form-body -->

            <div class="form-feet">
                <%$subbtn %>
            </div>

        </form>
    <%include file="cb.panel.footer.tpl"%>

</div><!-- .vertmenulayer #layer3 -->





<!-- LAYER4 //-->
<div style="display:none" class="vertmenulayer" id="layer4">

    <%include file="cb.panel.header.tpl" title="Verwendung"%>

        <%if (count($TPLINUSE)>0) %>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Titel</th>
                    <th>Einsatz als</th>
                    <th>Lng</th>
                    <th></th>
                </tr>
            </thead>

            <% foreach from=$TPLINUSE item=row %>
                <tr>
                    <td><a href="<% $row.edit_link %>"><% $row.description %></a></td>
                    <td><% $row.label%></td>
                    <td><img src="<% $row.thumb %>" ></td>
                    <td class="text-right"><% $row.edit_icon%></td>
                </tr>
            <%/foreach%>
        </table>
        <%else%>
        <div class="alert alert-info"><p class="text-info">Keine Implementierung in anderen Tempates gefunden.</p></div>
        <%/if%>
    <%include file="cb.panel.footer.tpl"%>

</div><!-- .vertmenulayer #layer4 -->

<!-- LAYER5 //-->
<div style="display:none" class="vertmenulayer" id="layer5"></div><!-- /.vertmenulayer #layer5 -->

<!-- LAYER6 //-->
<div style="display:visible" class="vertmenulayer" id="layer6"></div><!-- /.vertmenulayer #layer6 -->


<!-- LAYER2 //-->
<div style="display:none" class="vertmenulayer" id="layer2">

    <form class="stdform form-inline"  method="post" action="<%$PHPSELF%>" enctype="multipart/form-data">
        <input type="hidden" name="tid" value="<% $GET.id %>">
        <input type="hidden" name="cmd" value="save_hta">
        <input type="hidden" name="epage" value="<% $epage %>">
        <input type="hidden" name="id" value="<% $HTAF.id %>">
        <input type="hidden" name="HTAF[hta_tid]" value="<% $GET.id %>">
        <input type="hidden" name="HTAF[hta_description]" value="<% $TPLOBJ.description|sthsc %>">
        <input type="hidden" name="tmsid" value="<% $GET.tmsid %>">
        <input type="hidden" name="configid" value="2">

        <fieldset>

            <legend>URL Darstellung</legend>
<table>
 <tr>
    <td>Ident/Prefix:</td>
    <td><input type="text" class="form-control" size="30" maxlength="60" name="HTAF[hta_prefix]" value="<% $HTAF.hta_prefix %>"></td>
 </tr>
 <tr>
    <td>Fileextention:</td>
    <td>.<input type="text" class="form-control" size="5" maxlength="4" name="HTAF[hta_fileext]" value="<% $HTAF.hta_fileext %>"></td>
 </tr>

 <tr>
    <td>URL Request:</td>
    <td>
        <input type="text" class="form-control" size="31" maxlength="100" name="HTAF[hta_add]" value="<% $HTAF.hta_add %>">
        <br><span class="small">z.B.: &cmd=load_something</span>
    </td>
 </tr>


 <tr>
    <td>Es werden POST/GET Variablen empfangen:</td>
    <td><input type="checkbox" name="HTAF[hta_allowaddtags]" value="1"></td>
 </tr>

 <tr>
    <td>Link:</td>
    <td><a target="_ht" href="../<% $HTAF.link %>"><% $HTAF.link %></a></td>
 </tr>

<tr>
    <td colspan="2">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th>KEY/TAG</th>
                    <th>ID</th>
                    <th>TAG</th>
                    <th>NOT USED</th>
                </tr>
            </thead>

            <% foreach from=$HTAF.vars key=li item=htavar %>
                <% assign var=test value=$htavar.li %>
                 <tr>
                    <td>PHP Url Tag (<%$li%>):</td>
                    <td>
                        <select class="form-control custom-select" name="HTAF[hta_delimeter<%$li%>]">
                            <% foreach from=$HTAF.htad item=trenner %><option <% if ($htavar.delimiter==$trenner) %> selected <%/if%> value="<% $trenner %>"><% $trenner %></option><%/foreach%>
                        </select>(.*)
                    </td>
                    <td>
                        <input type="text" class="form-control" id="htavar<%$li%>" size="10" name="HTAF[hta_var<%$li%>]" value="<% $htavar.vars %>">
                        <script>
                            $('#htavar<%$li%>').hide();
                            <% if ($htavar.vartype==1) %>$('#htavar<%$li%>').show();<%/if%>
                        </script>
                    </td>
                    <td class="text-center"><input onClick="$('#htavar<%$li%>').fadeIn();" <% if ($htavar.vartype==1) %> checked <%/if%> type="radio" name="HTAF[hta_vartype<%$li%>]" value="1"></td>
                    <td class="text-center"><input onClick="$('#htavar<%$li%>').fadeOut();" <% if ($htavar.vartype==2) %> checked <%/if%> type="radio" name="HTAF[hta_vartype<%$li%>]" value="2"></td>
                    <td class="text-center"><input onClick="$('#htavar<%$li%>').fadeOut();"<% if ($htavar.vartype==3 || $htavar.vartype==0) %> checked <%/if%> type="radio" name="HTAF[hta_vartype<%$li%>]" value="3"></td>
                 </tr>
            <%/foreach%>
        </table>
    </td>
</tr>

</table>
<div class="subright"><% $subbtn %></div>
    </fieldset>

</form>
</div><!-- /#layer2 /.vermenulayer -->

<%include file="cb.panel.footer.tpl"%>

    <script>fwstart();</script>
    <%else %>
        <%include file="no_permissions.admin.tpl" %>
    <%/if%>
<%/if%>
