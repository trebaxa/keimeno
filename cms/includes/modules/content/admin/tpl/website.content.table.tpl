<% if (count($WEBSITE.content_table)>0) %>

    <div class="x_panel">
        <div class="x_title">
          <div class="input-group">
              <div class="input-group-prepend">
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#meinModal" onclick="show_select_type('<% $WEBSITE.TMCID %>','0','<%$hotspot.number%>')">
                  <i class="fas fa-plus-circle"></i>
                </button>
              </div>
            <div class="input-group-append">
            <span class="input-group-text"><%$hotspot.fw_pos|replace:'fw_':''%></span>
            </div>
          </div>
        
            <%if ($WEBSITE.TMTID>1) %>
                <%assign var="hotnum" value=$hotspot.number%>
                <% if ($WEBSITE.content_parent.$hotnum==true) %>
                <button type="button" class="btn btn-secondary lockit" id="js-mainframe-lock" rel='{"tm_cid":"<% $WEBSITE.TMCID %>","tm_pos":"<%$hotspot.number%>","status":"0"}'>
                  <i class="fas fa-lock"></i>
                </button>
                <%else%>
                <button type="button" class="btn btn-secondary lockit" id="js-mainframe-lock" rel='{"tm_cid":"<% $WEBSITE.TMCID %>","tm_pos":"<%$hotspot.number%>","status":"1"}'>
                  <i class="fas fa-unlock"></i>
                </button>
                <%/if%>

          <%/if%>
          <div class="clearfix"></div>
        </div>
        <div class="x_content" id="sortable" class="ui-sortable">


    <%foreach from=$WEBSITE.content_table item=hotspots %>
        <%foreach from=$hotspots key=hkey item=row name=webconloop%>
            <%if ($row.tm_pos==$hotspot.number) %>

      <div class="tplcont<% if ($row.heredity==true) %> heredity<%/if%> ui-state-default ui-sortable-handle" id="<%$row.TMID%>">
          <div class="tplcont-header <% if ($row.tm_approved==0) %>notapproved<%/if%>">
            <div class="meta">
              <div class="sortarea sort-col <% if ($row.heredity==true) %>hotspot-disbale-<%$hotspot.number%><%/if%>">
                <i class="fas fa-sort"></i>
              </div>
              <%if ($row.heredity==false) %>
              <h4 class="js-editor-plug-click" data-tmcid="<% $row.tm_cid %>" data-tmid="<%$row.TMID%>">
                <% if ($row.tm_hint!='') %>
                  <%$row.tm_hint%>
                <%/if%>
                <span class="badge">
              <% if ($row.tm_type=='P') %>
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
                <%/if%>
                </span>
              </h4>
              <% /if %>
            </div>
            <div class="interactions">
              <%if ($row.heredity==false) %>
              <div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                <button title="{LA_NEUERBEITRAGNACHDIESE}" type="button" class="btn btn-secondary" onclick="show_select_type('<%$row.tm_cid%>','<%$row.TMID%>','<%$row.tm_pos%>')"><i class="fas fa-plus-circle"></i></button>
                <button type="button" class="btn btn-secondary js-editor-plug-click" data-tmcid="<% $row.tm_cid %>" data-tmid="<%$row.TMID%>"><i class="fas fa-edit"></i></button>
                <%foreach from=$row.icons key=iconkey item=picon %><% $picon %><%/foreach%>
                <button type="button" title="{LBL_DELETE}" class="btn btn-danger js-block_delete" data-tmid="<%$row.TMID%>" id="del-<%$row.TMID%>"><i class="fas fa-trash-alt"></i></button>
                <a id="js-close-cm-<% $row.tm_cid %><%$row.TMID%>" class="btn btn-secondary js-editor-plug-close-click" href="javascript:void(0);" style="display:none" data-tmcid="<% $row.tm_cid %>" data-tmid="<%$row.TMID%>"><i class="fas fa-times"><!----></i></a>
              </div>
              <% /if %>
            </div>
          </div>
          <div id="js-content-editor-<% $row.tm_cid %><%$row.TMID%>" class="js-content-editor content-editor" style="display:none">

      </div>

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
        $('.js-content-editor').slideUp();
        $('.js-content-editor-panel').remove();
        $(this).hide();
        $( "#sortable" ).sortable( "enable" );
    });
    
    $('.js-editor-plug-click').css('cursor', 'pointer');
    $('.js-editor-plug-click').unbind('click');
    $('.js-editor-plug-click').click(function(e) {
        e.preventDefault();
        var tm_cid = $(this).data('tmcid');
        var tmid = $(this).data('tmid');
        remove_all_tinymce();
        var $t = $('#js-content-editor-'+tm_cid+tmid);       
        if ($t.is(':visible')) {
            $('#js-close-cm-'+tm_cid+tmid).trigger('click');        
        } else {
            $('.js-content-editor').hide();
            $('.js-content-editor-panel').remove();
            var url = '<%$eurl%>&id='+tmid+'&cmd=axshow_editor&tm_cid='+tm_cid+'&a='+Math.random(1,10000);
            simple_load_sync('js-content-editor-'+tm_cid+tmid,url);
            $t.slideDown();
            $t.prev('.row').hide();
            $('.js-editor-plug-close-click').hide();
            $(this).closest('.btn-group').find('.js-editor-plug-close-click').fadeIn();
            $('#js-close-cm-'+tm_cid+tmid).fadeIn();
            scroll_content_table('js-content-editor-'+tm_cid+tmid);
            $( "#sortable" ).sortable( "disable" );
        }
    });
});
</script>
