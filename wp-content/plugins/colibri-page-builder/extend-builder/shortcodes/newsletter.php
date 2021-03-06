<?php

namespace ExtendBuilder;

add_filter('mc4wp_form_content', '\ExtendBuilder\colibri_mc4wp_filter');
function colibri_mc4wp_filter($content)
{

    $matches = array();
    preg_match_all('/<input[^>]+>/', $content, $matches);

    $attrs = colibri_cache_get('colibri_newsletter_attrs');

    $email = "";
    $submit = "";
    $agree_terms = "";
    for ($i = 0; $i < count($matches[0]); $i++) {
        $match = $matches[0][$i];
        if (strpos($match, "email") !== false) {
            $email = $match;
        }
        if (strpos($match, "submit") !== false) {
            $submit = $match;
        }
        if (strpos($match, "AGREE_TO_TERMS") !== false) {
            $agree_terms = $match;
        }
    }

    ob_start();
    if ($email):
        ?>
        <div class="colibri-newsletter__email-group colibri-newsletter-group">
            <label><?php echo esc_html($attrs['email_label']); ?></label>
            <input type="email" name="EMAIL" placeholder="<?php echo esc_html($attrs['email_placeholder']); ?>" required/>
        </div>
    <?php
    endif;
    if($agree_terms):
    ?>
        <div class=" colibri-newsletter__agree-terms-group colibri-newsletter-group">
            <label>
                <input type="checkbox" name="AGREE_TO_TERMS"  value="1" required/>
                <?php echo esc_html($attrs['agree_terms_label']); ?>
            </label>
        </div>
    <?php
    endif;
    if ($submit):
        ?>
        <div class="colibri-newsletter__submit-group colibri-newsletter-group">
            <button type="submit" >
                <span class="h-svg-icon"><?php if($attrs['submit_button_use_icon'] === '1') echo $attrs['submit_button_icon']; ?></span>
                <span class="colibri-newsletter__submit-text"><?php echo esc_html($attrs['submit_button_label']); ?></span>
            </button>
        </div>
    <?php
    endif;
    $form = ob_get_clean();

//    return $content;
    return $form;
}

add_shortcode('colibri_newsletter', '\ExtendBuilder\colibri_newsletter_shortcode');


function colibri_newsletter_shortcode($atts)
{
    $attrs = shortcode_atts(
        array(
            'email_label' => 'Email address: ',
            'email_placeholder' => 'Your email address',
            'submit_button_label' => 'Subscribe',
            'submit_button_icon' => '',
            'submit_button_use_icon' => '0',
            'agree_terms_label' => 'I have read and agree to the terms & conditions',
            'shortcode' => '',
        ),
        $atts
    );
    $attrs['shortcode'] = colibri_shortcode_decode($attrs['shortcode']);
    $attrs['submit_button_icon'] = colibri_shortcode_decode($attrs['submit_button_icon']);
    colibri_cache_set('colibri_newsletter_attrs', $attrs);
    return do_shortcode($attrs['shortcode']);
}
