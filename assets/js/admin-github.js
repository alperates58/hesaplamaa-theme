(function($) {
    'use strict';

    $(function() {
        $('#htheme-check-version').on('click', function() {
            var $result = $('#htheme-version-result');

            $result.text(hthemeGithub.checking);

            $.post(hthemeGithub.ajaxurl, {
                action: 'htheme_check_github_version',
                nonce: hthemeGithub.nonce
            }).done(function(response) {
                if (response && response.success && response.data && response.data.sha) {
                    $result.text('Son commit: ' + response.data.sha);
                    return;
                }

                if (response && response.data && response.data.message) {
                    $result.text(response.data.message);
                    return;
                }

                $result.text(hthemeGithub.norepo);
            }).fail(function(xhr) {
                var response = xhr.responseJSON || {};
                var data = response.data || {};

                $result.text(data.message || hthemeGithub.failed);
            });
        });
    });
})(jQuery);
