function show_zw(id) {
 $('.zwcont').hide();
 $('#zw-'+id).show();
 $('#zw-zahlweise').val(id);
}

$(document).ready(function() {

$(".zwtabclick").click(function() {
 var tid = $(this).attr('id');
 var parts = tid.split("-");
 var id = parts[1];
 show_zw(id);
 $('.faultbox').hide();
});


});   