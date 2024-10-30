<?php
/**
 * @internal never define functions inside callbacks.
 * these functions could be run multiple times; this would result in a fatal error.
 */
 
/**
 * custom option and settings
 */
function lipsumator_settings_init()
{
    // register a new setting for "lipsumator" page
    register_setting('lipsumator', 'lipsumator_options');
    
    // register a new section in the "lipsumator" page
    add_settings_section(
        'lipsumator_section_guide',
        __('Guide', 'lipsumator'),
        'lipsumator_section_guide_cb',
        'lipsumator'
    );

    // register a new section in the "lipsumator" page
    add_settings_section(
        'lipsumator_section_settings',
        __('Settings.', 'lipsumator'),
        'lipsumator_section_settings_cb',
        'lipsumator'
    );
 
    // register a new field in the "lipsumator_section_settings" section, inside the "lipsumator" page
    add_settings_field(
        'lipsumator_highlight_checkbox', // as of WP 4.6 this value is used only internally
        // use $args' label_for to populate the id inside the callback
        __('Active Highlighting', 'lipsumator'),
        'lipsumator_highlight_checkbox_cb',
        'lipsumator',
        'lipsumator_section_settings',
        [
            'label_for' => 'lipsumator_highlight_checkbox',
            'class' => 'lipsumator_row',
            'lipsumator_custom_data' => 'custom',
        ]
    );


}
 
/**
 * register our lipsumator_settings_init to the admin_init action hook
 */
add_action('admin_init', 'lipsumator_settings_init');
 
/**
 * custom option and settings:
 * callback functions
 */
 
// developers section cb
 
// section callbacks can accept an $args parameter, which is an array.
// $args have the following keys defined: title, id, callback.
// the values are defined at the add_settings_section() function.
function lipsumator_section_guide_cb($args)
{
    ?>
<div id="<?php echo esc_attr($args['id']); ?>">
  <p>
    <?php esc_html_e('Lipsumator makes it easy to mock up page layouts with temporary text. When it is time to insert the final content you can turn on the "Highlight mode" to easily make sure you dont leave any placeholder text when the site goes live.', 'lipsumator'); ?>
  </p>
  <hr />
  <h3>
    <?php esc_html_e('Syntax', 'lipsumator'); ?>
  </h3>
  <p>
    <?php esc_html_e('Example:', 'lipsumator'); ?>
  </p>
  <pre>
<code>[lipsumator w=3 t=h1]</code>
</pre>

  <h4>
    <?php esc_html_e('Type', 'lipsumator'); ?>
  </h4>
  <p>
    <?php esc_html_e('Use "w", "s" and "p" to define the type of content you want to generate. Whatever number you specify dictates the amount of words, sentences etc. you generate.', 'lipsumator'); ?>
  </p>
  <ul>
    <li><code>
        <?php esc_html_e('w', 'lipsumator'); ?></code>:
      <?php esc_html_e('Generates words.', 'lipsumator'); ?>
    </li>
    <li><code>
        <?php esc_html_e('s', 'lipsumator'); ?></code>:
      <?php esc_html_e('Generates sentences.', 'lipsumator'); ?>
    </li>
    <li><code>
        <?php esc_html_e('p', 'lipsumator'); ?></code>:
      <?php esc_html_e('Generates paragraphs.', 'lipsumator'); ?>
    </li>
  </ul>

  <h4>
    <?php esc_html_e('Tag', 'lipsumator'); ?> <code>
      <?php esc_html_e('t', 'lipsumator'); ?></code>:
  </h4>
  <p>
    <?php esc_html_e('Tag is defined as "t" and defines the type of element tag you want to wrap your generated placeholder content in. Words and sentences are wrapt into a single tag. Paragraphs are wrapt individually. If you dont define anything, the generated content will not be wrapped at all.', 'lipsumator'); ?>
  </p>
  <hr />
</div>
<?php
}

function lipsumator_section_settings_cb($args)
{
    ?>
<p id="<?php echo esc_attr($args['id']); ?>">
  <?php //esc_html_e('Settings.', 'lipsumator'); ?>
</p>
<?php
}
 
// pill field cb
 
// field callbacks can accept an $args parameter, which is an array.
// $args is defined at the add_settings_field() function.
// wordpress has magic interaction with the following keys: label_for, class.
// the "label_for" key value is used for the "for" attribute of the <label>.
// the "class" key value is used for the "class" attribute of the <tr> containing the field.
// you can add custom key value pairs to be used inside your callbacks.
function lipsumator_highlight_checkbox_cb($args)
{
    // get the value of the setting we've registered with register_setting()
    $options = get_option('lipsumator_options');
    // output the field ?>
<input type="checkbox"
       name="lipsumator_options[highlight]"
       value="1"
       <?php
       checked(
       1==$options['highlight']
       );
       ?> />
<p class="description">
  <?php esc_html_e('Highlights all Lorem ipsum text. This is usefull when changing the placeholder text for the final content. The highlighting makes it less likely that you will miss something.', 'lipsumator'); ?>
</p>
<?php
}

 
/**
 * Management level menu
 */
function lipsumator_options_page()
{
    // add top level menu page
    add_management_page(
        'Lipsumator',
        'Lipsumator',
        'manage_options',
        'lipsumator',
        'lipsumator_options_page_html'
    );
}
 
/**
 * register our lipsumator_options_page to the admin_menu action hook
 */
add_action('admin_menu', 'lipsumator_options_page');
 
/**
 * top level menu:
 * callback functions
 */
function lipsumator_options_page_html()
{
    // check user capabilities
    if (! current_user_can('manage_options')) {
        return;
    }
 
    // add error/update messages
 
    // check if the user have submitted the settings
    // wordpress will add the "settings-updated" $_GET parameter to the url
    if (isset($_GET['settings-updated'])) {
        // add settings saved message with the class of "updated"
        add_settings_error('lipsumator_messages', 'lipsumator_message', __('Settings Saved', 'lipsumator'), 'updated');
    }
 
    // show error/update messages
 settings_errors('lipsumator_messages'); ?>
<div class="wrap">
  <h1>
    <?php echo esc_html(get_admin_page_title()); ?>
  </h1>
  <form action="options.php"
        method="post">
    <?php
 // output security fields for the registered setting "lipsumator"
 settings_fields('lipsumator');
    // output setting sections and their fields
    // (sections are registered for "lipsumator", each field is registered to a specific section)
    do_settings_sections('lipsumator');
    // output save settings button
    submit_button('Save Settings'); ?>
  </form>
</div>
<?php
}