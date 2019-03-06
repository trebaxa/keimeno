<link rel="stylesheet" type="text/css" href="<%$PATH_CMS%>includes/modules/faq/css/style.css">

<% if ($cmd=='load_items' || count($FAQ.faqitems)>0) %>

<% if (count($FAQ.faqitems)>0) %>
    <div class="panel-group" id="accordion">
<% foreach from=$FAQ.faqitems item=row %>
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapse-<%$row.id%>">
          <%$row.faq_question%>
        </a>
      </h4>
    </div>
    <div id="collapse-<%$row.id%>" class="panel-collapse collapse">
      <div class="panel-body">
        <%$row.faq_answer%>
      </div>
    </div>
  </div>
<%/foreach%>
</div>

<%else%>
Keine Einträge vorhanden.
<%/if%>



<%else%>
<% if (count($FAQ.groups)>1) %>
<div class="btn-group">
<% foreach from=$FAQ.groups item=row %>
        <a class="btn btn-default" title="<%$row.g_name|hsc%>" href="javascript:void(0);" onclick="simple_load('faqitems','<%$PHPSELF%>?page=<%$page%>&cmd=load_items&gid=<%$row.id%>')"><%$row.g_name%></a>
<%/foreach%>
</div>
<div id="faqitems"></div>
<%/if%>
<%/if%>