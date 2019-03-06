 <div class="row">
    <div class="col-md-12">
        <div class="pull-right">
            <small><b><%$RAMICRONEWS.newsdetail.category%> <%$RAMICRONEWS.newsdetail.publishingdate%></b></small>
        </div>
        <% if ($RAMICRONEWS.newsdetail.subtitle!="") %><h5><%$RAMICRONEWS.newsdetail.subtitle%></h5><%/if%>            
        <p><%$RAMICRONEWS.newsdetail.content|st|nl2br%></p>
        <br><i><%$RAMICRONEWS.newsdetail.reference%></i>
    </div>  
</div>        