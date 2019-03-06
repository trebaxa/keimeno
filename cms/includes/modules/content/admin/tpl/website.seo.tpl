<h3>SEO Analyse - Aus Sicht einer Suchmaschine</h3>

<div class="btn-group"><a class="btn btn-default" href="javascript:void(0)" onclick="startseo();">Neue Analyse</a></div>

<div id="seoresult"></div>

<script>
function startseo() {
    setLoaderIcon('seoresult','');
    simple_load('seoresult','<%$PHPSELF%>?epage=<%$epage%>&cmd=startseo&conid=<% $TPLOBJ.formcontent.id %>');
}
</script>