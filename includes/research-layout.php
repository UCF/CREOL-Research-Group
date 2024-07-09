<?php

$lab_names = [
    'FOL' => 'Fiber Optics',
    'GPCL' => 'Glass Processing Lab',
    'LAMO' => 'Laser-Advanced Manufacturing',
    'MIR' => 'Mid-Infared Combs Group',
    'OC' => 'Optical Ceramics',
    'SDL' => 'Semiconductor Diode Lasers',
    'UP' => 'Ultrafast Photonics',
    'LPL' => 'Laser Plasma Laboratory',
    'MOF' => 'Microstructured Fibers and Devices',

];


function research_display($atts = [], $content = null, $tag = '')
{
    global $lab_names;

    $atts = array_change_key_case((array) $atts, CASE_LOWER);

    $wporg_atts = shortcode_atts(
        array(
            'group' => '',
            'debug' => 'no',
        ),
        $atts,
        $tag
    );

    $group = strtoupper($wporg_atts['group']);

    ob_start();

    echo '<div class="research-group">';

    if (isset($lab_names[$group])) {
        echo '<button class="btn btn-outline-i-primary btn-block" type="button" data-toggle="collapse" data-target="#' . esc_attr($group) . '" aria-expanded="true" aria-controls="collapseExample">' . esc_html($lab_names[$group]) . '</button>';

        $args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_key'  => '_group_name',
            'meta_value'    => 'FOL',
        );

        $query = new WP_Query($args);

        if ($wporg_atts['debug'] === 'yes') {
            echo "Debug: Query arguments: " . print_r($args, true);
            echo "Debug: Number of posts found: " . $query->found_posts;
        }

        if ($query->have_posts()) {
            echo '<div class="row">';
            while ($query->have_posts()) {
                $query->the_post();
                $permalink = get_permalink();
                $featured_image = get_the_post_thumbnail(get_the_ID(), 'medium');
                $job_title = get_field('person_jobtitle');

                echo '<div class="card-box col-lg-2 col-md-3 col-sm-4 col-6">';
                echo '<div class="card custom-card">';
                echo '<a href="' . esc_url($permalink) . '">';
                if (!empty($featured_image)) {
                    echo $featured_image;
                }
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . esc_html(get_the_title()) . '</h5>';
                if (!empty($job_title)) {
                    echo '<div class="job-title"><i>' . esc_html($job_title) . '</i></div>';
                }
                echo '</div>';
                echo '</a>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
            wp_reset_postdata();
        } else {
            echo '<p>No people found in this group.</p>';
        }
    } else {
        echo '<p>Invalid group specified.</p>';
    }

    echo '</div>';

    return ob_get_clean();
}