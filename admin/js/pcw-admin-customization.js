jQuery(document).ready(function ($) {
    
    $('#pwc_button_add_layer').on('click', function () {
        var layerName = $('#pwc_new_option_name').val().trim();

        if (layerName === '') {
            alert('Please enter a name for the layer.');
            return;
        }

        var layerIndex = $('.pcw_layer').length;
        var template = $('#pcw_layer_template').html();
        var newLayer = template
            .replace(/<%= layerIndex %>/g, layerIndex)
            .replace(/<%= layerName %>/g, layerName)
            .replace(/<%= layerOptions %>/g, '')
        ;

        $('#pcw_metabox_layers').append(newLayer);
        $('#pwc_new_option_name').val('');
    });

    $(document).on('click', '.pcw_button_add_option', function () {
        var parentMetabox = $(this).closest('.wc-metabox');
        var layerIndex = $('.pcw_layer').index(parentMetabox);
        var template = $('#pcw_option_template').html();
        var newOption = template
            .replace(/<%= layerIndex %>/g, layerIndex)
            .replace(/<%= imageFront %>/g, '')
            .replace(/<%= imageBack %>/g, '')
            .replace(/<%= name %>/g, '')
            .replace(/<%= cost %>/g, '')
        ;

        $(`#pcw_layer_options_${layerIndex}`).append(newOption);
    });

    $(document).on('click', '#pcw_button_add_color', function () {
        var colorValue = $('#pcw_new_color_value').val();
        var colorName = $('#pcw_new_color_name').val();
        var template = $('#pcw_color_template').html();

        if (!colorValue || !colorName) {
            alert('Please insert a name and hexadecimal color value.');
            return;
        }

        var newColor = template
            .replace(/<%= colorValue %>/g, colorValue)
            .replace(/<%= colorName %>/g, colorName)
        ;

        $('#pcw-metabox-content-colors').append(newColor);
    });

    // Remover opção de personalização
    $(document).on('click', '.remove_row.delete', function (e) {
        e.preventDefault();
        $(this).closest('.woocommerce_variation').remove();
    });

    // Ação para carregar imagem
    $(document).on('click', '.pcw_button_upload_image', function (e) {
        e.preventDefault();

        var $button = $(this);
        var $input = $button.siblings('.pcw_upload_image');

        if ($input.val()) {
            $input.val('');
            $button.html('');
            $button.removeClass('remove');
            return;
        }

        // Cria o media frame
        if (frame) {
            frame.open();
            return;
        }

        var frame = wp.media({
            title: 'Select or Upload an Image',
            button: {
                text: 'Use this image'
            },
            multiple: false
        });

        // Quando uma imagem é selecionada
        frame.on('select', function () {
            var attachment = frame.state().get('selection').first().toJSON();
            $input.val(attachment.url);
            $button.html('<img src="' + attachment.url + '" class="pcw_uploaded_image" style="display: block">');
            $button.addClass('remove');
        });

        // Abre o modal de media
        frame.open();
    });
});