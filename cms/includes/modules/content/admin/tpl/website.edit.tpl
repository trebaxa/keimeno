
        <form class="jsonform form" method="post" action="<%$PHPSELF%>" enctype="multipart/form-data">
            <input type="hidden" name="tid" value="<% $GET.id %>">
            <input type="hidden" name="tl" value="<% $GET.tl %>">
            <input type="hidden" name="cmd" value="save_content">
            <input type="hidden" name="uselang" value="<% $GET.uselang%>">
            <input type="hidden" name="parent" value="<% $TPLOBJ.parent|hsc %>">
            <input type="hidden" name="FORM[lang_id]" value="<% $GET.uselang %>">
            <input type="hidden" name="FORM[tid]" value="<% $GET.id %>">
            <input type="hidden" name="id" value="<% $TPLOBJ.formcontent.id %>">    
            <input type="hidden" name="epage" value="<% $epage %>"> 
            <input type="hidden" name="tmsid" value="<% $GET.tmsid %>"> 

            <div id="webstitlecont">
                <%include file="website.edit.title.tpl"%>
            </div><!-- /#webstitlecont -->
         </form>  
                        
            <div id="tplcontent">
                <% include file="website.addcontent.tpl" %>
            </div><!-- /#tplcontent -->
            
            <div class="form-feet">
               
            </div><!-- /.form-feet -->
        

    <script>
        function load_title_tpl() {
            simple_load('webstitlecont','<%$eurl%>cmd=load_titletpl&tid=<% $GET.id %>&uselang=<% $GET.uselang%>');
        }
        
        var woptions = {
            target: '#webstitlecont', 
            type: 'POST',
            forceSync: true,
            success:  show_saved_msg
        };
        
        $('.websiteedittitleform').submit(function() {
            $(this).ajaxSubmit(woptions);
            return false;
        });
    </script>

<script>
    function moveupedit(TMID,tm_pos) {
        simple_load('tplcontent','<%$eurl%>cmd=moveup&id='+TMID+'&tm_pos='+tm_pos);
    }
    
    function movedownedit(TMID,tm_pos) {
        simple_load('tplcontent','<%$eurl%>cmd=movedown&id='+TMID+'&tm_pos='+tm_pos);  
    }
    
</script> 
