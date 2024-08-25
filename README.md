=== Plugin Name ===
Author: Oswaldo Cavalcante
Author URI: https://oswaldocavalcante.com/
Donate link: https://oswaldocavalcante.com/
Version: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

# Product Customizer for WooCommerce (PCW)

## Summary

Product Customizer for WooCommerce (PCW) is a powerful plugin that enhances the WooCommerce shopping experience by allowing customers to customize products before purchase. Key features include:

- Color selection for product parts
- Layer-based customization options
- Logo upload and positioning
- Real-time preview of customizations
- Saving and retrieving customizations

This plugin is perfect for businesses offering personalized products such as t-shirts, mugs, or any item that can be customized by the customer.

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