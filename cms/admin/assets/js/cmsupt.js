function start_update() {
    if( $("#rulecheck").prop("checked") ){
        $("#uptfault").hide();
        $("#updatecont").hide();
        $(".row1").hide();
        $(".row2").hide();
        $("#tpltab").show();
        $(".js-tplcheck").each(function( index ) {
         $(this).parent().parent().show();
         var cmd = $(this).data("cmd");
         $("#tpl-"+cmd).html("<p class=\'alert alert-danger\'>please wait...</p>");         
         simple_load_sync("tpl-"+cmd,"run.php?epage=cmsupt.inc&cmd="+cmd);               
        });  
        $("#uptfinish").show();        
        $("#updwarning").hide();
        simple_load_nocache('js-appmenu','run.php?epage=modulman.inc&cmd=reloadmenu'); 
    } else {
        $("#uptfault").show();
    }   
 return false;
}