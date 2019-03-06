<div class="plugin">
<%*
<div class="form-group">
    <label>Template:</label>
    <select class="form-control" name="PLUGFORM[tplid]">
        <% foreach from=$WEBSITE.PLUGIN.result.templates item=row %>
            <option <% if ($WEBSITE.node.tm_plugform.tplid==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
        <%/foreach%>
    </select>
</div>

*%>

<div class="form-group">
    <label>Iframe Template of mylivechat:</label>
    <textarea name="PLUGFORM[mylivechat_tpl]" class="form-control se-html"><%$WEBSITE.node.tm_plugform.mylivechat_tpl%></textarea>
</div>

<div class="well">
    Create somewhere in your template an button like this to activate chat:
    <code>
    <%help%><%literal%>
<div id="js-mylivechat" class="">
  <i onclick="$('#js-mylivevhat-form').toggle();" class="fa fa-comment-o fa-3x"></i>
  <div class="" id="js-mylivevhat-form">
    <div class="checkbox">
        <label>
            <input id="js-mylivechat-check" type="checkbox" value="1" name="disclaimner_accept" /> Hiermit akzeptieren Sie unsere <a href="#">Datenschutzbestimmungen</a>.
        </label>
    </div>
    <button type="button" onclick="start_mylivechat()" class="btn btn-default btn-sm">Chat starten</button> 
  </div>
</div>
<style>
  #js-mylivechat {
    width: auto;
    background-color: #286090;
    position: fixed;
    bottom: 24px;
    left: 0px;
    outline: 0 none;
    padding: 10px;
    color:#fff;
  }
  #js-mylivevhat-form {
    display:none;
  }
  #js-mylivechat .fa, #js-mylivechat a {
    color:#fff;
    cursor:pointer;
  }
</style>
    <%/literal%><%/help%>
    </code>
</div>
