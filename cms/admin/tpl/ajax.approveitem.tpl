$("img.axapprove").click(function() {
        var cmd = $(this).data('cmd');
        if ($(this).data('cmd') == "") cmd = 'axapprove_item';
        execrequest($(this).data('phpself') + '?epage=' + $(this).data('epage') + '&cmd='+cmd+'&id=tmp-' + $(this).data('ident') + '&value=' + $(this).data('value') + '&' + $(this).data('toadd'));
        if ($(this).data('value') == 1) {
                $(this).attr("src", './images/page_visible.png');
                $(this).data('value','0');
        } else {
                $(this).attr("src", './images/page_notvisible.png');
                $(this).data('value','1');
                
        }
        return false;
});