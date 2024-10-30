<?php
// Add Shortcode
function lipsumator_func($atts)
{
    // Attributes
    $atts = shortcode_atts(
        array(
            'w' => '',
            's' => '',
            'p' => '',
            't' => '',
        ),
        $atts
    );

    $lipsum = new joshtronic\LoremIpsum();

    if ($atts['w'] !== '') {
        $output = $lipsum->words($atts['w']);
        if ($atts['t'] !== '' ) {
            $output = '<' . $atts['t'] . '>' . ucfirst($output) . '</' . $atts['t'] . '>';
        }
    } elseif ($atts['s'] !== '') {
        $output = $lipsum->sentences($atts['s'], $atts['t']);
    } elseif ($atts['p'] !== '') {
        $output = $lipsum->paragraphs($atts['p'], $atts['t']);
    } else {
        $output = $lipsum->paragraphs('1', $atts['t']);
    }

    $output = ucfirst($output);

    $options = get_option('lipsumator_options');

    if (isset($options['highlight'])) {
        $output = '<span class="lorem-ipsum-highlight">' . $output . '</span>';
    }

    return $output;

    
}
add_shortcode('lipsumator', 'lipsumator_func');