<?php
    $optionName = '';
    $customizationIndex = 0;
?>

<div class="wc-metabox closed">
    <h3>
        <a href="#" class="remove_row delete"><?php _e('Remove', 'pcw'); ?></a>
        <div class="handlediv" aria-label="Click to toggle"><br></div>
        <strong><?php echo $optionName; ?></strong>
    </h3>
    <div class="wc-metabox-content hidden">
        <div class="data">
            <p class="upload_image">
                <label for="customization_image_${customizationIndex}"><?php _e('Option Image', 'pcw'); ?></label>
                <input type="hidden" class="option_image" name="customization_image[]" id="customization_image_${customizationIndex}" />
                <a class="upload_image_button button"><?php _e('Upload Image', 'pcw'); ?></a>
            </p>
            <p class="options_inputs">
                <label for="customization_name_${customizationIndex}"><?php _e('Option Name', 'pcw'); ?></label>
                <input type="text" class="option_name" name="customization_name[]" id="customization_name_${customizationIndex}" placeholder="Nome" />
                <label for="customization_cost_${customizationIndex}"><?php _e('Option Cost', 'pcw'); ?></label>
                <input type="text" class="option_name" name="customization_cost[]" id="customization_cost_${customizationIndex}" placeholder="Custo R$" />
            </p>
            <div class="image_preview"></div>
        </div>
    </div>
</div>