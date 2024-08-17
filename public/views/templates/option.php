<div class="pcw_option" data-option-id="<%= optionId %>">
    <p><%= optionName %></p>
    <p>R$ <%= optionCost %></p>
    <div class="pcw_option_colors"><%= optionColors %></div>
    <div class="pcw_optios_images">
        <img class="pcw_image pcw_image_front" image-front-id="<%= optionId %>" src="<%= imageFront %>" style="display: none" />
        <img class="pcw_image pcw_image_back" image-back-id="<%= optionId %>" src="<%= imageBack %>" style="display: none" />
    </div>
</div>