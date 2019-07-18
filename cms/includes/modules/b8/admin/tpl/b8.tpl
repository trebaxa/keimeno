<link rel="stylesheet" href="../includes/modules/b8/admin/css/style.css" type="text/css"/>

<div class="page-header"><h1>b8 - AntiSpam</h1></div>

<div class="row">
<div class="col-md-6">
<form action="<%$PHPSELF%>?epage=<%$epage%>" method="post" class="form jsonform">
<div class="form-group">
    <label>SPAM Text</label>
    <textarea class="form-control" name="text" cols="50" rows="16"></textarea>
</div>
<div class="form-group">
    <label>Aktion</label>
    <select class="form-control custom-select" name="cmd">
        <option value="classify">Classify</option>
        <option value="save_spam">Save as Spam</option>
        <option value="save_ham">Save as Ham</option>
        <option value="del_spam">Delete from Spam</option>
        <option value="del_ham">Delete from Ham</option>
        <option value="reset_db">reset database</option>
    </select>
</div>
<input type="submit" class="btn btn-primary" value="GO">
</form>
</div>
    <div class="col-md-6">
        <div id="b8result"></div>
        <div class="alert alert-info">1 = SPAM Verdacht sehr hoch; 0 = Kein SPAM<br>Infos unter <a target="_blank" href="http://nasauber.de/opensource/b8/">http://nasauber.de/opensource/b8/</a>
        <br>Letzte SPAM Filterung dauerte im Frontend: <%$B8.timetaken%>&thinsp;sec
        </div>
        <h3>Konfiguration</h3>
        <%$B8.CONFIG%>
    </div>
</div>
<script>
function show_classify(rating,red,green) {
    $('#b8result').html('<div style="padding:10px;color:#fff;background-color:rgb('+red+', '+green+', 0);"><b>'+rating+'</b></div>');
}
</script>