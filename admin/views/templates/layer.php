<div data-layer-id="<%= layerId %>" class="pcw_layer wc-metabox closed">
    <h3>
        <a href="#" class="pcw_button_remove_layer remove_row delete">Remove</a>
        <div class="handlediv" aria-label="Click to toggle"><br></div>
        <strong><%= layerName %></strong>
        <input type="hidden" name="pcw_layer[<%= layerId %>]" value="<%= layerName %>" />
    </h3>
    <div class="wc-metabox-content">
        <a class="pcw_button_add_option button">New option</a>
        <div class="pcw_layer_options"><%= layerOptions %></div>
    </div>
</div>