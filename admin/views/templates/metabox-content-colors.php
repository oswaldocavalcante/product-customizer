<?php
// Aqui você pode carregar as opções salvas e criar uma div para cada uma
$colors = get_post_meta(get_the_ID(), 'pcw_colors', true);

if (!empty($colors) && is_array($colors)) :
    foreach ($colors as $key => $color) :
        ?>
        <div class="pcw_color_display" style="background-color:<?php echo esc_attr($color['value']); ?>; width: 50px; height: 50px;">
            <input type="text" name="pcw_color_value[]" id="pcw_color_value_<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($color['value']); ?>" />
            <input type="text" name="pcw_color_name[]" id="pcw_color_name_<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($color['name']) ?>" />
        </div>
        <?php
    endforeach;
endif; ?>