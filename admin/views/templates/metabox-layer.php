<?php

$options = get_post_meta(get_the_ID(), 'pcw_customizations', true);

if (!empty($options) && is_array($options)) :
    foreach ($options as $key => $option) :
        ?>
            <div class="wc-metabox closed">
                <h3>
                    <a href="#" class="remove_row delete"><?php _e('Remove', 'pcw'); ?></a>
                    <div class="handlediv" aria-label="Click to toggle"><br></div>
                    <strong><?php echo esc_html($option['name']); ?></strong>
                </h3>
                <div class="wc-metabox-content hidden">
                    <div class="pcw_option data">
                        <p class="upload_image">
                            <input type="hidden" class="option_image" name="pwc_option_image[]" id="pwc_option_image_<?php echo $key; ?>" value="<?php echo esc_attr($option['image']); ?>" />
                            <a class="pcw_button_upload_image button">
                                <?php if ($option['image']) : ?>
                                    <img src="<?php echo esc_url($option['image']); ?>" style="max-width: 100px;">
                                <?php else: _e('Upload Image', 'pcw');
                                endif; ?>
                            </a>
                        </p>
                        <p class="options_inputs">
                            <input type="text" class="option_name" name="pcw_option_name[]" id="pcw_option_name_<?php echo $key; ?>" value="<?php echo esc_attr($option['name']); ?>" placeholder="<?php _e('Option Name', 'pcw'); ?>" />
                            <input type="text" class="option_name" name="pcw_option_cost[]" id="pcw_option_cost_<?php echo $key; ?>" value="<?php echo esc_attr($option['cost']); ?>" placeholder="<?php _e('Option Cost', 'pcw'); ?>" />
                        </p>
                    </div>
                </div>
            </div>
        <?php
    endforeach;
endif; ?>