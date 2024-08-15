<?php

// These are pulled from WordPress -> People -> People Groups
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
    'BLANCO-REDONDO' => 'Quantum Silicone Photonics',
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

    $arr = explode(" ", $wporg_atts['group']);

    $group = strtoupper($arr[0]);
    $section = $arr[1];
    $inverse = $wporg_atts['inverse'];

    ob_start();

    echo '<style>
        // .section-title {
        //     border-bottom: 3px solid #ffcc00;
        // }
        // .custom-card {
        //     border: none;
        //     background: #f7f7f7;
        //     align-items: center;
        //     padding: 15px;
        //     margin-bottom: 15px;
        //     width: 100%;
        //     margin-top: 3%;
        // }
        // .custom-card.i {
        //     background: #ffcc00;
        // }
        // .custom-card img {
        //     width: 150px;
        //     height: 150px;
        //     object-fit: cover;
        //     margin-right: 20px;
        // }
        // .custom-card .card-body {
        //     padding-top: 10px;
        //     padding-left: 3em;
        //     width: 100%;
        // }
        // .custom-card a {
        //     text-decoration: none;
        //     display: flex;
        //     align-items: center;
        // }
        // .job-title {
        //     font-size: 1.2rem;
        //     color: #000;
        //     margin-top: -1em;
        //     margin-bottom: 0.5em;
        //     display: block;
        // }
        // .card-title {
        //     font-size: 1.2rem;
        // }

        
    </style>';

    echo '<div class="research-group">';

    if (isset($lab_names[$group])) {
        echo '<script>
            console.log(' . json_encode($inverse) . ' + " inverse")
            console.log(' . json_encode($group) . ' + " group")
            console.log(' . json_encode($section) . ' + " section")
            console.log("Logged")
        </script>';
        if ($inverse == '')
            echo '<button class="btn btn-outline-i-primary btn-block" type="button" data-toggle="collapse" data-target="#' . esc_attr($group) . '-' . esc_attr($section) . '" aria-expanded="true" aria-controls="collapseExample">' . esc_html($lab_names[$group]) . '</button>';
        else
            echo '<button class="btn btn-outline-primary btn-block" type="button" data-toggle="collapse" data-target="#' . esc_attr($group) . '-' . esc_attr($section) . '" aria-expanded="true" aria-controls="collapseExample">' . esc_html($lab_names[$group]) . '</button>';

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

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $permalink = get_permalink();
                $featured_image = get_the_post_thumbnail(get_the_ID(), 'medium');
                $job_title = get_field('person_jobtitle');

                if ($inverse == '')
                    echo '<div class="custom-card collapse i" id="' . esc_attr($group) . '-' . esc_attr($section) . '">';
                else
                    echo '<div class="custom-card collapse" id="' . esc_attr($group) . '-' . esc_attr($section) . '">';    

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
            echo '<p></p>';
        }
    } else {
        echo '<p>Invalid group specified.</p>';
    }

    echo '</div>';

    return ob_get_clean();
}