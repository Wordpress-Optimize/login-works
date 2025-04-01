jQuery(document).ready(function ($) {
    let mediaUploader;

    $('#upload_logo_button').click(function (e) {
        e.preventDefault();

        // If the uploader object has already been created, reopen it.
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        // Create a new media uploader.
        mediaUploader = wp.media({
            title: 'Select Logo',
            button: {
                text: 'Use this logo'
            },
            multiple: false
        });

        // When an image is selected, run a callback.
        mediaUploader.on('select', function () {
            const attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#login_works_logo').val(attachment.url);
            $('#logo_preview').html('<img src="' + attachment.url + '" alt="Logo Preview" style="max-width: 200px; height: auto;" />');
            $('#remove_logo_button').show();
        });

        // Open the uploader dialog.
        mediaUploader.open();
    });

    $('#remove_logo_button').click(function (e) {
        e.preventDefault();
        $('#login_works_logo').val('');
        $('#logo_preview').html('');
        $(this).hide();
    });
});