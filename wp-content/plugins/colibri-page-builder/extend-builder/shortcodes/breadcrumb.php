<?php
namespace ExtendBuilder;
add_shortcode('mesmerize_breadcrumb_element', '\ExtendBuilder\mesmerize_breadcrumb_element_shortcode');

$mesmerize_breadcrumb_index = 0;

function mesmerize_breadcrumb_element_shortcode($atts)
{

    $mesmerize_breadcrumb_index = intval(get_theme_mod('mesmerize_breadcrumb_element_index', 0));
    set_theme_mod('mesmerize_breadcrumb_element_index', $mesmerize_breadcrumb_index === PHP_INT_MAX ? 0 : $mesmerize_breadcrumb_index + 1);
    $atts = shortcode_atts(
        array(
            'id'                           => 'ope-breadcrumb-' . ($mesmerize_breadcrumb_index),
            'separator-symbol'             => 'slash'
        ),
        $atts
    );

    $breadcrumbSeparatorSymbol = getSeparatorSymbol($atts['separator-symbol']);


    ob_start();

    ?>
    <div class="<?=$atts['id']?>-dls-wrapper breadcrumb-items-wrapper">
            <?=lana_breadcrumb()?>
    </div>
    <?php

    $breadcrumb = ob_get_clean();


    ob_start();

    $breadcrumb_selector = '#' . $atts['id'];

    ?>
    <style type="text/css">
        /* breadcrumb separator symbol */
        <?=$breadcrumb_selector?> .mesmerize-breadcrumb > li + li:before {
            content: "<?=$breadcrumbSeparatorSymbol?>";
        }
    </style>

    <?php

    $style = ob_get_clean();
    $breadcrumb = $style . $breadcrumb;
    return "<div id='{$atts['id']}' class='breadcrumb-wrapper'>{$breadcrumb}</div>";

}

function getSeparatorSymbol($symbol) {

    $symbol_code = '/';
    switch($symbol) {
        case 'slash':
            $symbol_code = "/";
            break;
        case 'greater_than':
            $symbol_code = ">";
            break;
        case 'angle_right':
            $symbol_code = "»";
            break;
        case 'bull':
            $symbol_code = "•";
            break;
    }

    return $symbol_code;

}
