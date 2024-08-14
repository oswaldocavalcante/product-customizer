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
            .replace(/<%= layerName %>/g, layerName);

        $('#pcw_metabox_layers').append(newLayer);
        $('#pwc_new_option_name').val('');
    });

    $(document).on('click', '.pcw_button_add_option', function () {
        var parentMetabox = $(this).closest('.wc-metabox'); // Identificar a camada (metabox) onde o botão foi clicado
        var layerIndex = $('.pcw_layer').index(parentMetabox); // Obter o índice da camada
        var template = $('#pcw_option_template').html();
        var newOption = template
            .replace(/<%= layerIndex %>/g, layerIndex)
            .replace(/<%= imageFront %>/g, '')
            .replace(/<%= imageBack %>/g, '')
            .replace(/<%= name %>/g, '')
            .replace(/<%= cost %>/g, '');
            
        $(`#pcw_layer_options_${layerIndex}`).append(newOption);
    });

    // $(document).on('click', '.pcw_button_add_option', function(){
    //     var parentMetabox = $(this).closest('.wc-metabox'); // Identificar a camada (metabox) onde o botão foi clicado
    //     var layerIndex = $('.pcw_layer').index(parentMetabox); // Obter o índice da camada
    //     newOption(layerIndex);
    // });

    function newOption(layerIndex){
        var optionIndex = $(`#pcw_layer_options_${layerIndex}`).children().length;
        var newOption = `
            <div id="pcw_option_${optionIndex}" class="pcw-option">
                <div class="pcw-option-images">
                    <div class="pcw-option-image">
                        <label>Front Image</label>
                        <input type="hidden" class="pcw_upload_image" name="pcw_option_image_front[${layerIndex}][]" id="pcw_${layerIndex}_option_image_front_${optionIndex}" />
                        <a class="pcw_button_upload_image button"><?php _e('Front image', 'pcw'); ?></a>
                    </div>
                    <div class="pcw-option-image">
                        <label>Back Image</label>
                        <input type="hidden" class="pcw_upload_image" name="pcw_option_image_back[${layerIndex}][]" id="pcw_${layerIndex}_option_image_back_${optionIndex}" />
                        <a class="pcw_button_upload_image button"><?php _e('Back image', 'pcw'); ?></a>
                    </div>
                </div>
                <div class="pcw-option-inputs">
                    <input type="text" class="option_name" name="pcw_option_name[${layerIndex}][]" id="pcw_${layerIndex}_option_name_${optionIndex}" placeholder="Nome" />
                    <input type="text" class="option_cost" name="pcw_option_cost[${layerIndex}][]" id="pcw_${layerIndex}_option_cost_${optionIndex}" placeholder="Custo R$" />
                </div>
            </div>
        `;

        $(`#pcw_layer_options_${layerIndex}`).append(newOption);
    }

    $('#pwc_button_add_color').on('click', function () {
        var colorValue = $('#pwc_new_color_value').val();
        var colorName = $('#pwc_new_color_name').val();
        var colorIndex = $('#pwc_color_display').length;

        if (!colorValue || !colorName) {
            alert('Please insert a name and hexadecimal color value.');
            return;
        }

        var newColor = `
            <div class="pcw_color_display" style="background-color:${colorValue}; width: 50px; height: 50px;">
                <input type="text" name="pcw_color_value[]" id="pcw_color_value_${colorIndex}" value="${colorValue}"/>
                <input type="text" name="pcw_color_name[]" id="pcw_color_name_${colorIndex}" value="${colorName}"/>
            </div>
        `;

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
            console.log($input);
            // $preview.html('<img src="' + attachment.url + '" style="max-width: 100px;">');
        });

        // Abre o modal de media
        frame.open();
    });
});