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

    echo '<style>
        .section-title {
            border-bottom: 3px solid #ffcc00;
        }
        .custom-card {
            border: none;
            border-radius: 10px;
            background: #ffcc00;
            display: flex;
            align-items: center;
            padding: 15px;
            margin-bottom: 15px;
            width: 100%;
        }
        .custom-card img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 20px;
        }
        .custom-card .card-body {
            padding-top: 10px;
            width: 100%;
        }
        .custom-card a {
            color: #000;
            text-decoration: none;
            font-size: 0.85rem;
            display: block;
        }
        .job-title {
            font-size: 0.85rem;
            color: #000;
            margin-top: -1em;
            margin-bottom: 0.5em;
            display: block;
        }
        .card-title {
            font-size: 1rem;
        }
        
    </style>';

    echo '<div class="research-group">';

    if (isset($lab_names[$group])) {
        echo '<button class="btn btn-outline-i-primary btn-block" type="button" data-toggle="collapse" data-target="#' . esc_attr($group) . '" aria-expanded="true" aria-controls="collapseExample">' . esc_html($lab_names[$group]) . '</button>';

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
                    'terms'    => 'fol',
                ),
            ),
        );

        $query = new WP_Query($args);

        if ($wporg_atts['debug'] === 'yes') {
            echo "Debug: Query arguments: " . print_r($args, true);
            echo "Debug: Number of posts found: " . $query->found_posts;
        }

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $permalink = get_permalink();
                $featured_image = get_the_post_thumbnail(get_the_ID(), 'medium');
                $job_title = get_field('person_jobtitle');

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
            }
            wp_reset_postdata();
        } else {
            echo '<p>No people found in this </p>';
        }
    } else {
        echo '<p>Invalid group specified.</p>';
    }

    echo '</div>';

    return ob_get_clean();
}