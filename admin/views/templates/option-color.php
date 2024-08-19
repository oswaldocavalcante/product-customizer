<div class="pcw_option_color" data-option-color-id="<%= optionColorId %>" style="background-color: <%= optionColorValue %>">
    <input type="hidden" name="pcw_option_color[<%= optionId %>][]" value="<%= optionColorId %>" />
    <input type="text" name="pcw_option_color_value[<%= optionId %>][]" value="<%= optionColorValue %>" placeholder="Color value" />
    <input type="text" name="pcw_option_color_name[<%= optionId %>][]" value="<%= optionColorName %>" placeholder="Color name" />
    <div class="pcw_remove_option_color">
        <span class="pcw_button_remove_option_color"></span>
    </div>
</div>