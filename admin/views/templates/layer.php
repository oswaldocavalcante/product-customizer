<div id="pcw_layer_<%= layerIndex %>" class="pcw_layer wc-metabox closed">
    <h3>
        <a href="#" class="remove_row delete">Remove</a>
        <div class="handlediv" aria-label="Click to toggle"><br></div>
        <strong><%= layerName %></strong>
        <input type="hidden" name="pcw_layer[<%= layerIndex %>]" value="<%= layerName %>" />
    </h3>
    <div class="wc-metabox-content">
        <a class="pcw_button_add_option button">New option</a>
        <div id="pcw_layer_options_<%= layerIndex %>" class="pcw_layer_options"><%= layerOptions %></div>
    </div>
</div>