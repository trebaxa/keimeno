function showRequest(formData, jqForm, options) { 
    $('#video-list').fadeOut();
    return true; 
} 

function showResponse(responseText, statusText, xhr, $form)  { 
    $('#video-list').fadeIn();
    return true; 
} 

$(document).ready(function() { 
    var options = { 
        target:        '#video-list',
        beforeSubmit:  showRequest,
        success:       showResponse
    }; 
    $('#video-list-form').ajaxForm(options); 
}); 


 function video_show_next(start, word, php, page) {
  $('#video-list').fadeOut();
  simple_load_sync('video-list',php+'?page='+page+'&cmd=search_videos_fe&start='+start+'&sword='+word);
  $('#video-list').fadeIn();
 }