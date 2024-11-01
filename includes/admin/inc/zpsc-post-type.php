<?php
/**
 * Register a custom post type called "Product Size Chart"
 */
$zpsc_labels = array(
                'name'                  => esc_html__( 'Size Charts', ZPSC_TEXT_DOMAIN),
                'singular_name'         => esc_html__( 'Size Chart', ZPSC_TEXT_DOMAIN ),
                'menu_name'             => esc_html__( 'Size Charts', ZPSC_TEXT_DOMAIN),
                'name_admin_bar'        => esc_html__( 'Size Chart', ZPSC_TEXT_DOMAIN ),
                'add_new'               => esc_html__( 'Add New', ZPSC_TEXT_DOMAIN ),
                'add_new_item'          => esc_html__( 'Add New Size Chart', ZPSC_TEXT_DOMAIN ),
                'new_item'              => esc_html__( 'New Size Chart', ZPSC_TEXT_DOMAIN ),
                'edit_item'             => esc_html__( 'Edit Size Chart', ZPSC_TEXT_DOMAIN ),
                'view_item'             => esc_html__( 'View Size Chart', ZPSC_TEXT_DOMAIN ),
                'all_items'             => esc_html__( 'All Size Charts', ZPSC_TEXT_DOMAIN ),
                'search_items'          => esc_html__( 'Search Size Charts', ZPSC_TEXT_DOMAIN ),
                'parent_item_colon'     => esc_html__( 'Parent Size Charts:', ZPSC_TEXT_DOMAIN ),
                'not_found'             => esc_html__( 'No size chart found.', ZPSC_TEXT_DOMAIN ),
                'not_found_in_trash'    => esc_html__( 'No size charts in Trash.', ZPSC_TEXT_DOMAIN )       
            );

            $zpsc_args = array(
                'labels'             => $zpsc_labels,
                'public'             => false,
                'publicly_queryable' => true,
                'show_ui'            => true,
                'show_in_menu'       => 'edit.php?post_type=zpsc-size-chart',
                'query_var'          => true,
                'rewrite'            => array( 'slug' => 'zpsc-size-chart' ),
                'capability_type'    => 'post',
                'has_archive'        => true,
                'hierarchical'       => false,
                'menu_position'      => null,
                'supports'           => array( 'title' ),
            );

            /**
            * Register post type
            */
            register_post_type('zpsc-size-chart', $zpsc_args);   
?>