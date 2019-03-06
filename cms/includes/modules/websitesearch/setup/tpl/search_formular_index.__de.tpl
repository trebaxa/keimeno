<script  type="text/javascript" src="<%$PATH_CMS%>js/autocompleter/Observer.js"></script>
<script  type="text/javascript" src="<%$PATH_CMS%>js/autocompleter/Autocompleter.js"></script>
<link rel="stylesheet" href="<%$PATH_CMS%>js/autocompleter/Autocompleter.css" type="text/css" media="screen" />
<script type="text/javascript">
    window.addEvent('domready', function() {    
        new Autocompleter.Ajax.Json('fe-searcher', '<%$PATH_CMS%>siauto.php', {
            'postVar': 'setvalue'
        });
    });
</script>
<form name="searchform" action="<% $searchformurl %>" method="POST">
<input type="hidden" name="cmd" value="indexsearch">
<img style="padding-left:3px;float:right;margin-top:3px;" onClick="document.searchform.submit()" src="/images/opt_sr_btn.gif"  >
<% if ($POST.setvalue=="") %><% assign var=sv value="Suchbegriff" %><% else %><% assign var=sv value=$POST.setvalue %><%/if%>
<input autocomplete="off" id="fe-searcher" name="setvalue" value="<% $sv %>" <%if ($sv=="Suchbegriff") %> onFocus="javascript:this.value=''"<%/if%> type="text" class="searcher" size="16" >
</form>
