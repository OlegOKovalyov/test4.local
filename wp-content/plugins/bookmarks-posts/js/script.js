jQuery(function($) {

    /**
     * Submit or delete favorite
     */
    $('.bmp-bookmark-link').on('click', function(event) {
        event.preventDefault();

        var $self = $(this);

        var data = {
            post_id: $self.data('id'),
            nonce: bmp.nonce,
            action: 'bmp_action'
        };

        $self.addClass('bmp-loader');

        $.post(bmp.ajaxurl, data, function(res) {

            if (res.success) {
                $self.html(res.data);

            } else {
                alert(bmp.errorMessage);
            }

            // remove loader
            $self.removeClass('bmp-loader');
        }); // $.post()
    }); // on()

    /**
     * delete favorite
     */
    $('.bmp-remove-bookmark').on('click', function(event) {
        event.preventDefault();

        var $self = $(this);

        var data = {
            post_id: $self.data('id'),
            nonce: bmp.nonce,
            action: 'bmp_action'
        };

        $self.addClass('bmp-loader');

        $.post(bmp.ajaxurl, data, function(res) {

            if (res.success) {
                window.location.reload();

            } else {
                alert(bmp.errorMessage);
            }

            // remove loader
            $self.removeClass('bmp-loader');
        }); // $.post()
    }); // on()
});