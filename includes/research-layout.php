<?php

// These are pulled from WordPress -> People -> People Groups
// Array of manually inputted group names
$lab_names = [
    'FOL' => 'Fiber Optics',
    'GPCL' => 'Glass Processing Lab',
    'LAMP' => 'Laser-Advanced Manufacturing',
    'MIR' => 'Mid-Infared Combs Group',
    'OC' => 'Optical Ceramics',
    'SDL' => 'Semiconductor Diode Lasers',
    'UP' => 'Ultrafast Photonics',
    'FAST' => 'Florida Attosecond Science & Technology',
    'LPL' => 'Laser Plasma Laboratory',
    'MOF' => 'Microstructured Fibers and Devices',
    'NLO' => 'Nonlinear Optics',
    'PPL' => 'Photoinduced Processing',
    'ULP' => 'Ultrafast Laser Processing',
    'OFC' => 'Optical Fiber Communications',
    'MULTIOFD' => 'Multi-material Optical Fiber Devices',
    'IPES' => 'Integrated Photonic Emerging Solutions',
    'NPM' => 'Nanophotonic Materials Group',
    'BLANCO-REDONDO' => 'Quantum Silicon Photonics',
    'TAS' => 'Theoretical Attosecond Spectroscopiesy',
    'MQW' => 'Multiple Quantum Wells',
    'KIK' => 'Nanophotonics & Near-Field Optics',
    'KVL' => 'Knight Vision Lab',
    'LCD' => 'Liquid Crystal Displays',
    'NPD' => 'Nanophotonics Device',
    'PSD' => 'Photonic Structures & Devices',
    'NANOSCOPY' => 'Optical Nanoscopy',
    'OISL' => 'Optical Imaging System Laboratory',
    'SALEH' => 'Quantum Optics',
    'RANDOM' => 'Photonics Diagnostics of Random Media',
    'NFD' => 'Nonlinear Fiber Dynamics'
];

// Shortcode widget that fetches and displays people using group abbreviations 
// Groups can be displayed in bulk and use inverse styling 
// Cache is set every hour 
function research_display($atts = [], $content = null, $tag = '')
{
    global $lab_names;

    $atts = array_change_key_case((array) $atts, CASE_LOWER);

    $wporg_atts = shortcode_atts(
        array(
            'group' => '',
            'debug' => 'no',
            'inverse' => ''
        ),
        $atts,
        $tag
    );

    // Each group have a number after it ( ex. "FOL 1") the number is the section or "unique ID"
    $arr = explode(" ", $wporg_atts['group']);

    $group = strtoupper($arr[0]);
    $section = $arr[1];
    $inverse = $wporg_atts['inverse']; // Styling

    $groups_and_sections = explode(',', $atts['group']);

    ob_start();

    echo '<style>
        .custom-card {
            display: flex;
            width: 100%;
        }
        .card-image {
            padding: 10px;
        }
        .card-image img {
            width: 150px;
            height: 150px;
            object-fit: cover;
        }
        .card-body {
            padding: 10px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .card-title {
            margin-bottom: 0;
        }
        .job-title {
            color: #000;
        }        
    </style>';

    echo '<div class="research-group">';

    foreach ($groups_and_sections as $item) {
        list($group, $section) = explode(" ", trim($item));
        $group = strtoupper($group);

        if (isset($lab_names[$group])) {
            if ($inverse == '')
                echo '<button class="btn group-btn btn-outline-i-primary btn-block collapsed mb-3" type="button" data-toggle="collapse" data-target="#' . esc_attr($group) . '-' . esc_attr($section) . '" aria-expanded="true" aria-controls="collapseExample">' . esc_html($lab_names[$group]) . '</button>';
            else
                echo '<button class="btn group-btn btn-outline-primary btn-block collapsed mb-3" type="button" data-toggle="collapse" data-target="#' . esc_attr($group) . '-' . esc_attr($section) . '" aria-expanded="true" aria-controls="collapseExample">' . esc_html($lab_names[$group]) . '</button>';

            // Cache key based on group and section(makes faster)
            $transient_key = 'research_group_' . esc_attr($group) . '_' . esc_attr($section);
            $cached_posts = get_transient($transient_key);
            // Fetch posts
            if (false === $cached_posts) {

                $args = array(
                    'posts_per_page' => -1,
                    'post_type'      => 'person',
                    'post_status'    => 'publish',
                    'tax_query'      => array(
                        'relation' => 'AND',
                        array(
                            'taxonomy' => 'category',
                            'field'    => 'slug',
                            'terms'    => 'core-faculty',
                        ),
                        array(
                            'taxonomy' => 'people_group',
                            'field'    => 'slug',
                            'terms'    => $group,
                        ),
                    ),
                );
                $query = new WP_Query($args);
                $cached_posts = $query->have_posts() ? $query->posts : [];

                // Makes cache every hour
                set_transient($transient_key, $cached_posts, HOUR_IN_SECONDS);

                // Always reset post data after a query
                wp_reset_postdata();
            }
            
            if (!empty($cached_posts)) {
                if ($inverse == '')
                    echo '<div class="collapse bg-primary mb-3" id="' . esc_attr($group) . '-' . esc_attr($section) . '">';
                else
                    echo '<div class="collapse bg-faded mb-3" id="' . esc_attr($group) . '-' . esc_attr($section) . '">';   

                foreach ($cached_posts as $post) {
                    setup_postdata($post); // This sets up the global post data for this iteration

                    $permalink = get_permalink($post->ID); // Pass the post ID directly
                    $featured_image = get_the_post_thumbnail($post->ID, 'medium', ['loading' => 'lazy']); // Get thumbnail with post ID
                    $job_title = get_field('person_jobtitle', $post->ID); // Get ACF field with post ID 

                    echo '<a href="' . esc_url($permalink) . '">';
                    echo '<div class="custom-card">';
                    echo '<div class="card-image">';
                    if (!empty($featured_image)) {
                        echo $featured_image;
                    }
                    echo '</div>';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . esc_html(get_the_title($post->ID)) . '</h5>';
                    if (!empty($job_title)) {
                        echo '<div class="job-title"><i>' . esc_html($job_title) . '</i></div>';
                    }
                    echo '</div>';
                    echo '</div>';
                    echo '</a>';
                }

                echo '</div>';

                wp_reset_postdata();
            } else {
                echo '<p>No posts found for this group.</p>';
            }

        } else {
            echo '<p>Invalid group specified.</p>';
        }
        
    }

    echo '</div>';

    return ob_get_clean();
}