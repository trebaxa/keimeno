<% if (count($WEBSITE.content_table)>0) %>

    <div class="x_panel">
        <div class="x_title">
            <h2><a class="" data-toggle="modal" data-target="#meinModal" title="Inhalt on Top hinzuf&uuml;gen" href="javascript:void(0);" onclick="show_select_type('<% $WEBSITE.TMCID %>','0','<%$hotspot.number%>')"><i class="fa fa-plus-circle"><!----></i></a> <%$hotspot.fw_pos|replace:'fw_':''%></h2>
         <%if ($WEBSITE.TMTID>1) %>
            <ul class="nav navbar-right panel_toolbox panel_toolbox_auto">
                <%assign var="hotnum" value=$hotspot.number%>
                <% if ($WEBSITE.content_parent.$hotnum==true) %>
                    <li><a class="lockit" href="javascript:void(0);" rel='{"tm_cid":"<% $WEBSITE.TMCID %>","tm_pos":"<%$hotspot.number%>","status":"0"}'><i class="fa fa-lock"><!----></i></a></li>
                <%else%>
                    <li><a class="lockit" href="javascript:void(0);" rel='{"tm_cid":"<% $WEBSITE.TMCID %>","tm_pos":"<%$hotspot.number%>","status":"1"}'><i class="fa fa-unlock"><!----></i></a></li>                   
                <%/if%> 
            </ul>            
          <%/if%>
          <div class="clearfix"></div>  
        </div>
        <div class="x_content" id="sortable" class="ui-sortable">
        

    <%foreach from=$WEBSITE.content_table item=hotspots %>
        <%foreach from=$hotspots key=hkey item=row name=webconloop%>
            <%if ($row.tm_pos==$hotspot.number) %>
            
                <div id="<%$row.TMID%>" class="tplcont<% if ($row.heredity==true) %> heredity<%/if%> ui-state-default ui-sortable-handle">
                    <div class="tdright axchdtd <% if ($row.tm_approved==0) %>notapproved<%/if%>">
                        <div class="axchd">
                            <div class="pull-left sort-col <% if ($row.heredity==true) %>hotspot-disbale-<%$hotspot.number%><%/if%>">                                         
                                <i class="fa fa-sort" aria-hidden="true"></i>                                 
                            </div>                        
                            <%if ($row.heredity==false) %>
                                <div class="btn-group pull-right">
                                    <a class="btn btn-default" title="{LA_NEUERBEITRAGNACHDIESE}" href="javascript:void(0);" onclick="show_select_type('<%$row.tm_cid%>','<%$row.TMID%>','<%$row.tm_pos%>')"><i class="fa fa-plus-circle"><!----></i></a>                        
                                    <a class="btn btn-default js-editor-plug-click" href="javascript:void(0);" data-tmcid="<% $row.tm_cid %>" data-tmid="<%$row.TMID%>"><i class="fa fa-pencil-square-o"><!----></i></a>
                                    <%foreach from=$row.icons key=iconkey item=picon %><% $picon %><%/foreach%>
                                    <a title="{LBL_DELETE}" href="javascript:void(0);" class="btn btn-danger js-block_delete" data-tmid="<%$row.TMID%>" id="del-<%$row.TMID%>"><i class="fa fa-trash"><!----></i></a>
                                    <a class="btn btn-default js-editor-plug-close-click" href="javascript:void(0);" style="display:none" data-tmcid="<% $row.tm_cid %>" data-tmid="<%$row.TMID%>"><i class="fa fa-times"><!----></i></a>                                  
                                </div><!-- /.btn-group -->
                                <div class="pull-left"><h4  class="js-editor-plug-click" data-tmcid="<% $row.tm_cid %>" data-tmid="<%$row.TMID%>">
                                  <% if ($row.tm_hint!='') %>
                                        <%$row.tm_hint%>
                                <%/if%>                                  
                                    
                                     <small ><% if ($row.tm_type=='P') %>
                                         <%$row.tm_plugname|st|truncate:300%>   
                                    <%/if%>
                                    <% if ($row.tm_type=='W') %>
                                        HTML Text
                                     <%/if%>      
                                    <% if ($row.tm_type=='C') %>
                                        Script Code
                                     <%/if%> 
                                    <% if ($row.tm_type=='S') %>
                                        System Template - <%$row.tm_pluginfo%>
                                     <%/if%></small>
                                      </h4>
                                </div>
                            <%/if%>
                            

                        </div>
                    </div><!-- /.tdright .axchdtd -->
                    
                    <div class="ctable-inner">
                      <div class="row ctable-row">
                        <div class="col-md-6">
                                             
                       </div>
                        <div class="col-md-6 text-right">
                             <% if ($row.tm_type=='P') %>
                               <div><%$row.tm_pluginfo|st|truncate:300%></div>
                            <%else%>
                                <div class="text-muted"><%$row.tm_content|st|truncate:300%></div>
                            <%/if%>
                            <% if ($row.tm_type=='S') %>
                                <div class="text-muted">System Template: <%$row.tm_pluginfo%></div>
                            <%/if%>
                        </div>
                      </div>
                       
                      <div id="js-content-editor-<% $row.tm_cid %><%$row.TMID%>" class="js-content-editor content-editor" style="display:none">
                        
                      </div>
                      
                    </div><!-- /.ctable-inner -->
                </div>
            
            <%/if%>
        
        <%/foreach%>
    
    <%/foreach%>
   </div>        
</div> 
    <% if ($GET.axcall==1) %><script>set_ajaxapprove_icons();</script><%/if%>

<%/if%>

<script>
$(document).ready(function() {
    $( "#sortable" ).sortable({
        placeholder: "highlight",
         cursor: 'move',
        update: function(event, ui) {
          var ids = $(this).sortable('toArray').toString();
          jsonexec('<%$eurl%>cmd=sort_cm_table&ids='+ids);
       }
    });

    $('.js-editor-plug-close-click').unbind('click');
    $('.js-editor-plug-close-click').click(function(e) {
        e.preventDefault();
        var tm_cid = $(this).data('tmcid');
        var tmid = $(this).data('tmid');
        remove_all_tinymce();  
        $('.js-content-editor').hide();
        $('.js-content-editor-panel').remove();
        $(this).hide();
        $('.ctable-row').show();
        $( "#sortable" ).sortable( "enable" );
    });
    
    $('.js-editor-plug-click').click(function(e) {
        e.preventDefault();
        var tm_cid = $(this).data('tmcid');
        var tmid = $(this).data('tmid');
        remove_all_tinymce();  
        $('.js-content-editor').hide();
        $('.js-content-editor-panel').remove();
        simple_load_sync('js-content-editor-'+tm_cid+tmid,'<%$PHPSELF%>?epage=<%$epage%>&id='+tmid+'&cmd=axshow_editor&tm_cid='+tm_cid);
        $('#js-content-editor-'+tm_cid+tmid).slideDown();
        $('.ctable-row').show();
        $('#js-content-editor-'+tm_cid+tmid).prev('.row').hide();
        $('.js-editor-plug-close-click').hide();
        $(this).closest('.axchd').find('.js-editor-plug-close-click').fadeIn();
        scroll_content_table('js-content-editor-'+tm_cid+tmid);
        $( "#sortable" ).sortable( "disable" );
    });
});    
</script>