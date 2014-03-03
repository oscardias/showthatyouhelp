/*
 * jQuery Document Ready
 */
function updateContent(element, content){
    element.fadeOut('slow', function(){
        $(this).html(content).fadeIn('slow');
    });
}

function fetchUrl(){
    if($('#url').val() != '') {
        $('#submit').attr('disabled', true);
        updateContent($('.updates-list-share-answer'), '<div class="loading"></div>');		
        $.post(base_url + 'share/url', $('#share').serialize(), 
        function(data){
            if(data.result == 1) {
                $('.updates-list-share-answer').html(data.html);
                
                url_data = jQuery.parseJSON(data.url_json);
                $('#share input[name=remote]').val(1);
                $('#share input[name=url]').val(url_data.url);
                $('#share input[name=title]').val(url_data.title);
                $('#share input[name=description]').val(url_data.description);
                $('#share input[name=image]').val($('.share-images-list img.selected').attr('src'));
                $('#share input[name=icon]').val(url_data.icon);
                $('#share input[name=domain]').val(url_data.domain);
                $('#share input[name=site_name]').val(url_data.site_name);
                
                $('#submit').removeAttr('disabled');
            } else {
                $('#submit').attr('disabled', true);
                updateContent($('.updates-list-share-answer'), data.html);
            }
        }, 'json')
        .error(function(){
            $('#submit').attr('disabled', true);
            updateContent($('.updates-list-share-answer'),
                '<p class="error-msg">An internal error occurred. Please try again. If the error persists contact us at support@showthatyouhelp.com</p>');
        });
    }
}

function switchImage(direction){
    var count_arr = $('.share-images-counter').html().split('/');
    var current = parseInt($.trim(count_arr[0]));
    var total = parseInt($.trim(count_arr[1]));
    var next;
    if(direction == 'left')
        next = (current == 1)?(total):(current - 1);
    else
        next = (current == total)?(1):(current + 1);

    $('.single-image-' + (current - 1)).fadeOut('fast').removeClass('selected');
    $('.single-image-' + (next - 1)).fadeIn('fast').addClass('selected');
    $('#image').val($('.share-images-list img.selected').attr('src'));
    $('.share-images-counter').html(next + ' / ' + total);

}

$(document).ready(function(){
    // Fetch URL automatically
    fetchUrl();
    
    // URL change update
    $('#url').blur(function(){
        fetchUrl();
    });
    
    // Select image
    $('.share-images-left').live('click', function(){
        switchImage('left');
    });
    $('.share-images-right').live('click', function(){
        switchImage('right');
    });
    
    // Share form submission
    $('#share').submit(function(){
        $.post(base_url + 'share', $(this).serialize(), 
        function(data){
            if(data.result == 1)
                window.close();
            else {
                $('#submit').attr('disabled', true);
                $('.updates-list-share-answer').html(data.html);
            }
        }, 'json')
        .error(function(){
            $('#submit').attr('disabled', true);
            updateContent($('.updates-list-share-answer'),
                '<p class="error-msg">An internal error occurred. Please try again. If the error persists contact us at support@showthatyouhelp.com</p>');
        });
        return false;
    });
});