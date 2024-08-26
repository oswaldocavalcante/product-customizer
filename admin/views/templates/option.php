<div class="pcw-option pcw-innerbox" data-option-id="<%= optionId %>">
    <input type="hidden" name="pcw_option[<%= layerId %>][]" value="<%= optionId %>" />
    <div class="pcw-option-images">
        <div class="pcw-option-image woocommerce_variable_attributes">
            <label>Front Image</label>
            <input type="hidden" class="pcw_upload_image" name="pcw_option_image_front[<%= layerId %>][]" value="<%= imageFront %>" required />
            <a class="pcw_button_upload_image upload_image_button tips">
                <img src="<%= imageFront %>" class="pcw_uploaded_image" />
            </a>
        </div>
        <div class="pcw-option-image woocommerce_variable_attributes">
            <label>Back Image</label>
            <input type="hidden" class="pcw_upload_image" name="pcw_option_image_back[<%= layerId %>][]" value="<%= imageBack %>" required />
            <a class="pcw_button_upload_image upload_image_button tips">
                <img src="<%= imageBack %>" class="pcw_uploaded_image" />
            </a>
        </div>
    </div>
    <div class="pcw-option-inputs">
        <input type="text" class="option_name" name="pcw_option_name[<%= layerId %>][]" value="<%= name %>" placeholder="Name" required />
        <input type="text" class="option_cost" name="pcw_option_cost[<%= layerId %>][]" value="<%= cost %>" placeholder="Cost $" />
    </div>
    <div class="pcw-option-colors">
        <a class="pcw_button_add_option_color button"><%= addNewColor %></a>
        <%= optionColors %>
    </div>
    <div class="pcw_remove_option pcw-delete">
        <span class="pcw_button_remove_option pcw-button-delete"></span>
    </div>
</div>