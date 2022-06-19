<?php

if ( ! defined('ABSPATH') ) {
    die('Direct access not permitted.');
}

// Plugin Functions

// Citation Posts

function citation_meta_boxes() {
    add_meta_box('citation-meta-box', __( 'Citation', 'citation_textdomain' ), 'citation_meta_box_callback', 'post', 'normal', 'high' );
}

function citation_register_meta_fields() {

    register_meta( 'post', 'citation', 'sanitize_text_field', 'citation_custom_fields_auth_callback' );
    
}

function citation_meta_box_callback( $post ) {	
    $text= get_post_meta($post->ID, 'citation' , true );
    wp_editor( htmlspecialchars_decode($text), 'citation', $settings = array('textarea_name'=>'citation') );
    
}

function save_citation_post($post_id) {
    if (!empty($_POST['citation'])) {
        $datta=htmlspecialchars($_POST['citation']);
        update_post_meta($post_id, 'citation', $datta );
    }
}

// Shortcode citation

function shortcodes_init(){

    function shortcode_citation($atts){

        extract( shortcode_atts( array(
                'post_id' => get_the_ID()
            ), $atts ) );

        $output = htmlspecialchars_decode( get_post_meta($post_id, 'citation' , true ) );

        return $output;
    };

    add_shortcode( "mc-citation","shortcode_citation" );

}

// Process Cron

function nc_verify_links( ) {

    $posts_links = get_posts([
        'post_type'   => 'post',
        'post_status' => 'publish',
        'numberposts' => -1
    ]);

    $links_options = []; 

    foreach( $posts_links as $val ){
        
        $origin_link = '<a target="_blank" href="' . get_permalink($val) . '">' . $val->post_title . '</a>' ;

        if ( preg_match_all('/<a[^>]+href="([^"]+)"[^>]*>/', $val->post_content, $matches, PREG_SET_ORDER) ) {

            foreach( $matches as $key_match => $val_match){

                $url = parse_url($matches[$key_match][1]);

                $status_link = "";

                if (substr($matches[$key_match][1], 0, 8) != 'https://' && substr($matches[$key_match][1], 0, 7) != 'http://'){
                    $status_link ="Protocolo No Especificado";
                } else if (substr($matches[$key_match][1], 0, 7) === 'http://' && !strpos($matches[$key_match][1], " ")){
                    $status_link ="Enlace Inseguro";
                }else if(strpos($matches[$key_match][1], " ")){
                    $status_link ="Enlace malformado";
                }else{
                    continue;
                }

                $links_options[] = array(      
                    "url" => $matches[$key_match][1],
                    "status" => $status_link,
                    "origin" => $origin_link        
                );
            }
        }
    }

    if (!get_option('wrong_links', $links_options)) {
        delete_option('wrong_links');
        add_option('wrong_links', $links_options);
    }else{
        update_option('wrong_links', $links_options);
    }

}

// Define Cron Jobs

function custom_cron_job_recurrence( $schedules ) {
    $schedules['min'] = array(
        'display' => __( 'Min', 'textdomain' ),
        'interval' => 60, 
    );
    return $schedules;
}

function custom_cron_job() {
    if ( ! wp_next_scheduled( 'nc_process_verify_links' ) ) {
        wp_schedule_event( current_time( 'timestamp' ), 'min', 'nc_process_verify_links' );
    }
}