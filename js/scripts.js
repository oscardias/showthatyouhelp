// Loading img src
var loadingSrc = base_url + 'images/ajax-loader.gif';

// Refresh rate
var refreshRate = 120000;
var local_stamp = Math.round(+new Date / 1000);

/*
* jQuery.ajaxQueue - A queue for ajax requests
* 
* (c) 2011 Corey Frang
* Dual licensed under the MIT and GPL licenses.
*
* Requires jQuery 1.5+
*/ 
(function($) {

// jQuery on an empty object, we are going to use this as our Queue
var ajaxQueue = $({});

$.ajaxQueue = function( ajaxOpts ) {
    var jqXHR,
        dfd = $.Deferred(),
        promise = dfd.promise();

    // queue our ajax request
    ajaxQueue.queue( doRequest );

    // add the abort method
    promise.abort = function( statusText ) {

        // proxy abort to the jqXHR if it is active
        if ( jqXHR ) {
            return jqXHR.abort( statusText );
        }

        // if there wasn't already a jqXHR we need to remove from queue
        var queue = ajaxQueue.queue(),
            index = $.inArray( doRequest, queue );

        if ( index > -1 ) {
            queue.splice( index, 1 );
        }

        // and then reject the deferred
        dfd.rejectWith( ajaxOpts.context || ajaxOpts, [ promise, statusText, "" ] );
        return promise;
    };

    // run the actual query
    function doRequest( next ) {
        jqXHR = $.ajax( ajaxOpts )
            .done( dfd.resolve )
            .fail( dfd.reject )
            .then( next, next );
    }

    return promise;
};

})(jQuery);

function showMessageDialog(title, message, icon, p_class) {
    var html = '<div style="display:none;" title="' + title + '">' +
	'<p><span class="ui-icon ' + icon + '"></span>' +
        '<p class="' + p_class + '">' + message + '</p></p>' +
        '</div>';
    $(html).dialog({
        resizable: false,
        height:140,
        modal: true,
        buttons: {
            Ok: function() {
                $( this ).dialog( "close" );
            }
        }
    });
}

function showErrorDialog(title, message){
    showMessageDialog(title, message, 'ui-icon-alert', 'error-msg');
}

function showStandardDialog(title, message){
    showMessageDialog(title, message, 'ui-icon-styh', '');
}

function showSystemError() {
    showErrorDialog(messages.js_error_title, messages.js_error_msg + ' <a href="' +
        base_url + 'about/contact">' + messages.js_error_here +'</a>.');
}

function fetchUrl(){
    var share_answer = $('#updates-list-share-answer');

    var btn = $('#submit').attr('disabled', true);

    share_answer.fadeOut('fast', function(){

        $(this).html('<div class="loading"></div>').fadeIn('fast', function(){

            var form = $('#home-share-form');

            $.ajaxQueue({
                type: 'POST',
                url: base_url + 'share/url',
                data: form.serialize(),
                dataType: 'json',
                success: function(data){
                    if(data.result == 1) {
                        share_answer.html(data.html);

                        url_data = jQuery.parseJSON(data.url_json);
                        form.children('input[name=remote]').val(1);
                        form.children('input[name=url]').val(url_data.url);
                        form.children('input[name=title]').val(url_data.title);
                        form.children('input[name=description]').val(url_data.description);
                        form.children('input[name=image]').val($('.share-images-list img.selected', share_answer).attr('src'));
                        form.children('input[name=icon]').val(url_data.icon);
                        form.children('input[name=domain]').val(url_data.domain);
                        form.children('input[name=site_name]').val(url_data.site_name);
                        form.children('input[name=player]').val(url_data.player);

                        btn.removeAttr('disabled').val(messages.js_share_submit);
                    } else {
                        btn.removeAttr('disabled');
                        share_answer.fadeOut('fast',function(){
                            $(this).html(data.html).fadeIn('fast');
                        });
                    }
                },
                error: function(){
                    btn.removeAttr('disabled');
                    share_answer.fadeOut('fast',function(){
                        showSystemError();
                    });
                }
            });

        });

    });

}

function switchImage(direction){
    var count_arr = $('#share-images-counter').html().split('/');
    var current = parseInt($.trim(count_arr[0]));
    var total = parseInt($.trim(count_arr[1]));
    var next;
    if(direction == 'left')
        next = (current == 1)?(total):(current - 1);
    else
        next = (current == total)?(1):(current + 1);

    $('.single-image-' + (current - 1)).fadeOut('fast').removeClass('selected');
    $('.single-image-' + (next - 1)).fadeIn('fast').addClass('selected');
    $('#home-share-form input[name=image]').val($('.share-images-list img.selected').attr('src'));
    $('#share-images-counter').html(next + ' / ' + total);

}

