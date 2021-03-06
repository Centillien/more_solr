<?php
$setting = elgg_get_plugin_setting('admin_only', 'more_solr');
if($setting != 'no'){
    if(elgg_is_admin_logged_in()){
        /**
         * Search box
         *
         * @uses $vars['value'] Current search query
         * @uses $vars['class'] Additional class
         */
        /** this overrides elgg's own search_box */

        $value = "";
        if (array_key_exists('value', $vars)) {
            $value = $vars['value'];
        } elseif ($value = get_input('q', get_input('tag', NULL))) {
            $value = $value;
        }

        $class = "elgg-search";
        if (isset($vars['class'])) {
            $class = "$class {$vars['class']}";
        }

        // encode <,>,&, quotes and characters above 127
        if (function_exists('mb_convert_encoding')) {
            $display_query = mb_convert_encoding($value, 'HTML-ENTITIES', 'UTF-8');
        } else {
            // if no mbstring extension, we just strip characters
            $display_query = preg_replace("/[^\x01-\x7F]/", "", $value);
        }

        // render placeholder separately so it will double-encode if needed
        $placeholder = htmlspecialchars(elgg_echo('search'), ENT_QUOTES, 'UTF-8');

        $search_attrs = elgg_format_attributes(array(
            'type' => 'text',
            'class' => 'search-input',
            'size' => '21',
            'name' => 'q',
            'autocapitalize' => 'off',
            'autocorrect' => 'off',
            'required' => true,
            'value' => $display_query,
        ));

        $popup_content = elgg_view_form('options', array(
            'enctype' => 'multipart/form-data',
            'id' => 'searchForm'
        ));

        $popup = elgg_format_element('div', [
            'class' => 'elgg-module-popup hidden',
            'id' => 'popup-module',
        ], $popup_content);

        $link = elgg_view('output/url', [
            'href' => '#popup-module',
            'text' => 'Advanced options',
            'rel' => 'popup',
            'data-position' => json_encode([
                'my' => 'center bottom',
                'at' => 'center top',
            ]),
        ]);
        ?>

        <form class="<?php echo $class; ?>" action="<?php echo elgg_get_site_url(); ?>search" method="get">
            <fieldset>
                <input placeholder="<?php echo $placeholder; ?>" <?php echo $search_attrs; ?> />
                <input type="hidden" name="search_type" value="all" />
                <input type="submit" value="<?php echo elgg_echo('search:go'); ?>" class="search-submit-button" />
            </fieldset>
        </form>
        <?php echo $popup; echo $link;
    } else {
        /**
         * Search box
         *
         * @uses $vars['value'] Current search query
         * @uses $vars['class'] Additional class
         */

        $value = "";
        if (array_key_exists('value', $vars)) {
            $value = $vars['value'];
        } elseif ($value = get_input('q', get_input('tag', NULL))) {
            $value = $value;
        }

        $class = "elgg-search";
        if (isset($vars['class'])) {
            $class = "$class {$vars['class']}";
        }

        // @todo - create function for sanitization of strings for display in 1.8
        // encode <,>,&, quotes and characters above 127
        if (function_exists('mb_convert_encoding')) {
            $display_query = mb_convert_encoding($value, 'HTML-ENTITIES', 'UTF-8');
        } else {
            // if no mbstring extension, we just strip characters
            $display_query = preg_replace("/[^\x01-\x7F]/", "", $value);
        }

        // render placeholder separately so it will double-encode if needed
        $placeholder = htmlspecialchars(elgg_echo('search'), ENT_QUOTES, 'UTF-8');

        $search_attrs = elgg_format_attributes(array(
            'type' => 'text',
            'class' => 'search-input',
            'size' => '21',
            'name' => 'q',
            'autocapitalize' => 'off',
            'autocorrect' => 'off',
            'required' => true,
            'value' => $display_query,
        ));
        ?>

        <form class="<?php echo $class; ?>" action="<?php echo elgg_get_site_url(); ?>search" method="get">
            <fieldset>
                <input placeholder="<?php echo $placeholder; ?>" <?php echo $search_attrs; ?> />
                <input type="hidden" name="search_type" value="all" />
                <input type="submit" value="<?php echo elgg_echo('search:go'); ?>" class="search-submit-button" />
            </fieldset>
        </form>
        <?php
        }
} else {
    /**
     * Search box
     *
     * @uses $vars['value'] Current search query
     * @uses $vars['class'] Additional class
     */
    /** this overrides elgg's own search_box */

    $value = "";
    if (array_key_exists('value', $vars)) {
        $value = $vars['value'];
    } elseif ($value = get_input('q', get_input('tag', NULL))) {
        $value = $value;
    }

    $class = "elgg-search";
    if (isset($vars['class'])) {
        $class = "$class {$vars['class']}";
    }

    // encode <,>,&, quotes and characters above 127
    if (function_exists('mb_convert_encoding')) {
        $display_query = mb_convert_encoding($value, 'HTML-ENTITIES', 'UTF-8');
    } else {
        // if no mbstring extension, we just strip characters
        $display_query = preg_replace("/[^\x01-\x7F]/", "", $value);
    }

    // render placeholder separately so it will double-encode if needed
    $placeholder = htmlspecialchars(elgg_echo('search'), ENT_QUOTES, 'UTF-8');

    $search_attrs = elgg_format_attributes(array(
        'type' => 'text',
        'class' => 'search-input',
        'size' => '21',
        'name' => 'q',
        'autocapitalize' => 'off',
        'autocorrect' => 'off',
        'required' => true,
        'value' => $display_query,
    ));

    $popup_content = elgg_view_form('options', array(
        'enctype' => 'multipart/form-data',
        'id' => 'searchForm'
    ));

    $popup = elgg_format_element('div', [
        'class' => 'elgg-module-popup hidden',
        'id' => 'popup-module',
    ], $popup_content);

    $link = elgg_view('output/url', [
        'href' => '#popup-module',
        'text' => 'Advanced options',
        'rel' => 'popup',
        'data-position' => json_encode([
            'my' => 'center bottom',
            'at' => 'center top',
        ]),
    ]);
    ?>

    <form class="<?php echo $class; ?>" action="<?php echo elgg_get_site_url(); ?>search" method="get">
        <fieldset>
            <input placeholder="<?php echo $placeholder; ?>" <?php echo $search_attrs; ?> />
            <input type="hidden" name="search_type" value="all" />
            <input type="submit" value="<?php echo elgg_echo('search:go'); ?>" class="search-submit-button" />
        </fieldset>
    </form>
    <?php echo $popup; echo $link;
}
?>