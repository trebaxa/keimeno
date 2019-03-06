<div class="page-header"><h1>Backup</h1></div>

<div class="btn-group"><%$LAY.backup.date%></div>

<h3>Inhalt</h3>
<div style="overflow: scroll;height: 410px;width: 830px;padding: 10px 30px;margin-bottom: 30px;">
<%$LAY.backup.b_content|hsc|stripslashes|nl2br%>
</div>

<form action="<%$PHPSELF%>" method="POST">
    <input type="hidden" name="epage" value="<%$epage%>">
    <input type="hidden" name="cmd" value="restorecss">
    <input type="hidden" name="id" value="<%$GET.id%>">
    <input type="submit" class="btn btn-primary" value="Restore">
</form>