function getShareInput(type){
    switch (type) {
        case 'link':
            return '<input type="text" name="url" id="url" placeholder="' + messages.js_link + '">';
        case 'video':
            return '<input type="text" name="video" id="video" placeholder="' + messages.js_video + '">';
        case 'photo':
            return '<input type="text" name="photo_styled" id="photo-styled" placeholder="' + messages.js_link + '"><input type="file" name="photo" id="photo">';
    }
}

// Function for loading items (updates/notifications)
function loadNextItems(btn, wrap){
    if(btn.hasClass('gradient-loading') || btn.is(':hidden'))
        return true;
    
    var pattern = /\/(\d+)(?!.*\d)/g;
    var href = btn.attr('href');
    var result = pattern.exec(href);
    var total_pages = $('#updates-list-load-total-pages', wrap);
    
    if(total_pages.val() > (result[1] - 1)) {
        
        btn.addClass('gradient-loading').html('&nbsp;');
        
        $.ajaxQueue({
            type: 'GET',
            url: href + '/' + $('#updates-list-refresh-last').val(),
            success: function(data){
                wrap.before(data);
                var next_page = parseInt(result[1]) + 1;
                btn.attr('href', href.replace(/\/(\d+)(?!.*\d)/g, '/' + next_page));
                btn.html(messages.js_load_more).removeClass('gradient-loading');
                if(total_pages.val() <= (next_page - 1)) {
                    btn.hide();
                }
                refreshTimes();
            }
        });
    } else {
        btn.hide();
    }
    
    return true;
}

/*
*   Refresh functions
*/
function titleUpdatesCount(count){
    var title = $('title').html();
    if(count > 0)
        $('title').html('(' + count + ') ' + title.replace(/\(\d+\)/g, ""));
    else
        $('title').html(title.replace(/\(\d+\)/g, ""));
}

function refreshNotifications() {
    setInterval(function(){
        $.ajaxQueue({
            type: 'GET',
            url: base_url + 'ajax/get_unread_notifications_count',
            cache: false,
            dataType: 'json',
            success: function(data){
                $('#notifications-link').html(data.html);
            }
        });
    }, refreshRate);
}

function refreshUpdates() {
    setInterval(function(){
        $.ajaxQueue({
            type: 'GET',
            dataType: 'json',
            url: base_url + 'ajax/get_new_updates_count/' + $('#updates-list-refresh-last').val() + '/1',
            success: function(data){
                if(data.count > 0)
                    $('#updates-list-refresh').html(data.html).slideDown('fast');
                else
                    $('#updates-list-refresh').html('').slideUp('fast');
                
                titleUpdatesCount(data.count);
                refreshTimes();
            }
        });
    }, refreshRate);
}
function refreshPosts() {
    setInterval(function(){
        $.ajaxQueue({
            type: 'GET',
            url: base_url + 'ajax/get_new_posts_count/' + 
                $('#updates-list-username').val() + '/' + 
                $('#updates-list-user-id').val() + '/' + 
                $('#updates-list-refresh-last').val() +
                '/1',
            success: function(data){
                if(data.count > 0)
                    $('#updates-list-refresh').html(data.html).slideDown('fast');
                else
                    $('#updates-list-refresh').html('').slideUp('fast');
                
                titleUpdatesCount(data.count);
                refreshTimes();
            }
        });
    }, refreshRate);
}

function refreshTimes(){
    var server_stamp = $('#updates-list-refresh-stamp');
    if(server_stamp.length) {
        $('#updates-list-left small.time-since').each(function(){
            var ago_stamp = (Math.round(+new Date / 1000) - local_stamp) + 
                (parseInt(server_stamp.val()) - parseInt($(this).attr('stamp')));

            var tokens = new Array();
            var tokens_p = new Array();
            var stamps = new Array();

            tokens[0] = messages.js_year; // 60 * 60 * 24 * 365
            tokens_p[0] = messages.js_years;
            stamps[0] = 31536000;
            tokens[1] = messages.js_month; // 60 * 60 * 24 * 30
            tokens_p[1] = messages.js_months;
            stamps[1] = 2592000;
            tokens[2] = messages.js_week; // 60 * 60 * 24 * 7
            tokens_p[2] = messages.js_weeks;
            stamps[2] = 604800;
            tokens[3] = messages.js_day; // 60 * 60 * 24
            tokens_p[3] = messages.js_days;
            stamps[3] = 86400;
            tokens[4] = messages.js_hour; // 60 * 60
            tokens_p[4] = messages.js_hours;
            stamps[4] = 3600;
            tokens[5] = messages.js_minute; // 60
            tokens_p[5] = messages.js_minutes;
            stamps[5] = 60;
            tokens[6] = messages.js_second;
            tokens_p[6] = messages.js_seconds;
            stamps[6] = 1;

            var num_units;

            for (var i=0;i<7;i++) {
                if (ago_stamp < stamps[i]) continue;
                num_units = Math.floor(ago_stamp / stamps[i]);
                if(num_units > 1)
                    $(this).html(num_units + ' ' + tokens_p[i] + ' ' + messages.js_ago);
                else
                    $(this).html(num_units + ' ' + tokens[i] + ' ' + messages.js_ago);
                break;
            }
        });
    }
}

