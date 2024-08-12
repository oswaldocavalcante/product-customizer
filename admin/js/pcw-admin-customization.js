jQuery(document).ready(function ($) {

    $('#pwc_button_add_color').on('click', function () {
        var colorValue  = $('#pwc_new_color_value').val();
        var colorName   = $('#pwc_new_color_name').val();
        var colorIndex  = $('#pwc_color_display').length;

        if(!colorValue || !colorName) {
            alert('Please insert a name and hexadecimal color value.');
            return;
        }

        var newColorOption = `
            <div class="gcw_color_display" style="background-color:${colorValue}; width: 50px; height: 50px;">
                <input type="text" name="pcw_color_value[]" id="pcw_color_value_${colorIndex}" value="${colorValue}"/>
                <input type="text" name="pcw_color_name[]" id="pcw_color_name_${colorIndex}" value="${colorName}"/>
            </div>
        `;

        $('#pwc_colors_container').append(newColorOption);
    });
    
    // Adicionar nova opção de personalização
    $('#pwc_button_add_option').on('click', function () {
        var optionName = $('#pwc_new_option_name').val().trim();

        if (optionName === '') {
            alert('<?php _e("Please enter a name for the option.", "pcw"); ?>');
            return;
        }

        var customizationIndex = $('#customization_options').length;

        var newOption = `
            <div class="wc-metabox closed">
                <h3>
                    <a href="#" class="remove_row delete"><?php _e('Remove', 'pcw'); ?></a>
                    <div class="handlediv" aria-label="Click to toggle"><br></div>
                    <strong>${optionName}</strong>
                </h3>
                <div class="wc-metabox-content hidden">
                    <div class="data">
                        <p class="upload_image">
                            <label for="pwc_option_image_${customizationIndex}"><?php _e('Option Image', 'pcw'); ?></label>
                            <input type="hidden" class="pcw_image" name="pwc_option_image[]" id="pwc_option_image_${customizationIndex}" />
                            <a class="pcw_button_upload_image button"><?php _e('Upload Image', 'pcw'); ?></a>
                        </p>
                        <p class="options_inputs">
                            <label for="pwc_option_name_${customizationIndex}"><?php _e('Option Name', 'pcw'); ?></label>
                            <input type="text" class="option_name" name="pwc_option_name[]" id="pwc_option_name_${customizationIndex}" placeholder="Nome" />
                            <label for="pwc_option_cost_${customizationIndex}"><?php _e('Option Cost', 'pcw'); ?></label>
                            <input type="text" class="option_name" name="pwc_option_cost[]" id="pwc_option_cost_${customizationIndex}" placeholder="Custo R$" />
                        </p>
                        <div class="image_preview"></div>
                    </div>
                </div>
            </div>`;

        $('#customization_options').append(newOption);

        // Limpar o campo de entrada após adicionar a nova opção
        $('#pwc_new_option_name').val('');
    });

    // Remover opção de personalização
    $(document).on('click', '.remove_row.delete', function (e) {
        e.preventDefault();
        $(this).closest('.woocommerce_variation').remove();
    });

    // Ação para carregar imagem
    var frame;
    $(document).on('click', '.pcw_button_upload_image', function (e) {
        e.preventDefault();

        var $button = $(this);
        var $input = $button.siblings('.pcw_image');
        var $preview = $button.siblings('.image_preview');

        // Cria o media frame
        if (frame) {
            frame.open();
            return;
        }

        frame = wp.media({
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
            $preview.html('<img src="' + attachment.url + '" style="max-width: 100px;">');
        });

        // Abre o modal de media
        frame.open();
    });
});