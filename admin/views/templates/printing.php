<div class="pcw_printing_method pcw-innerbox" data-printing-method-id="<%= id %>">
    <input type="text" name="pcw_printing_method_name[]" value="<%= name %>" placeholder="Method name" required />
    <input type="text" name="pcw_printing_method_cost[]" value="<%= cost %>" placeholder="Cost $" />
    <textarea name="pcw_printing_method_description[]" placeholder="(Optional) Description" rows="3"><%= description %></textarea>
    <div class="pcw-delete">
        <span class="pcw_button_delete_printing_method pcw-button-delete"></span>
    </div>
</div>