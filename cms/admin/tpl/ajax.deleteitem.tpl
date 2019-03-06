$("table td img.delete").click(function () {
		var r=true;
		var rel = $(this).attr('rel');
		var parts = rel.split("|");
    if (parts[1]=='confirm') {
    	var r=confirm('{LBL_CONFIRM}');
    }
    var cmd = "axdelete_item";
    if (parts[0]!="") {
    	cmd = parts[0];
    }
    var phpfile = parts[2];
    var toadd = parts[3];
    if (r==true) {
    simple_load('axdelresult',phpfile +'?epage=<%$epage%>&cmd='+cmd+'&id=' + $(this).attr('id')+'&'+toadd);
    $(this).parent().parent().parent().fadeTo(400, 0, function () { 
        $(this).remove();
    });
    }
    return false;
});