<div class="pcw-option">
    <div class="pcw-option-images">
        <div class="pcw-option-image">
            <label>Front Image</label>
            <input type="hidden" class="pcw_upload_image" name="pcw_option_image_front[<%= layerIndex %>][]" value="<%= imageFront %>" />
            <a class="pcw_button_upload_image button"><?php _e('Front image', 'pcw'); ?></a>
        </div>
        <div class="pcw-option-image">
            <label>Back Image</label>
            <input type="hidden" class="pcw_upload_image" name="pcw_option_image_back[<%= layerIndex %>][]" value="<%= imageBack %>" />
            <a class="pcw_button_upload_image button"><?php _e('Back image', 'pcw'); ?></a>
        </div>
    </div>
    <div class="pcw-option-inputs">
        <input type="text" class="option_name" name="pcw_option_name[<%= layerIndex %>][]" value="<%= name %>" placeholder="Nome" />
        <input type="text" class="option_cost" name="pcw_option_cost[<%= layerIndex %>][]" value="<%= cost %>" placeholder="Custo R$" />
    </div>
</div>