// Function to get the Min value in Array
Array.min = function(array) {
    return Math.min.apply(Math, array);
};
// Function to get the Max value in Array
Array.max = function(array) {
    return Math.max.apply(Math, array);
};

/*
 * Custom jQuery functions
 */
jQuery.fn.updateCommentCount = function(num) {
    if(num == 1)
        $(this[0]).html('1 ' + messages.js_comment);
    else
        $(this[0]).html(num + ' ' + messages.js_comments);
};

jQuery.fn.insertNewHtml = function(action, element) {
    switch(action) {
        case 'insertAfter':
            $(this[0]).hide().insertAfter(element).fadeIn('fast');
            break;
        case 'insertBefore':
            $(this[0]).hide().insertBefore(element).fadeIn('fast');
            break;
        case 'appendTo':
            $(this[0]).hide().appendTo(element).fadeIn('fast');
            break;
    }
};

jQuery.fn.replaceThis = function(html) {
    var current = $(this[0]);
    current.slideUp('fast', function(){
        $(html).hide().insertAfter(current).slideDown('fast', function(){
            current.remove();
        });
    });
};

jQuery(function() {
    jQuery.support.placeholder = false;
    test = document.createElement('input');
    if('placeholder' in test) jQuery.support.placeholder = true;
});

/*
 * jQuery Document Ready
 */
