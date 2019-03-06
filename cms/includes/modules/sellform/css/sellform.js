function show_zw(id) {
 $('.zwcont').hide();
 $('#zw-'+id).show();
 $('#zw-zahlweise').val(id);
 if (id==5) {
  $('#register-blue-arrow').css('background-position','50px bottom');
 }
 if (id==11) {
  $('#register-blue-arrow').css('background-position','200px bottom');
 } 
 if (id==6) {
  $('#register-blue-arrow').css('background-position','350px bottom');
 } 
 if (id==2) {
  $('#register-blue-arrow').css('background-position','547px bottom');
 } 
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