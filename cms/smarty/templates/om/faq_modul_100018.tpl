<link rel="stylesheet" type="text/css" href="/includes/modules/faq/css/style.css">

<% if ($cmd=='load_items') %>

<% if (count($FAQ.faqitems)>0) %>
<% foreach from=$FAQ.faqitems item=row %>
    <div class="faqitems">
        <h3 class="faqqestion" data-ident="<%$row.id%>"><%$row.faq_question%></h3>
        <div class="faqanswer" id="<%$row.id%>"><%$row.faq_answer|truncate:300%></div>
    </div>
<%/foreach%>
<script>
$( ".faqqestion" ).click(function() {
    $(this).next().slideDown();
});
</script>
<%else%>
Keine EintrÃ¤ge vorhanden.
<%/if%>



<%else%>
<h2>FAQ</h2>

<div id="faq">
<ul>
<% foreach from=$FAQ.groups item=row %>
    <li>
        <a title="<%$row.g_name|hsc%>" href="javascript:void(0);" onclick="simple_load('faqitems','<%$PHPSELF%>?page=<%$page%>&cmd=load_items&gid=<%$row.id%>')"><%$row.g_name%></a>
    </li>
<%/foreach%>
</ul>
<div class="clearer"></div>
<div id="faqitems"></div>
</div>
<%/if%>
