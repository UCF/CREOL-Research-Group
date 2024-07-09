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


function research_display( $atts = [], $content = null, $tag = '' ) {
    global $lab_names;

    $atts = array_change_key_case( (array) $atts, CASE_LOWER );

    $wporg_atts = shortcode_atts(
        array(
            'group'  => '',
        ), $atts, $tag
    );

    $group = strtoupper($wporg_atts['group']);

    echo 'TEST';

    echo '<div class="research-group">';

    if (isset($lab_names[$group])) {
        echo '<button class="btn btn-outline-i-primary btn-block" type="button" data-toggle="collapse" data-target="#' . esc_attr($group) . '" aria-expanded="true" aria-controls="collapseExample">' . esc_html($lab_names[$group]) . '</button>';

        $args = array(
            'post_type'      => 'person',
            'post_status'    => 'publish',
            'category_name'  => 'core-faculty',
            'meta_query'     => array(
                array(
                    'key'     => 'person_group',
                    'value'   => $group,
                    'compare' => '=',
                ),
            ),
        );

        $posts = get_posts($args);

        if (!empty($posts)) {
            echo '<div class="row">';
            foreach ($posts as $post) {
                setup_postdata($post);
                $permalink = get_permalink($post);
                $featured_image = get_the_post_thumbnail($post->ID, 'medium');
                $job_title = get_field('person_jobtitle', $post->ID);

                echo '<div class="card-box col-lg-2 col-md-3 col-sm-4 col-6">';
                echo '<div class="card custom-card">';
                echo '<a href="' . esc_url($permalink) . '">';
                if (!empty($featured_image)) {
                    echo $featured_image;
                }
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . esc_html(get_the_title($post)) . '</h5>';
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
            echo '<p>No posts found.</p>';
        }
    } else {
        echo '<p>Invalid group specified.</p>';
    }

    echo '</div>'; 
}