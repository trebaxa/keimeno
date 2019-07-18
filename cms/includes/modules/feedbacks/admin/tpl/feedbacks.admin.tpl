<div class="page-header"><h1><i class="fa fa-comment-o"><!----></i> Testimonials</h1></div>

    <div class="btn-group">
        <a class="btn btn-primary" href="javascript:void(0);" data-toggle="modal" data-target="#addfeed"><i class="fa fa-plus"></i> Neu anlegen</a>
        <a class="btn btn-secondary ajax-link" href="<%$eurl%>"><i class="fa fa-table"></i> All anzeigen</a>
    </div>
    

<% if ($cmd=='') %>

    
    <form action="<%$PHPSELF%>" class="form-inline" method="post">
        <table class="table table-striped table-hover" id="feedback-table">
            <thead>
                <tr>
                    <th>{LBL_OPTIONS}</th>
                    <th>{LBL_WRITTENDATE}</th>
                    <th>{LBL_FROM}</th>
                </tr>
            </thead>
            
            <% foreach from=$FEEDB.items item=row %>
                <tr>
                    <td><div class="btn-group"><% foreach from=$row.icons item=picon %><% $picon %><%/foreach%></div></td>
                    <td><% $row.datum%></td>
                    <td><% $row.kname%></td>
                </tr>
            <%/foreach%>
        </table>
        <%* Tabellen Sortierungs Script *%>
        <%assign var=tablesortid value="feedback-table" scope="global"%>
        <%include file="table.sorting.script.tpl"%>   
       
        <input type="hidden" name="epage" value="<%$epage%>">
        <input type="hidden" value="a_msave" name="aktion"><%$subbtn%>
    </form>

<%/if%> 


<% if ($cmd=='edit') %>

<form method="post" action="<%$PHPSELF%>" class="jsonform" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<%$GET.id%>">
    <input type="hidden" name="epage" value="<%$epage%>">
    <div class="form-group">
        <label>{LBL_WRITTENDATE}:</label>
        <%$FEEDB.form.date_time%>
    </div>
    <div class="row">
        <div class="form-group col-md-6">
            <label>{LBL_FROM}:</label>
            <input required="" name="FORM[kname]" type="text" class="form-control"  size="30" value="<%$FEEDB.form.kname%>">
        </div>
        <div class="form-group col-md-6">
            <label>Email:</label>
            <input name="FORM[email]" type="email" class="form-control" size="30" value="<%$FEEDB.form.email%>">
        </div>
    </div>
    <div class="row">
        <div class="form-group col-md-6">
            <label>Vom (dd.mm.YYYY):</label>
            <input required name="FORM[time_int]" type="date" class="form-control" size="10" value="<%$FEEDB.form.date%>">
        </div>
        <div class="form-group col-md-6">
            <label>Kunden Zuordnung</label>
            <input data-target="feedbcustsearch" data-epage="<%$epage%>" data-cmd="searchcustomer" placeholder="{LBLA_CUSTOMER}" autocomplete="off" type="text" class="form-control" value=""  size="6" class="form-control live_search">
            <input type="hidden" name="FORM[kid]" value="" id="feedbkid">     
        </div>
     </div>   
     
    
    <div class="form-group">
        <label>Titel</label>
        <input name="FORM[title]" type="text" class="form-control" value="<%$FEEDB.form.title|sthsc%>">
    </div>
    
    <div class="form-group">
        <label>{LBL_MESSAGE}:</label><%$FEEDB.form.editor%>
    </div>
    
    <div class="row">
       <div class="col-md-6">
            <%include file="cb.fileupload.tpl" name="datei" label="Bild"%>
        </div>
        <div id="js-feed-img" class="col-md-6"></div>
    </div>       
     
     <input type="hidden" name="cmd" value="save_item">
     <%$subbtn%>
    </form>
    <script>
        function load_feed_img() {
           simple_load('js-feed-img','<%$PHPSELF%>?epage=<%$epage%>&cmd=load_feed_img&id=<%$GET.id%>'); 
        }
        load_feed_img();
    </script>
<%/if%>

<script>
    function add_customer_to_feedback(kid,custname) {
        $('#feedbkid').val(kid);
        $('#feedbkid').after('<b>' + custname + '</b>');
        $('#feedbcustsearch').slideUp();
        $('.live_search').val('');
    }
</script>

<!-- Modal -->
<div class="modal fade" id="addfeed" tabindex="-1" role="dialog" aria-labelledby="addfeedLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
     <form action="<%$PHPSELF%>" method="post">
            <input type="hidden" name="cmd" value="additem">
            <input type="hidden" name="epage" value="<%$epage%>">
      <div class="modal-header">
        <h5 class="modal-title" id="addfeedLabel">Testimonials hinzufügen</h5>
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        
      </div>
      <div class="modal-body">
          <label>Von:</label>
            <input type="text" class="form-control" required placeholder="Von" name="FORM[kname]">
            <label>Email:</label>
            <input type="text" class="form-control" placeholder="Email" name="FORM[email]">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <%$subbtn%>
      </div>
      </form>
    </div>
  </div>
</div>  