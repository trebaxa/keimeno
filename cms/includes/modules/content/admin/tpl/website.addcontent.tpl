
<%foreach from=$WEBSITE.hotspots item=hotspot%>
    <% if ($hotspot.fw_pos=='fw_footer') %>
        <div class="clearfix"></div>
    <%/if%>
    <div id="tplcontent-<%$hotspot.number%>" class="spot <%$hotspot.fw_pos%>">
        <% include file="website.content.table.tpl" %>
    </div>
    <%assign var="hotnum" value=$hotspot.number%>
    <% if ($WEBSITE.content_parent.$hotnum==true) %>
        <script>
            $('.hotspot-disbale-<%$hotspot.number%>').html('');
        </script>
    <%/if%>
<%/foreach%>
<div class="clearfix"></div>
<script>
    if ($('.fw_right').length==0 && $('.fw_left').length==0) {
        $('.fw_main').css('width','100%');
    }
</script>


<!-- Modal contselect-->
<div class="modal fade" id="contselect" tabindex="-1" role="dialog" aria-labelledby="contselectLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="contselectLabel">{LA_INHALTHINZUFGEN}</h4>
      </div>
      <div class="modal-body">
        <div class="row">
        <% foreach from=$WEBSITE.first_plugs item=row name=plugloop %>
            <div class="col-md-6">
             <button type="button" class="btn-select-click btn btn-default  btn-block" data-tmtype="<%$row.tm_type%>" data-pluginid="<%$row.id%>" data-modident="<%$row.modident%>"><i class="fa fa-code"><!----></i> <%$row.plug_name%></button>
            </div>
        <%/foreach%>
        </div>
    <hr>
    <h3>Plugins</h3>
    <div class="row">
        <% foreach from=$WEBSITE.plugins item=row %>
                <div class="col-md-6">
                     <button type="button" class="btn-select-click btn btn-default  btn-block" data-tmtype="<%$row.tm_type%>" data-pluginid="<%$row.id%>" data-modident="<%$row.modident%>"><i class="fa fa-puzzle-piece"><!----></i> <%$row.plug_name%></button>
                </div>
        <%/foreach%>
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
    $(".btn-select-click").click(function (event) {
        event.preventDefault();
        $('#contselect').modal('hide');
        add_show_box_tpl('<%$eurl%>tid=<% $REQUEST.id %>&langid=<% $WEBSITE.langid%>&tm_plugid='+$(this).data('pluginid')+'&tm_modident='+$(this).data('modident')+'&tm_type='+$(this).data('tmtype')+'&tm_cid='+selectd_tm_cid+'&cmd=axshow_editor&after='+after_id+'&tm_pos='+selectd_tm_pos, '{LA_INHALTHINZUFGEN}');     
    });
    
    $(".lockit").click(function () {
     var obj = jQuery.parseJSON($(this).attr('rel'));
     simple_load('tplcontent','<%$eurl%>cmd=setheredity&tm_cid=<% $WEBSITE.TMCID %>&tm_pos='+obj.tm_pos+'&status='+obj.status);
    });
    
    var selectd_tm_cid = 0;
    var selectd_tm_pos = 0;
    var after_id = 0;
    function show_select_type(tm_cid,after,tm_pos) {
        selectd_tm_cid = tm_cid;
        selectd_tm_pos = tm_pos;
        after_id = after;    
        $('#contselect').modal('show');
        return false;
    }
    
    $(".tplcont").hover(
     function () {
        $(this).find('.axchdtd').fadeTo(300,1);
     },
     function () {
        $(this).find('.axchdtd').fadeTo(300,0.3);
     }
    );
    
    $(".js-block_delete").click(function () {
       var valid = confirm('{LBLA_CONFIRM}');
       if (valid==true) {
        jsonexec('<%$eurl%>cmd=axdelcon&id=' + $(this).data('tmid'), true);
        $(this).closest('.tplcont').fadeTo(400, 0, function () { 
            $(this).remove();
        });
     
        return false;
        }
    });
</script>