$(document).ready(function(){
    // jQuery objects
    //var updates_left = $("#updates-list-left");
    var updates_right = $("#updates-list-right");
    var updates_refresh = $('#updates-list-refresh-wrap');

    // Placeholder support
    if(!$.support.placeholder) {
        // Placeholder text is not supported.
        $('[placeholder]').live('focus', function(){
            var input = $(this);
            if (input.val() == input.attr('placeholder')) {
                if (input.setSelectionRange) {
                    input.setSelectionRange(0, 0);
                } else if (input.get(0).createTextRange) {
                    var range = input.get(0).createTextRange();
                    range.collapse(true);
                    range.moveEnd('character', 0);
                    range.moveStart('character', 0);
                    range.select();
                }
            }
        }).live('keydown',function() {
            var input = $(this);
            if (input.val() == input.attr('placeholder')) {
                input.val('');
                input.removeClass('placeholder');
            }
        }).live('blur', function() {
            var input = $(this);
            if (input.val() == '' || input.val() == input.attr('placeholder')) {
                input.addClass('placeholder');
                input.val(input.attr('placeholder'));
            }
        }).blur();
        
        $('form').submit(function() {
            $(this).find('[placeholder]').each(function() {
                var input = $(this);
                if (input.val() == input.attr('placeholder')) {
                    input.val('');
                }
            })
        });
    }

    /*
     * Sign Up
     */
    // Begin - Settings Form
    var settings_form = $('#settings-form');
    if(settings_form.length == 0)
        settings_form = $('#sign-up-form');
    if(settings_form[0]) {
        $('input.validate-focusout', settings_form).focusout(function(){
            var field = $(this);
            var id = field.attr('id');
            var post_data = {};
            post_data[id] = field.val();

            if(id == 'password_confirm')
                post_data['password'] = $('input#password', settings_form).val();

            $.ajaxQueue({
                type: 'POST',
                url: base_url + 'ajax/validate_sign_up',
                data: post_data,
                dataType: 'json',
                cache: false,
                success: function(data){
                    if(data.result == 0) {
                        field.addClass('error_box');
                        $('#error_' + id, settings_form).html(data[id]);
                    } else {
                        field.removeClass('error_box');
                        $('#error_' + id, settings_form).html('');
                    }
                }
            });
        });

        settings_form.submit(function(){
            var form = $(this);
            var input_error = $('input.error_box', form);
            if(input_error.length) {
                input_error.addClass('animated tada');
                var wait = window.setTimeout( function(){
                            input_error.removeClass('animated tada')},
                            1300
                    );

                    return false;
            } else {
                $.ajaxQueue({
                    type: 'POST',
                    url: base_url + 'ajax/validate_sign_up',
                    data: form.serialize(),
                    dataType: 'json',
                    cache: false,
                    success: function(data){
                        if(data.result == 0) {
                            for (var key in data) {
                                if(key != 'result' && data[key] != '') {
                                    $('#' + key, form).addClass('error_box');
                                    $('#error_' + key, form).html(data[key]);
                                }
                            }

                            return false;
                        } else {
                            return true;
                        }
                    }
                });
            }
        });

        // Count remaining chars
        $('#bio', settings_form).keyup(function(){
            var remaining = 255 - $(this).val().length;
            if(remaining < 0) {
                $(this).val($(this).val().substring(0, 255));
                $('#bio-length-remaining', settings_form).text(0);
                return false;
            }
            $('#bio-length-remaining', settings_form).text(remaining);
        });
    }
    // End - Settings Form
    
    /*
     * Share
     */
    // Begin - Share Form
    var share_form = $('#home-share-form');
    if(share_form[0]) {
        // URL change update
        $('#url', share_form).live('focus', function(){
            var btn = share_form.children('input[type=submit]');
            btn.val('Preview');
        });
        
        // Video change update
        $('#video', share_form).live('focus', function(){
            var btn = share_form.children('input[type=submit]');
            btn.val('Preview');
        });

        // Select image
        $('#share-images-left').live('click', function(){
            switchImage('left');
        });
        $('#share-images-right').live('click', function(){
            switchImage('right');
        });

        // Change share type
        $('#update-list-share-btns a.share-btn-action').click(function(){
            var form = share_form;
            form.children('input[type=submit]').val(messages.js_share_submit);
            
            var add = form.find('#update-list-share-add');
            var href = $(this).attr('href');

            var type = href.substr(href.lastIndexOf('/') + 1);
            var prev_type = form.children('input[name=type]').val();

            if(type == prev_type)
                return false;

            // Clear error box
            form.children('textarea').removeClass('error_box');

            $('#update-list-share-btns a.share-btn-action.selected').removeClass('selected');
            $(this).addClass('selected');

            form.children('input[name=type]').val(type);

            // Ensure link type matches the url input name
            if(prev_type == 'link')
                prev_type = 'url';

            if(type == 'text' && prev_type != 'text') {
                add.fadeOut('fast', function(){
                    $(this).html('');
                });
            } else if(type != 'text' && prev_type == 'text') {
                add.html(getShareInput(type)).fadeIn('fast', function(){
                    if(!$.support.placeholder) {
                        $('[placeholder]', add).each(function(){
                            var input = $(this);
                            if (input.val() == '' || input.val() == input.attr('placeholder')) {
                                input.addClass('placeholder');
                                input.val(input.attr('placeholder'));
                            }
                        });
                    }
                });
            } else {
                add.fadeOut('fast', function(){
                    $(this).html(getShareInput(type)).fadeIn('fast', function(){
                        if(!$.support.placeholder) {
                            $('[placeholder]', add).each(function(){
                                var input = $(this);
                                if (input.val() == '' || input.val() == input.attr('placeholder')) {
                                    input.addClass('placeholder');
                                    input.val(input.attr('placeholder'));
                                }
                            });
                        }
                    });
                });
            }
            
            // Clear answer
            $('#updates-list-share-answer').fadeOut('fast', function(){
                $(this).html('');
            });

            return false;
        });

        $('#home-share-comment').focus(function(){
            $(this).animate({width: '500px', height: '100px', boxShadow: '0 0 5px #000'}).addClass('animate');
        });

        $('#home-share-comment').focusout(function(){
            $(this).animate({width: '258px', height: '50px', boxShadow: '0 0 0 #000'}).removeClass('animate');
        });
    
        $('#photo').live('change', function(){
            var pattern = /^.*[\/\\](.*)$/i;
            var file = $(this).val();
            var result = pattern.exec(file);
            if(result != null)
                file = result[1];
            
            $('#photo-styled').val(file);
        });

        share_form.submit(function(){
            var form = $(this);
            var btn = form.children('input[type=submit]');
            
            if(btn.val() == 'Preview') {
                fetchUrl();
                return false;
            }
            
            btn.attr('disabled', true);

            // Set remote field
            form.children('input[name=remote]').val(1);

            var type = form.children('input[name=type]').val();
            var loading = $('<div class="loading-wrap"><div class="loading-inner"></div></div>').hide();

            // Run traditional upload for files - TODO: improve it
            if(type == 'photo') {
                loading.appendTo(form.parent()).fadeIn('fast');
                return true;
            }

            if(type == 'link')
                type = 'url';

            loading.appendTo(form.parent()).fadeIn('fast', function(){
                $.ajaxQueue({
                    type: 'POST',
                    url: form.attr('action'),
                    data: form.serialize(),
                    dataType: 'json',
                    cache: false,
                    success: function(data){
                        if(data.result == 1){
                            form.children('textarea').val('').removeClass('error_box');
                            if(type != 'text')
                                form.find('input[name=' + type + ']').val('').removeClass('error_box');
                            $('#updates-list-share-answer').fadeOut('fast', function(){
                                $(this).html('');
                            });
                        } else {
                            data.fields = jQuery.parseJSON(data.fields);
                            if(data.fields.comment != '')
                                form.children('textarea').addClass('error_box');
                            if(data.fields.url != '')
                                form.children('input[name=url]').addClass('error_box');
                            if(data.fields.video != '')
                                form.children('input[name=video]').addClass('error_box');
                            if(data.fields.photo != '')
                                form.children('input[name=photo]').addClass('error_box');

                            $('#updates-list-share-answer').html(data.html).fadeIn('fast');
                        }

                        btn.removeAttr('disabled');
                        loading.remove();
                    },
                    error: function(){
                        btn.removeAttr('disabled');
                        loading.remove();
                        showSystemError();
                    }
                });

                // Queue updates list refresh
                $.ajaxQueue({
                    type: 'GET',
                    dataType: 'json',
                    url: base_url + 'ajax/get_new_updates_count/' + $('#updates-list-refresh-last').val() + '/1',
                    success: function(data){
                        if(data.count > 0)
                            $('#updates-list-refresh').html(data.html).slideDown('fast');
                        else
                            $('#updates-list-refresh').html('').slideUp('fast');

                        titleUpdatesCount(data.count);
                    }
                });

            });

            return false;
        });
    }
    // End - Share Form
    
    /*
     * Updates List
     */
    // Check if user is in the updates list
    // Begin - Updates List Load
    var updates_load = $("#updates-list-load-wrap");
    if(updates_load[0]) {
        var updates_load_btn = $('#updates-list-load', updates_load);
        if(updates_load_btn[0]){
            // Hide standard pagination and show AJAX btn
            $('#updates-list-load-more-pages', updates_load).hide();
            updates_load_btn.css('display', 'inline-block').click(function(){
                loadNextItems(updates_load_btn, updates_load);
                return false;
            });

            // Load next page by default
            loadNextItems(updates_load_btn, updates_load);

            $(window).scroll(function () {
                if($(window).scrollTop() + $(window).height() > $(document).height() - 300)
                    loadNextItems(updates_load_btn, updates_load);
            });
        }
    }

    if(updates_refresh[0]){
        var type = $('#updates-list-refresh-type', updates_refresh).val();
        var href = '';
        if(type == 'home') {
            refreshUpdates();
            href = base_url + 'ajax/display_new_updates/';
        } else if(type == 'profile') {
            refreshPosts();
            href = base_url + 'ajax/display_new_posts/' + 
            $('#updates-list-user-id', updates_refresh).val() + '/';
        }
        
        $('#updates-list-refresh-url', updates_refresh).live('click', function(){
            var refresh_block = $(this).parent();
            var last = $('#updates-list-refresh-last', updates_refresh);
            
            refresh_block.slideUp('fast', function(){
                $.ajaxQueue({
                    type: 'GET',
                    url: href + last.val(),
                    success: function(data){
                        $(data).hide().insertAfter(refresh_block).slideDown('fast', function(){
                            var id = refresh_block.next().attr('id');
                            id = id.split('-');
                            last.val(id[1]);

                            titleUpdatesCount(0);
                            refreshTimes();
                        });
                    }
                });
            });
            
            return false;
        });
        
    }
    
    $('a.remove-update-action').live('click', function(){
        var update = $(this).parent();
        var id = update.attr('id');
        id = id.split('-');        

        $.ajaxQueue({
            type: 'GET',
            url: base_url + 'ajax/update_delete/' + id[1],
            success: function(data){
                update.replaceThis(data);

                refreshTimes();
            }
        });

        return false;
    });

    $('a.update-delete-undo-action').live('click', function(){
        var wrap = $(this).parent();
        var href = $(this).attr('href');

        $.ajaxQueue({
            type: 'GET',
            url: href,
            cache: false,
            success: function(data){
                wrap.replaceThis(data);

                refreshTimes();
            }
        });

        return false;
    });

    // Connect to user
    $('a.connect-action').live('click', function(){
        var btn = $(this);

        var pattern = /^.*\/(.*)$/g;
        var href = btn.attr('href');
        var result = pattern.exec(href);
        var username = result[1];

        var loading = $('<div class="loading-btn"></div>');
        btn.append(loading);

        $.ajaxQueue({
            type: 'GET',
            url: href,
            dataType: 'json',
            cache: false,
            success: function(data){
                loading.remove();
                if(data.result == 1) {
                    $('a.connect-' + username).each(function(){
                        $(this).removeClass('connect-action').
                            addClass('disconnect-action gradient-red').
                            attr('href', base_url + 'user/disconnect/' + username);

                        var icon = $(this).children('i');
                        if(icon.length)
                            icon.removeClass('connect-btn').addClass('disconnect-btn');
                        else
                            $(this).html(messages.js_disconnect);
                    });
                } else {
                    showSystemError();
                }
            }
        });

        return false;
    });

    // Disonnect from user
    $('a.disconnect-action').live('click', function(){
        var btn = $(this);

        var pattern = /^.*\/(.*)$/g;
        var href = btn.attr('href');
        var result = pattern.exec(href);
        var username = result[1];

        var loading = $('<div class="loading-btn"></div>');
        btn.append(loading);

        $.ajaxQueue({
            type: 'GET',
            url: href,
            dataType: 'json',
            cache: false,
            success: function(data){
                loading.remove();
                if(data.result == 1) {
                    $('a.connect-' + username).each(function(){
                        $(this).removeClass('disconnect-action gradient-red').
                            addClass('connect-action').
                            attr('href', base_url + 'user/connect/' + username);

                        var icon = $(this).children('i');
                        if(icon.length)
                            icon.removeClass('disconnect-btn').addClass('connect-btn');
                        else
                            $(this).html(messages.js_connect);
                    });
                } else {
                    showSystemError();
                }
            }
        });

        return false;
    });
    
    /*
     * Invite user
     */
    if(updates_right[0]){
        $('#user-invite-form').submit(function(){
            var form = $(this);
            var parent = form.closest('.updates-list-invite');
            var count = $('#updates-list-invite-count');

            var loading = $('<div class="loading-wrap"><div class="loading-inner"></div></div>');
            parent.append(loading);

            $.ajaxQueue({
                type: 'POST',
                url: form.attr('action'),
                data: form.serialize(),
                dataType: 'json',
                cache: false,
                success: function(data){
                    if(data.result == 1) {
                        form.children('input[type=text]').removeClass('error_box').val('');
                        $("#updates-list-invite-answer").html('').slideUp('fast');
                        count.html(parseInt(count.html()) - 1);
                        showStandardDialog(messages.js_invitation_sent, messages.js_invitation_msg);

                        if(parseInt(count.html()) == 1) {
                            parent.slideUp('fast').remove();
                        }
                    } else {
                        form.children('input[type=text]').addClass('error_box');
                        $("#updates-list-invite-answer").html(data.error).slideDown('fast');
                    }
                    loading.remove();
                },
                error: function(){
                    loading.remove();
                    showSystemError();
                }
            });

            return false;
        });
    }
    
    /*
     * Updates Share
     */
    // Begin - Updates Share
    // Hide/Show share buttons
    $("a.updates-item-share-btn").live('click', function(){
        var wrap = $(this).prev();
        wrap.animate({width: parseInt(wrap.css('width')) == 0?'108':'0'});

        return false;
    });

    // Open share popup - Remote share
    $("a.remote-share-action").live('click', function(){
        window.open($(this).attr('href'), '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=400,width=600');
        return false;
    });

    // Open share dialog - Local/Internal share
    $("a.local-share-action").live('click', function(){
        var html = '<div title="' + messages.js_share + '"></div>';
        
        var dialog_btns = {};
        dialog_btns[messages.js_share] = function() {
            var dialog = $(this);
            var form = $('#update-share-form form');
            var btn = form.children('input[type=submit]');
            btn.attr('disabled', true);
            var loading = $('<div class="loading-wrap"><div class="loading-inner"></div></div>').hide();

            loading.appendTo(form.parent().parent()).fadeIn('fast', function(){
                $.ajaxQueue({
                    type: 'POST',
                    url: form.attr('action'),
                    data: form.serialize(),
                    dataType: 'json',
                    cache: false,
                    success: function(data){
                        if(data.result == 1){
                            form.children('textarea').val('').removeClass('error_box');
                            $('#updates-list-share-answer').fadeOut('fast', function(){
                                $(this).html('');
                            });
                            dialog.dialog( "close" );
                        } else {
                            data.fields = jQuery.parseJSON(data.fields);
                            if(data.fields.comment != '')
                                form.children('textarea').addClass('error_box');

                            $('#updates-list-share-answer').html(data.html).fadeIn('fast');
                        }

                        btn.removeAttr('disabled');
                        loading.remove();
                    },
                    error: function(){
                        btn.removeAttr('disabled');
                        loading.remove();
                        showSystemError();
                    }
                });
            });
        }

        $(html).load($(this).attr('href')).
            dialog({
                resizable: false,
                width: 400,
                modal: true,
                buttons: dialog_btns,
                close: function(){
                    // Queue updates list refresh
                    $.ajaxQueue({
                        type: 'GET',
                        dataType: 'json',
                        url: base_url + 'ajax/get_new_updates_count/' + $('#updates-list-refresh-last').val() + '/1',
                        success: function(data){
                            if(data.count > 0)
                                $('#updates-list-refresh').html(data.html).slideDown('fast');
                            else
                                $('#updates-list-refresh').html('').slideUp('fast');

                            titleUpdatesCount(data.count);
                        }
                    });
                }
            });

        return false;
    });
    // End - Updates Share
    
    /*
     * Update Comments
     */
    // Expand comment box
    $("#comment-textarea").focus(function(){
        $(this).animate({'height':'4em'});
    });
    
    // Show comments
    $('a.view-comments-action').live('click', function(){
        var btn = $(this);
        var wrap = btn.parent();
        var comments = wrap.children('.updates-item-comments-wrap');

        // Check if comment form is exists
        var comment_form = wrap.children('.updates-item-comment-form');

        // Get update ID
        var pattern = /\/(\d+)(?!.*\d)/g;
        var href = btn.attr('href');
        var result = pattern.exec(href);

        var loading = $('<div class="loading-btn"></div>');
        btn.append(loading);

        if(comments.length) {
            // Comments already visible
            comments.fadeTo('fast', 0.5, function(){
                $.ajaxQueue({
                    type: 'GET',
                    url: base_url+'ajax/display_comment_list/'+result[1],
                    cache: false,
                    success: function(data){
                        loading.remove(); // Remove loading indicator
                        btn.updateCommentCount($(data).children().length); // Update comments btn count
                        comments.replaceWith(data); // Update comments content
                    },
                    error: function(){
                        loading.remove();
                        showSystemError();
                    }
                });
            });
            return false;
        }

        // Get comment list view
        $.ajaxQueue({
            type: 'GET',
            url: base_url+'ajax/display_comment_list/'+result[1],
            cache: false,
            success: function(data){
                loading.remove(); // Remove loading indicator

                var comment_list = $(data).hide();
                // Check if comment_form is there
                if(comment_form.length) 
                    comment_list.insertBefore(comment_form).fadeIn('fast');
                else
                    comment_list.appendTo(wrap).fadeIn('fast');

                btn.updateCommentCount($(data).children().length); // Update comments btn count
            },
            error: function(){
                loading.remove();
                showSystemError();
            }
        });

        return false;
    });

    // Show comment form
    $("a.comment-btn-click").live('click', function(){
        var btn = $(this);
        var wrap = btn.parent();

        if(btn.hasClass('selected')) {
            btn.removeClass('selected');
            wrap.children('.updates-item-comment-form').fadeOut('fast', function(){
                $(this).remove();
            });
        } else {
            var loading = $('<div class="loading-btn"></div>');
            btn.append(loading);

            var pattern = /\/(\d+)(?!.*\d)/g;
            var href = btn.attr('href');
            var result = pattern.exec(href);

            $.ajaxQueue({
                type: 'GET',
                url: base_url+'ajax/display_comment_form/'+result[1],
                cache: false,
                success: function(data){
                    btn.addClass('selected');
                    loading.remove();

                    $(data).insertNewHtml('appendTo', wrap);
                },
                error: function(){
                    loading.remove();
                    showSystemError();
                }
            });
        }

        return false;
    });
    
    // Share form submission
    $('#comment_form').live('submit', function(){
        var form = $(this);
        
        if(form.children('textarea').val() == '') {
            form.children('textarea').addClass('error_box');
            return false;
        } else {
            form.children('textarea').removeClass('error_box');
        }
        
        var answer = form.prev();
        var wrap = answer.parent().parent();
        var btn_comments = wrap.children('.updates-view-comments-btn');
        
        answer.html('<div class="loading"></div>').fadeIn('fast', function(){      
            
            $.ajaxQueue({
                type: 'POST',
                url: base_url + 'share/comment',
                data: form.serialize(),
                dataType: 'json',
                cache: false,
                success: function(data){
                    answer.fadeOut('fast', function(){
                        answer.html('');
                        if(data.result == 1) {
                            var comment_wrap = answer.parent().prev();

                            if(comment_wrap.hasClass('updates-item-comments-wrap')) {
                                $(data.html_single).hide().appendTo(comment_wrap).fadeIn('fast');
                            } else {
                                $(data.html).hide().insertBefore(answer.parent()).fadeIn('fast');
                            }
                            form.children('textarea').val('');

                            // Update comment count if button exists
                            if(btn_comments.length)
                                btn_comments.updateCommentCount(parseInt(btn_comments.html()) + 1);
                        } else {
                            $(data.html).hide().appendTo(answer.show()).fadeIn('fast');
                        }
                    });
                },
                error: function(){
                    showSystemError();
                }
            });
        });
        
        return false;
    });
    
    $('a.comment-remove-action').live('click', function(){
        var remove_btn = $(this);
        $.ajaxQueue({
            type: 'GET',
            url: remove_btn.attr('href'),
            dataType: 'json',
            cache: false,
            success: function(data){
                if(data.result == 1) {
                    var wrap = remove_btn.closest('.updates-list-item');
                    var btn_comments = wrap.children('.updates-view-comments-btn');
                    remove_btn.parent().fadeOut('fast', function(){
                        $(this).remove();
                        btn_comments.updateCommentCount(parseInt(btn_comments.html()) - 1);
                    });
                }
            },
            error: function(){
                showSystemError();
            }
        });
        
        return false;
    });
    
    /*
     * Notifications
     */
    var notifications_link = $('#notifications-link');
    if(notifications_link[0]) {
        refreshNotifications();

        notifications_link.click(function(){
            var btn = $(this);

            if(btn.hasClass('selected')) {
                btn.removeClass('selected');
                
                var window = $('#notifications-window');
                if(window.length > 0){
                    window.hide();
                    window.remove();
                }
            } else {
                var loading = $('<div class="loading-btn"></div>');
                btn.addClass('selected').append(loading);
                
                $.ajaxQueue({
                    type: 'GET',
                    url: btn.attr('href'),
                    cache: false,
                    success: function(data){
                        loading.remove();
                        $(data).hide().appendTo(btn.parent()).show();
                    },
                    error: function(){
                        loading.remove();
                        showSystemError();
                    }
                });
            }
            return false;
        });
        
        // Global click - close notification window
        $(document).click(function(e){
            var window = $('#notifications-window');
            if((window.has(e.target).length === 0) && 
                (e.target !== notifications_link) && 
                (notifications_link.hasClass('selected'))){
                notifications_link.removeClass('selected');
                
                if(window.length > 0){
                    window.hide();
                    window.remove();
                }
            }
        });
    }
    
    if(updates_right[0]) {
        $('#mark-all-read', updates_right).click(function(){
            $.ajaxQueue({
                type: 'GET',
                url: $(this).attr('href'),
                dataType: 'json',
                cache: false,
                success: function(data){
                    if(data.result == 1) {
                        $('div.updates-list-item.unread').removeClass('unread');
                        $('#notifications-link').html('Notifications');
                    }
                },
                error: function(){
                    showSystemError();
                }
            });

            return false;
        });
    }
    
    /*
     * Automatically move sidebar items
     */
    if(updates_right[0]) {
        var fixed_block = $('div.fixed-block', updates_right);
        if(fixed_block.length){
            var relative_block = $('div.relative-block', updates_right);
            $(window).scroll(function () {
                if($(window).scrollTop() > (relative_block.offset().top + relative_block.outerHeight() - $('#top-navigation').outerHeight()))
                    fixed_block.css('position', 'fixed').css('top', '32px');
                else
                    fixed_block.css('position', 'relative').css('top', '0');
            });
        }
    }
    
    /*
     * Menu
     */
    var menu_link = $('#top-navigation-menu-icon');
    if(menu_link[0]) {
        // Only click opens the menu
        menu_link.parent().removeClass('menu').find('ul').hide();
        
        menu_link.click(function(){
            var wrap = menu_link.parent();
            var window = $('#top-navigation-menu-window');
            if(wrap.hasClass('selected')) {
                window.hide();
                wrap.removeClass('selected');
            } else {
                wrap.addClass('selected');
                window.show();
            }
        });
        
        // Global click - close menu window
        $(document).click(function(e){
            var wrap = menu_link.parent();
            var window = $('#top-navigation-menu-window');
            if((window.has(e.target).length === 0) &&
                ($(e.target).attr('id') !== menu_link.attr('id')) && 
                (wrap.hasClass('selected'))){
                window.hide();
                wrap.removeClass('selected');
            }
        });
    }

});