<div class="row">
    <div id="cssfiles" class="col-md-6"></div>
    <div id="usedcss" class="col-md-6"></div>    
</div>

<style>

.cssadd {
    cursor:pointer;
}
</style>

<script>
function load_css_tree() {
    simple_load('cssfiles','<%$PHPSELF%>?epage=<%$epage%>&cmd=load_css_folder');
    simple_load('usedcss','<%$PHPSELF%>?epage=<%$epage%>&cmd=load_usedcss');
}
</script>