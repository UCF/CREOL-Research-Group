<?php
/*
Plugin Name: Research Groups
Description: Create dropdown modals for 'Research at CREOL' page
Version: 0.0.0
Author: UCF Web Communications
License: GPL3
GitHub Plugin URI: UCF/{{My-Project}}
*/

if ( ! defined( 'WPINC' ) ) {
    die;
}

require_once 'includes/research-layout.php';

add_shortcode( 'research', 'research_display');