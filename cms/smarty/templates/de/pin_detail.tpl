<img src="<%$selected_item.thumb%>" style="max-width:100%;margin-bottom:10px;" alt="<% $selected_item.title|sthsc %>">

<article class="threecol">
  <h2><% $selected_item.title %></h2>
  <strong>{LBL_CREATED}:</strong>
  <% $selected_item.date %>
  <% $selected_item.content %>
  
  <% if (count($selected_item.fotos)>0) %>
    <div class="blog-row clearfix">
      <h3>Bilder</h3>
        <% foreach from=$selected_item.fotos item=row %>
            <img src="<%$PATH_CMS%>file_data/tcblog/fotos/<%$row.foto%>" class="blog-thumb">
        <%/foreach%>
      </div>
  <%/if%>
  
  <% if ($selected_item.b_ytid!="") %>
    <iframe width="100%" height="315" src="//www.youtube.com/embed/<%$selected_item.b_ytid%>" frameborder="0" allowfullscreen></iframe>
  <%/if%>
  
  <div id="js-blog-comments">
    <%include file="tcblog_blog-comments.tpl"%>
  </div>
  <div class="row">
    <div class="col-md-12">
        <form class="jsonform" method="POST" action="<%$PHPSELF%>">
          <input type="hidden" value="send_comment_blog" name="cmd">
          <input type="hidden" value="<%$page%>" name="page">
          <input type="hidden" value="<%$selected_item.DID%>" name="FORM[c_itemid]">
          <div class="form-group">
            <label for="blog-username">Ihr Name</label>
            <input id="blog-username" type="text" value="" name="FORM[c_autor]" required="" class="form-control"/>
          </div>
          <div class="form-group">
            <label for="blog-comment">Ihr Kommentar</label>
            <textarea id="blog-comment" name="FORM[c_comment]" required="" class="form-control"></textarea>
          </div>
          <div class="form-group hidden">
            <label for="blog-email">email</label>
            <input id="blog-email" type="email" value="" name="email" class="form-control"/>
          </div>  
          <% if ($contact.cf_cpatcha==1) %>
            <div class="form-group">
                <label for="blog-secure"  class="sr-only">{LBL_SECODE}*</label>
                <div class="input-group">
                  <input placeholder="{LBL_CODEENTER}" id="blog-secure" autocomplete="OFF" name="securecode" class="form-control" type="text"></td>  
                  <div class="input-group-addon"><img title="{LBL_SECODE}" alt="capcha" style="height:20px;"  src="<%$PATH_CMS%>captcha.php"></div>
                </div>
                
                
            </div>
          <% /if %>             
          <button class="btn btn-default" type="submit"><i class="fa fa-pencil-square-o "></i> senden</button>
        </form>
      </div>
  </div>
</article>

<script>
  function reload_blog_comments() {
    simple_load('js-blog-comments','<%$PHPSELF%>?page=<%$page%>&cmd=reload_blog_comments&id=<%$selected_item.DID%>');
  }
</script>