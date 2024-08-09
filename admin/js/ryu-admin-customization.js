jQuery(document).ready(function ($) {
    var frame;
    // Adicionar nova opção de personalização
    $('.add_customization_option').on('click', function () {
        var optionName = $('#new_customization_name').val().trim();

        if (optionName === '') {
            alert('<?php _e("Please enter a name for the option.", "ryu"); ?>');
            return;
        }

        var customizationIndex = $('#customization_options').length;

        var newOption = `
            <div class="wc-metabox closed">
                <h3>
                    <a href="#" class="remove_row delete"><?php _e('Remove', 'ryu'); ?></a>
                    <div class="handlediv" aria-label="Click to toggle"><br></div>
                    <strong>${optionName}</strong>
                </h3>
                <div class="wc-metabox-content hidden">
                    <div class="data">
                        <p class="upload_image">
                            <label for="customization_image_${customizationIndex}"><?php _e('Option Image', 'ryu'); ?></label>
                            <input type="hidden" class="option_image" name="customization_image[]" id="customization_image_${customizationIndex}" />
                            <a class="upload_image_button button"><?php _e('Upload Image', 'ryu'); ?></a>
                        </p>
                        <p class="options_inputs">
                            <label for="customization_name_${customizationIndex}"><?php _e('Option Name', 'ryu'); ?></label>
                            <input type="text" class="option_name" name="customization_name[]" id="customization_name_${customizationIndex}" placeholder="Nome" />
                            <label for="customization_cost_${customizationIndex}"><?php _e('Option Cost', 'ryu'); ?></label>
                            <input type="text" class="option_name" name="customization_cost[]" id="customization_cost_${customizationIndex}" placeholder="Custo" />
                        </p>
                        <div class="image_preview"></div>
                    </div>
                </div>
            </div>`;

        $('#customization_options').append(newOption);

        // Limpar o campo de entrada após adicionar a nova opção
        $('#new_customization_name').val('');
    });

    // Remover opção de personalização
    $(document).on('click', '.remove_row.delete', function (e) {
        e.preventDefault();
        $(this).closest('.woocommerce_variation').remove();
    });

    // Ação para carregar imagem
    $(document).on('click', '.upload_image_button', function (e) {
        e.preventDefault();

        var $button = $(this);
        var $input = $button.siblings('.option_image');
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