jQuery(document).ready(function($) {

    // Handler for Install & Update
    $('.mrcode-btn-install').on('click', function(e) {
        e.preventDefault();
        var $btn = $(this);
        performAction($btn, 'mrcode_install_addon', mrcode_vars.installing);
    });

    // Handler for Activation Only
    $('.mrcode-btn-activate').on('click', function(e) {
        e.preventDefault();
        var $btn = $(this);
        performAction($btn, 'mrcode_activate_addon', mrcode_vars.activating);
    });

    // Common Function
    function performAction($btn, actionName, loadingText) {
        var url = $btn.data('url');
        var mainFile = $btn.data('mainfile');

        $btn.prop('disabled', true).css('opacity', '0.7').text(loadingText);

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: actionName,
                download_url: url,
                main_file: mainFile,
                security: mrcode_vars.nonce
            },
            success: function(res) {
                if(res.success) {
                    $btn.removeClass('btn-install btn-update btn-activate')
                        .addClass('btn-active-static')
                        .css({'background-color': '#00a32a', 'opacity': '1', 'color': '#fff', 'border': 'none'})
                        .html('<span class="dashicons dashicons-yes"></span> ' + mrcode_vars.success);

                    setTimeout(function(){ location.reload(); }, 1000);
                } else {
                    alert(mrcode_vars.error + '\n' + res.data.message);
                    $btn.prop('disabled', false).css('opacity', '1').text('Retry');
                }
            },
            error: function() {
                alert('Server connection error.');
                $btn.prop('disabled', false).css('opacity', '1');
            }
        });
    }

});
