<form role="form" name="searchform" action="{URL_TPL_100166}" class="form-inline" method="POST">
  <input type="hidden" name="cmd" value="fulltextsearch">
    <div class="form-group">
      <input type="search" autocomplete="off" id="fe-searcher" class="form-control" name="FORM[keyword]" <%if ($sv=="Suchbegriff") %> onFocus="javascript:this.value=''"<%/if%> value="<%$POST.FORM.keyword%>" placeholder="Ihr Suchbegriff...">
    </div>
    <button type="submit" class="btn btn-default"><i class="fa fa-search"><span class="sr-only">Suchen</span></i></button>
</form>