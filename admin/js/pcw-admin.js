jQuery(document).ready(function ($) 
{
    function generateUniqueId() {
        return Math.random().toString(36);
    }

    $(document).on('click', '#pcw_button_add_color', function () 
    {
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

    $(document).on('click', '#pcw_button_add_printing_method', function () 
    {
        var template = $('#pcw_printing_method_template').html();
        var newPrintingMethod = template
            .replace(/<%= id %>/g, generateUniqueId())
            .replace(/<%= name %>/g, '')
            .replace(/<%= cost %>/g, '')
            .replace(/<%= description %>/g, '')
        ;

        $('#pcw-metabox-content-printing-methods').append(newPrintingMethod);
    });
    
    $('#pwc_button_add_layer').on('click', function () 
    {
        var layerName = $('#pwc_new_option_name').val().trim();

        if (layerName === '') {
            alert('Please enter a name for the layer.');
            return;
        }

        var template = $('#pcw_layer_template').html();
        var newLayer = template
            .replace(/<%= layerId %>/g, generateUniqueId())
            .replace(/<%= layerName %>/g, layerName)
            .replace(/<%= layerOptions %>/g, '')
        ;

        $('#pcw_metabox_layers').append(newLayer);
        $('#pwc_new_option_name').val('');
    });

    $(document).on('click', '.pcw_button_add_option', function ()
    {
        var layerId = $(this).closest('.pcw_layer').data('layer-id');
        var template = $('#pcw_option_template').html();
        var newOption = template
            .replace(/<%= layerId %>/g, layerId)
            .replace(/<%= optionId %>/g, generateUniqueId())
            .replace(/<%= imageFront %>/g, '')
            .replace(/<%= imageBack %>/g, '')
            .replace(/<%= name %>/g, '')
            .replace(/<%= cost %>/g, '')
            .replace(/<%= addNewColor %>/g, 'Add new color')
            .replace(/<%= optionColors %>/g, '')
        ;

        var layerOptions = $(this).siblings('.pcw_metabox_options');
        layerOptions.append(newOption);
    });

    $(document).on('click', '.pcw_button_add_option_color', function()
    {
        var optionId = $(this).closest('.pcw-option').data('option-id');
        var template = $('#pcw_option_color_template').html();
        var newOptionColor = template
            .replace(/<%= optionId %>/g, optionId)
            .replace(/<%= optionColorId %>/g, generateUniqueId())
            .replace(/<%= optionColorValue %>/g, '')
            .replace(/<%= optionColorName %>/g, '')
        ;

        var optionColors = $(this).closest('.pcw-option-colors');
        optionColors.append(newOptionColor);
    });

    // Ação para carregar imagem
    $(document).on('click', '.pcw_button_upload_image', function (e) 
    {
        e.preventDefault();

        var $button = $(this);
        var $input = $button.siblings('.pcw_upload_image');

        if ($input.val()) 
        {
            $input.val('');
            $button.html('');
            $button.removeClass('remove');
            return;
        }

        // Cria o media frame
        if (frame) 
        {
            frame.open();
            return;
        }

        var frame = wp.media
        ({
            title: 'Select or Upload an Image',
            button: {
                text: 'Use this image'
            },
            multiple: false
        });

        // Quando uma imagem é selecionada
        frame.on('select', function () 
        {
            var attachment = frame.state().get('selection').first().toJSON();
            $input.val(attachment.url);
            $button.html('<img src="' + attachment.url + '" class="pcw_uploaded_image" style="display: block">');
            $button.addClass('remove');
        });

        // Abre o modal de media
        frame.open();
    });

    $(document).ready(function () {
        // Itera por cada input dentro de .pcw-option
        $('.pcw-option').each(function () {
            // Para cada input encontrado, verifica se tem valor
            $(this).find('input').each(function () {
                if ($(this).val() !== '') {
                    // Se o input tiver valor, adiciona a classe 'remove' à tag <a> correspondente
                    $(this).siblings('a').addClass('remove');
                }
            });
        });
    });

    $(document).on('click', '.pcw_button_remove_color', function (e) {
        e.preventDefault();

        if (confirm('Are you sure you want to delete this color?')) {
            var postId = $('#post_ID').val();
            var $color = $(this).closest('.pcw_color');
            var colorId = $color.data('color-id');

            $.ajax({
                url: pcw_ajax_object.url,
                type: 'POST',
                data: {
                    action: 'pcw_delete_color',
                    nonce: pcw_ajax_object.nonce,
                    post_id: postId,
                    color_id: colorId
                },
                success: function (response) {
                    $color.remove();
                },
                error: function (xhr, status, error) {
                    alert('Failed to delete color.');
                }
            });
        }
    });

    $(document).on('click', '.pcw_button_delete_printing_method', function (e) {
        e.preventDefault();

        if (confirm('Are you sure you want to delete this printing method?')) {
            var postId = $('#post_ID').val();
            var $printingMethod = $(this).closest('.pcw_printing_method');
            var printingMethodId = $printingMethod.data('printing-method-id');

            $.ajax({
                url: pcw_ajax_object.url,
                type: 'POST',
                data: {
                    action: 'pcw_delete_printing_method',
                    nonce: pcw_ajax_object.nonce,
                    post_id: postId,
                    printing_method_id: printingMethodId
                },
                success: function (response) {
                    $printingMethod.remove();
                },
                error: function (xhr, status, error) {
                    alert('Failed to delete printing method.');
                }
            });
        }
    });

    $(document).on('click', '.pcw_button_remove_layer', function (e) {
        e.preventDefault();

        if (confirm('Are you sure you want to delete this layer?')) {
            var postId = $('#post_ID').val();
            var $layer = $(this).closest('.pcw_layer');
            var layerId = $layer.data('layer-id');

            $.ajax({
                url: pcw_ajax_object.url,
                type: 'POST',
                data: {
                    action: 'pcw_delete_layer',
                    nonce: pcw_ajax_object.nonce,
                    post_id: postId,
                    layer_id: layerId
                },
                success: function (response) {
                    $layer.remove();
                },
                error: function (xhr, status, error) {
                    alert('Failed to delete layer.');
                }
            });
        }
    });

    $(document).on('click', '.pcw_button_remove_option', function (e) 
    {
        e.preventDefault();

        if (confirm('Are you sure you want to delete this option?')) 
        {
            var postId = $('#post_ID').val();
            var $option = $(this).closest('.pcw-option');
            var optionId = $option.data('option-id');

            $.ajax
            ({
                url: pcw_ajax_object.url,
                type: 'POST',
                data: 
                {
                    action: 'pcw_delete_option',
                    nonce: pcw_ajax_object.nonce,
                    post_id: postId,
                    option_id: optionId
                },
                success: function (response) {
                    $option.remove();
                },
                error: function (xhr, status, error) {
                    alert('Failed to delete option.');
                }
            });
        }
    });

    $(document).on('click', '.pcw_button_remove_option_color', function (e) 
    {
        e.preventDefault();

        if (confirm('Are you sure you want to delete this option color?')) 
        {
            var postId          = $('#post_ID').val();
            var $option         = $(this).closest('.pcw-option');
            var optionId        = $option.data('option-id');
            var $optionColor    = $(this).closest('.pcw_option_color');
            var optionColorId   = $optionColor.data('option-color-id');

            $.ajax
            ({
                url: pcw_ajax_object.url,
                type: 'POST',
                data:
                {
                    action: 'pcw_delete_option_color',
                    nonce: pcw_ajax_object.nonce,
                    post_id: postId,
                    option_id: optionId,
                    option_color_id: optionColorId,
                },
                success: function (response) {
                    $optionColor.remove();
                },
                error: function (xhr, status, error) {
                    alert('Failed to delete option.');
                }
            });
        }
    });
});