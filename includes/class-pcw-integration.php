<?php

class PCW_Integration extends WC_Integration
{
    public $id;
    public $method_title;
    public $method_description;
    public $form_fields;
    public $settings;

    public function __construct()
    {
        $this->id = 'product_customizer';
        $this->method_title = __('Product Customizer', 'pcw');
        $this->method_description = __('Configure o módulo de personalização de produtos.', 'pcw');

        $this->init_form_fields();
        $this->init_settings();

        add_action('woocommerce_update_options_integration_' . $this->id, array($this, 'process_admin_options'));
    }

    public function init_form_fields()
    {
        $this->form_fields = array(
            'pcw-settings-background' => array(
                'title'         => __('Background padrão', 'pcw'),
                'type'          => 'text',
                'placeholder'   => 'https://...',
                'description'   => __('O URL da imagem a ser usada como background padrão para personalização de produtos.', 'gestaoclick'),
            ),
            'pcw-settings-colors' => array(
                'title'         => __('Cores padrão', 'pcw'),
                'type'          => 'textarea',
                'placeholder'   => 'Vermelho, #FF0000',
                'description'   => __('Escreva cada cor em uma linha: começando pelo nome da cor seguido do código hexadecimal da cor separados por vírgula.', 'gestaoclick'),
            ),
            'pcw-settings-printings' => array(
                'title'         => __('Métodos de impressão padrão', 'pcw'),
                'placeholder'   => __('Serigrafia, 5.00', 'pcw'),
                'type'          => 'textarea',
                'description'   => __('Escreva cada método de impressão em uma linha: começando pelo nome seguido do valor separados por vírgula. O valor deve usar ponto para separar as casas decimais.', 'gestaoclick'),
            ),
            'pcw-settings-disclaimer' => array(
                'title'         => __('Observação padrão', 'pcw'),
                'type'          => 'textarea',
                'description'   => __('Escreva uma observação padrão para ser exposta em todos os produtos personalizáveis. Essa observação pode ser sobrescrita individualmente na página de edição de cada produto.', 'gestaoclick'),
            ),
            'pcw-settings-notes' => array(
                'title'         => __('Observações dos clientes', 'pcw'),
                'type'          => 'checkbox',
                'label'         => __('Permitir observações dos clientes.', 'pcw'),
                'default'       => 'no',
                'description'   => __('Habilite para permitir, por padrão, que os clientes escrevam observações adicionais nos produtos personalizáveis.', 'gestaoclick'),
            ),
        );
    }

    public function admin_options()
    {
        update_option('pcw-settings-background',    $this->settings['pcw-settings-background']);
        update_option('pcw-settings-disclaimer',    $this->settings['pcw-settings-disclaimer']);
        update_option('pcw-settings-notes',         $this->settings['pcw-settings-notes']);
        update_option('pcw-settings-colors',        $this->get_formatted_option('pcw-settings-colors', 'name', 'value'));
        update_option('pcw-settings-printings',     $this->get_formatted_option('pcw-settings-printings', 'name', 'cost'));

        echo '<div>';
        echo '<h2>' . esc_html($this->get_method_title()) . '</h2>';
        echo wp_kses_post(wpautop($this->get_method_description()));
        echo '<div><input type="hidden" name="section" value="' . esc_attr($this->id) . '" /></div>';
        echo '<table class="form-table">' . $this->generate_settings_html($this->get_form_fields(), false) . '</table>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '</div>';
    }

    public function get_formatted_option($field, $key_name, $value_name)
    {
        if (!$this->settings[$field])
        {
            return null;
        }

        $option_lines = explode(PHP_EOL, $this->settings[$field]);
        $formatted_option = array();

        foreach ($option_lines as $option)
        {
            list($name, $value) = array_map('trim', explode(',', $option));
            $formatted_option[] = array
            (
                'id'        => uniqid('default_', true),
                $key_name   => $name, 
                $value_name => $value
            );
        }

        return $formatted_option;
    }
}
