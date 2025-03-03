# Product Customizer for WooCommerce (PCW)

- Author: [Oswaldo Cavalcante](https://oswaldocavalcante.com/)
- License: [GPLv2 or later](http://www.gnu.org/licenses/gpl-2.0.html)

## Description

Product Customizer for WooCommerce (PCW) is a powerful plugin that enhances the WooCommerce shopping experience by allowing customers to customize products before purchase. Key features include:

- Color selection for product parts
- Layer-based customization options
- Logo upload and positioning
- Real-time preview of customizations
- Saving and retrieving customizations

This plugin is perfect for businesses offering personalized products such as t-shirts, mugs, or any item that can be customized by the customer.

## Installation

1. Upload the plugin files to the `/wp-content/plugins/product-customizer` directory, or install the plugin directly from the WordPress plugins area.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Configure the plugin options on the WooCommerce settings page.

## Usage

1. After installation and activation, go to the product edit page in the WordPress admin.

2. Set up the customization options for your product:
   - Add background images for the product views
   - Define color options for the product
   - Create layers for different customizable parts of the product
   - Add options for each layer with associated images and colors

3. On the front-end product page, customers will see:
   - Color selection options
   - Layer-based customization menu
   - Upload areas for logos or custom designs

4. Customers can:
   - Select colors for different parts of the product
   - Choose options for each customizable layer
   - Upload and position logos or designs on the product
   - See a real-time preview of their customizations

5. The customizations are automatically saved and can be retrieved later.

6. When a customer adds the product to the cart, their customizations are saved with the order.

For developers who want to integrate with PCW:

```javascript
$(document).trigger('pcw_save_customizations');
```

This event can be used to trigger the saving of customizations programmatically.

To retrieve saved customizations on the server-side:

```php
$product_id = get_the_ID(); // Or however you're getting the product ID
$customizations = WC()->session->get("pcw_customizations_{$product_id}");
   
if (is_array($customizations) && isset($customizations['images'])) {
    $front_image = $customizations['images']['front'] ?? null;
    $back_image = $customizations['images']['back'] ?? null;
    
    // Use $front_image and $back_image as needed
}
```

You can also hook into the customization saving process:

```php
add_action('pcw_customizations_updated', 'your_function_name', 10, 2);
   
function your_function_name($customizations, $product_id) {
    // Your code here
    // $customizations is an array containing all saved customization data
    // $product_id is the ID of the product being customized
}
```

## Contributing

Contributions are welcome! Please read the CONTRIBUTING.md file for details on our code of conduct and the pull request process.

## How to Trigger Image Saving and Retrieve Them on the Server Side

For external plugins that need to interact with PCW, here are the steps to trigger image saving and retrieve the saved images on the server side:

1. Trigger image saving:

   To trigger the saving of customized images, you can use JavaScript to dispatch a custom event:

   ```javascript
   $(document).trigger('pcw_save_customizations');
   ```

   This will initiate the saving process for the current product customizations.

2. Retrieve saved images on the server side:

   After the customizations are saved, you can retrieve them using the following PHP code:

   ```php
   $product_id = get_the_ID(); // Or however you're getting the product ID
   $customizations = WC()->session->get("pcw_customizations_{$product_id}");
   
   if (is_array($customizations) && isset($customizations['images'])) {
       $front_image = $customizations['images']['front'] ?? null;
       $back_image = $customizations['images']['back'] ?? null;
       
       // Use $front_image and $back_image as needed
   }
   ```

3. Hook into the customization saving process:

   If you need to perform additional actions when customizations are saved, you can use the `pcw_customizations_updated` action hook:

   ```php
   add_action('pcw_customizations_updated', 'your_function_name', 10, 2);
   
   function your_function_name($customizations, $product_id) {
       // Your code here
       // $customizations is an array containing all saved customization data
       // $product_id is the ID of the product being customized
   }
   ```

By following these instructions, external plugins can easily integrate with PCW to trigger the saving of customized images and retrieve them for further processing or display.
