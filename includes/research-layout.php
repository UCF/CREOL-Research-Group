<?php

$lab_names = [
    'FOL' => 'Fiber Optics',
    'GPCL' => 'Glass Processing Lab',
    'LAMP' => 'Laser-Advanced Manufacturing',
    'MIR' => 'Mid-Infared Combs Group',
    'OC' => 'Optical Ceramics',
    'SDL' => 'Semiconductor Diode Lasers',
    'UP' => 'Ultrafast Photonics',
    'LPL' => 'Laser Plasma Laboratory',
    'MOF' => 'Microstructured Fibers and Devices',
    'NLO' => 'Nonlinear Optics',
    'PPL' => 'Photoinduced Processing',
    'ULP' => 'Ultrafast Laser Processing',

];


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

    $group = strtoupper($wporg_atts['group']);
    $inverse = $wporg_atts['inverse'];

    ob_start();

    echo '<style>
        .section-title {
            border-bottom: 3px solid #ffcc00;
        }
        .custom-card {
            border: none;
            background: #f7f7f7;
            align-items: center;
            padding: 15px;
            margin-bottom: 15px;
            width: 100%;
            margin-top: 3%;
        }
        .custom-card.i {
            background: #ffcc00;
        }
        .custom-card img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            margin-right: 20px;
        }
        .custom-card .card-body {
            padding-top: 10px;
            padding-left: 3em;
            width: 100%;
        }
        .custom-card a {
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        .job-title {
            font-size: 1.2rem;
            color: #000;
            margin-top: -1em;
            margin-bottom: 0.5em;
            display: block;
        }
        .card-title {
            font-size: 1.2rem;
        }

        
    </style>';

    echo '<div class="research-group">';

    if (isset($lab_names[$group])) {
        echo '<script>
            // console.log(' . $wporgs_atts['inverse'] . ' + " wporgs")
            console.log(' . $inverse . ' + " inverse")
            console.log(' . $group . ' + " group")
            console.log("Logged")
        </script>';
        if ($inverse == '')
            echo '<button class="btn btn-outline-i-primary btn-block" type="button" data-toggle="collapse" data-target="#' . esc_attr($group) . '" aria-expanded="true" aria-controls="collapseExample">' . esc_html($lab_names[$group]) . '</button>';
        else
            echo '<button class="btn btn-outline-primary btn-block" type="button" data-toggle="collapse" data-target="#' . esc_attr($group) . '" aria-expanded="true" aria-controls="collapseExample">' . esc_html($lab_names[$group]) . '</button>';

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
                    'terms'    => $wporg_atts['group'],
                ),
            ),
        );

        $query = new WP_Query($args);

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $permalink = get_permalink();
                $featured_image = get_the_post_thumbnail(get_the_ID(), 'medium');
                $job_title = get_field('person_jobtitle');

                if ($inverse == '')
                    echo '<div class="custom-card collapse i" id="' . esc_attr($group) . '">';
                else
                    echo '<div class="custom-card collapse" id="' . esc_attr($group) . '">';    

                echo '<a href="' . esc_url($permalink) . '">';
                echo '<div class="card-image">';
                if (!empty($featured_image)) {
                    echo $featured_image;
                }
                echo '</div>';
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