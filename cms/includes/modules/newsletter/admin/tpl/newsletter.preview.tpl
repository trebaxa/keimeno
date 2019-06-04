    <%include file="cb.panel.header.tpl" title="{LBLA_PREVIEW} 1/3"%>
    <div class="text-center">
        <form method="post" action="<%$PHPSELF%>" enctype="multipart/form-data">
            <input type="hidden" name="epage" value="<%$epage%>">
            <input type="hidden" name="aktion" value="show_send">
            <input type="hidden" name="id" value="<%$GET.id%>">
    	 <input type="submit" class="btn btn-primary" value="weiter zu {LBLA_RECIPIENTS} 2/3">
         </form>
     </div>

     <br><iframe style="border:1px solid #eee;" src="<%$NEWSLETTER.preview_link%>" width="99%" height="600" name="news_preview" scrolling="yes" marginheight="0" marginwidth="0" frame target="_self" class="thumb"></iframe>
    <%include file="cb.panel.footer.tpl"%>