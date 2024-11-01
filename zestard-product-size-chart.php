<?php

/*

 * Plugin Name:       Zestard Product Size Chart

 * Description:       Allows you to create size chart for products and category

 * Version:           1.0.5

 * Author:            Zestard Technologies

 * Author URI:        https://profiles.wordpress.org/zestardtechnologies/

 * License:           GPLv2 or later

 * Text Domain:       zestard-product-size-chart

 */



/**

  Copyright 2020  Zestard Technology

  This program is free software; you can redistribute it and/or modify

  it under the terms of the GNU General Public License, version 2, as

  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,

  but WITHOUT ANY WARRANTY; without even the implied warranty of

  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the

  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License

  along with this program; if not, write to the Free Software

  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

 */



/**

 * For security check 

 */

defined('ABSPATH') or die('You can not access this file');



   /**

    * Define variable for text-domain 

    */

    if ( ! defined( 'ZPSC_TEXT_DOMAIN' ) ) {



        define('ZPSC_TEXT_DOMAIN','zestard-product-size-chart');

    }



/**

 * Check woocommerce plugin activate or not

 */

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

        

/**

 * Define Product Size Chart main class 

 */

if ( ! class_exists( 'ZPSC_product_size_chart' ) ) {



    /**

     * Main ZPSC_product_size_chart Class

     * @since 1.0.0

     */

    class ZPSC_product_size_chart {



       /**

        * Public variable

        */

        public $plugin;     



        /**

         * Get parameter for the error code

         */

        const ZPSC_CHART_ERROR_PARAM = 'zpsc-chart-error';

        const ZPSC_CHART_OPT_ERROR_PARAM = 'zpsc-opt-error';



        /**

        * Define constructor

        */

        public function __construct() {



           /**

            * Extracts the details of a plugin from its filename

            */

            $this->plugin = plugin_basename(__FILE__);

        }



       /**

        * Add all required actions & filter_list()

        */

        public function ZPSC_register() {



            /**

             * Add admin actions & filters

             */

            if ( is_admin() ) {

                add_action('admin_enqueue_scripts', array($this, 'ZPSC_admin_enqueue'));

                add_action('admin_menu', array($this, 'ZPSC_register_post_type'), 99);

                add_action('add_meta_boxes', array($this, 'ZPSC_register_chart_meta_box'));

                add_action('save_post', array($this, 'ZPSC_save_chart_table'), 10, 3 );

                add_action('edit_form_top', array($this, 'ZPSC_field_validate_admin_notice'));    

                add_filter('post_updated_messages', array( $this, 'ZPSC_post_updated_messages'));    

                add_filter('bulk_post_updated_messages', array( $this, 'ZPSC_bulk_post_updated_messages'), 10, 2 );           

            }



            /**

             * Add frontend actions & filters

             */

            add_action('wp_enqueue_scripts', array($this, 'ZPSC_front_Enqueue'));



            /**

             * Add table on assigned products tab

             */

            add_filter('woocommerce_product_tabs', array( $this, 'ZPSC_product_tab'));



        }



        /**

         * Include activation file

         */

        public function ZPSC_activate() {



            include_once('includes/zpsc-activation.php');

        }



        /**

         * Include deactivation file

         */

        public function ZPSC_deactivate() {



           include_once('includes/zpsc-deactivation.php');

        }



        /**

         * Include Size Chart Post Type as submenu of woocommerce

         */

        public function ZPSC_register_post_type() {



           add_submenu_page('woocommerce', esc_html__('Size Charts', ZPSC_TEXT_DOMAIN), esc_html__('Zestard Size Charts', ZPSC_TEXT_DOMAIN), 'manage_options', 'edit.php?post_type=zpsc-size-chart');  



            include_once('includes/admin/inc/zpsc-post-type.php');  

        }        



        /**

         * Include Size Chart Meta Box file

         * @param string $post_type

         * @return string current $post_type

         */

        public function ZPSC_register_chart_meta_box($post_type) {



            /**

             * Check for current post type

             */

            if ( 'zpsc-size-chart' !== $post_type ) {

                return;

            }



            include_once('includes/admin/inc/zpsc-meta-box.php');

        }





        /**

         * Save the chart table meta when the post is saved.

         * @param int $post_id The ID of the post being saved.

         */

        public function ZPSC_save_chart_table( $post_id ) {



            /**

             * We need to verify this came from the our screen and with proper authorization,

             * because save_post can be triggered at other times.

             */     



            $screen = get_current_screen();



            /**

             * Check current post type is "zpsc-size-chart"

             */

            if ('zpsc-size-chart' === $screen->post_type) {



                // Check to see if we are autosaving

                if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)

                    return;

            }           



            /**

             * Check if our nonce is set.

             */

            if ( ! isset( $_POST['zpsc_chart_meta_box_nonce'] ) ) {

                return $post_id;

            }

        

            /**

             * Get our nonce value.

             */

            $zpsc_chart_nonce = sanitize_text_field( $_POST['zpsc_chart_meta_box_nonce'] );



            /**

             * Verify that the nonce is valid. 

             */

            if ( ! wp_verify_nonce( $zpsc_chart_nonce, 'zpsc_chart_meta_box' ) ) {

                return $post_id;

            }                  



            /**

             * Check if our nonce is set.

             */

            if ( ! isset( $_POST['zpsc_chart_opt_meta_box_nonce'] ) ) {

                return $post_id;

            }



            /**

             * Get our nonce value.

             */

            $zpsc_chart_opt_nonce = sanitize_text_field( $_POST['zpsc_chart_opt_meta_box_nonce'] );



            /**

             * Verify that the nonce is valid. 

             */

            if ( ! wp_verify_nonce( $zpsc_chart_opt_nonce, 'zpsc_chart_opt_meta_box' ) ) {

                return $post_id;

            }        



            if ( !empty( $_POST[ 'zpsc_chart_input' ] ) ) {



                if ( !empty( $_POST[ 'zpsc_table_data' ] ) ) {



                    /**

                     * Sanitize the user input.

                     */

                    $zpsc_table_data = sanitize_text_field( $_POST['zpsc_table_data'] );



                    /**

                     * Update the "create size chart" meta field.

                     */

                    update_post_meta( $post_id, 'zpsc_table_data', $zpsc_table_data );                    



                }



            } else { 



                    /**

                     * Save post as pending if fields are empty

                     */

                    if ( ( isset( $_POST['publish'] ) || isset( $_POST['save'] ) ) && $_POST['post_status'] == 'publish' ) {

                        $zpsc_table_data = sanitize_text_field( $_POST['zpsc_table_data'] );
                        update_post_meta( $post_id, 'zpsc_table_data', $zpsc_table_data );

                        /**

                         * Change post status

                         */

                        /*global $wpdb;

                        $wpdb->update( $wpdb->posts, array( 'post_status' => 'pending' ), array( 'ID' => $post_id ) );*/



                        /**

                         * Add error status in url 

                         */

                         /*add_filter('redirect_post_location', function($loc) {

                            return add_query_arg( 

                                array( 'message' => 5, 

                                        self::ZPSC_CHART_ERROR_PARAM => 1

                                     ),

                                $loc

                            );

                        });*/               



                    }

            }





            if ( !empty( $_POST[ 'zpsc_product' ] ) ) {              



                    if ( count( $_POST[ 'zpsc_product' ] ) > 0 ) {



                        /**

                         * Sanitize the user input array.

                         */

                        $zpsc_product = filter_var_array($_POST['zpsc_product']); 



                        /**

                         * Update the "chart option" meta field.

                         */

                        update_post_meta( $post_id, 'zpsc_product', $zpsc_product );

                    }



            } else { 



                    /**

                     * Save post as pending if fields are empty

                     */

                    if ( ( isset( $_POST['publish'] ) || isset( $_POST['save'] ) ) && $_POST['post_status'] == 'publish' ) {



                        /**

                         * Change post status

                         */

                        global $wpdb;

                        $wpdb->update( $wpdb->posts, array( 'post_status' => 'pending' ), array( 'ID' => $post_id ) );



                        /**

                         * Add error status in url 

                         */

                         add_filter('redirect_post_location', function($loc) {

                            return add_query_arg( 

                                array( 'message' => 5, 

                                        self::ZPSC_CHART_OPT_ERROR_PARAM => 2

                                     ),

                                $loc

                            );

                        });               



                    }                

                }

                

        }



        /**

         * Change messages when a post type is updated.

         *

         * @param  array $messages Array of messages.

         * @return array

         */

        public function ZPSC_post_updated_messages( $messages ) {

            global $post;



            $messages['zpsc-size-chart'] = array(

                0  => '', // Unused. Messages start at index 1.

                /* size chart view URL. */

                1  => esc_html__( 'Size Chart updated.', ZPSC_TEXT_DOMAIN ),

                4  => esc_html__( 'Size Chart updated.', ZPSC_TEXT_DOMAIN ),

                /* size chart url */

                6  => esc_html__( 'Size Chart published.', ZPSC_TEXT_DOMAIN ),

                7  => esc_html__( 'Size Chart saved.', ZPSC_TEXT_DOMAIN ),

                8  => esc_html__( 'Size Chart submitted.', ZPSC_TEXT_DOMAIN ),

            );



            return $messages;

        }



        /**

         * Specify custom bulk actions messages.

         *

         * @param  array $bulk_messages Array of messages.

         * @param  array $bulk_counts Array of how many objects were updated.

         * @return array

         */

        public function ZPSC_bulk_post_updated_messages( $bulk_messages, $bulk_counts ) {

            

            $bulk_messages['zpsc-size-chart'] = array(

                /* size chart count updated */

                'updated'   => esc_html__( $bulk_counts['updated'] . ' Size Chart updated.', ZPSC_TEXT_DOMAIN ),

                /* size chart count locked */

                'locked'    => esc_html__( $bulk_counts['locked'] . ' Size Chart not updated, somebody is editing it.', ZPSC_TEXT_DOMAIN ),

                /* size chart count deleted */

                'deleted'   => esc_html__( $bulk_counts['deleted'] . ' Size Chart permanently deleted.', ZPSC_TEXT_DOMAIN ),

                /* size chart count trashed */

                'trashed'   => esc_html__( $bulk_counts['trashed'] . ' Size Chart moved to the Trash.', ZPSC_TEXT_DOMAIN ),

                /* size chart count untrashed */

                'untrashed' => esc_html__( $bulk_counts['untrashed'] . ' Size Chart restored from the Trash.', ZPSC_TEXT_DOMAIN ),

            );



            return $bulk_messages;

        }



        /**

         * Callback function for field validation admin notice

         */

        public function ZPSC_field_validate_admin_notice() {



                $screen = get_current_screen();



                    /**

                     * Check current post type is "zpsc-size-chart"

                     */

                    if ('zpsc-size-chart' === $screen->post_type) {



                        if ( isset($_GET[self::ZPSC_CHART_ERROR_PARAM]) && isset($_GET[self::ZPSC_CHART_OPT_ERROR_PARAM]) ) {                    



                            $zpsc_chart_errorCode = (int) sanitize_text_field( $_GET[self::ZPSC_CHART_ERROR_PARAM] );

                            $zpsc_opt_errorCode = (int) sanitize_text_field( $_GET[self::ZPSC_CHART_OPT_ERROR_PARAM] );



                            if( ($zpsc_chart_errorCode == 1) && ($zpsc_opt_errorCode == 2) ){                           



                                echo '<div class="error"><p>'; 

                                echo esc_html__('Your size chart will be save as a pending due to "Create Size Chart" and "Select Product or Category" are empty. Please enter values in both required fields.', ZPSC_TEXT_DOMAIN);

                                echo "</p></div>";                          



                            }

                        } else if (isset($_GET[self::ZPSC_CHART_ERROR_PARAM])) {                    



                            $zpsc_chart_errorCode = (int) sanitize_text_field( $_GET[self::ZPSC_CHART_ERROR_PARAM] );



                            if($zpsc_chart_errorCode == 1){                           



                                echo '<div class="error"><p>'; 

                                echo esc_html__('Your size chart will be save as a pending due to "Create Size Chart" is empty. Please enter value in both required field.', ZPSC_TEXT_DOMAIN);

                                echo "</p></div>";                          



                            }

                        } else if ( isset($_GET[self::ZPSC_CHART_OPT_ERROR_PARAM]) ) {                    



                            $zpsc_opt_errorCode = (int) sanitize_text_field( $_GET[self::ZPSC_CHART_OPT_ERROR_PARAM] );



                            if($zpsc_opt_errorCode == 2){                           



                                echo '<div class="error"><p>'; 

                                echo esc_html__('Your size chart will be save as a pending due to "Select Product or Category" is empty. Please enter value in required field.', ZPSC_TEXT_DOMAIN);

                                echo "</p></div>";                          



                            }

                        } 



                    }                                            

             }



        /**

         * Add admin styles and scripts

         */

        public function ZPSC_admin_enqueue() { 



           /**

            * check current screen

            */

            $screen = get_current_screen();



            if ('zpsc-size-chart' === $screen->post_type) {



                /**

                 * admin style

                 */

                wp_enqueue_style('zpsc-admin-css', plugins_url('includes/admin/css/zpsc-admin.css', __FILE__));

                wp_enqueue_style('zpsc-multi-select-css', plugins_url('includes/admin/css/zpsc-multi-select2.css', __FILE__));



                /**

                 * admin script 

                 */

                wp_enqueue_script('zpsc-admin-js', plugins_url('includes/admin/js/zpsc-admin.js', __FILE__));

                wp_enqueue_script('zpsc-multi-select-js', plugins_url('includes/admin/js/zpsc-multi-select2.js', __FILE__));



             }

        }



        /**

         * Add frontend styles and scripts 

         */

        function ZPSC_front_Enqueue() {



            /**

             * Frontend style 

             */

            wp_enqueue_style('zpsc-frontend-css', plugins_url('includes/public/css/zpsc-frontend.css', __FILE__));

        }      



        /**

         * Add size chart table tab to product page

         */

        function ZPSC_product_tab($tabs) {



            global $post;



            /**

             * create argument for get size chart post type data

             */

            $zpsc_tab_args = array(

                'post_per_page' => -1,

                'post_type'     => 'zpsc-size-chart',

                'post_status'   => 'publish'

            );



            /**

             * Get size chart post type data

             */

            $zpsc_charts = get_posts( $zpsc_tab_args );         



            /**

             * Count size chart post type data

             */

            if ( count( $zpsc_charts ) > 0 ) {



                foreach ( $zpsc_charts as $zpsc_chart ) {



                /**

                 * Get the selected products

                 */         

                 $zpsc_product_arr = get_post_meta( $zpsc_chart->ID, 'zpsc_product', true );



                 $zpsc_cat_id = wp_get_post_terms( $post->ID, 'product_cat', array('fields'=>'ids'));



                    if(in_array($post->ID, $zpsc_product_arr)){ 



                       /**

                        * Create size chart tab with chart id for product selection

                        */

                        $tabs[ 'zpsc-size-chart-tab-' . $zpsc_chart->ID ] = array(

                            'title'         => $zpsc_chart->post_title,

                            'priority'      => 99,

                            'callback'      => array( $this, 'zpsc_chart_tab_content' ),

                            'zpsc_size_chart_id' => $zpsc_chart->ID

                        );

                    } 



                    if(array_intersect($zpsc_cat_id, $zpsc_product_arr)){



                       /**

                        * Create size chart tab with chart id for category selection

                        */

                        $tabs[ 'zpsc-size-chart-tab-' . $zpsc_chart->ID ] = array(

                            'title'         => $zpsc_chart->post_title,

                            'priority'      => 99,

                            'callback'      => array( $this, 'zpsc_chart_tab_content' ),

                            'zpsc_size_chart_id' => $zpsc_chart->ID

                        );

                    }



                }

            }   

            return $tabs;

        }



        /**

         * Add size chart table content in assigned product page

         * @param string $key the key of the tab

         * @param array  $tab array that contains info of tab (title, priority, callback, zpsc_size_chart_id)

         */

        function zpsc_chart_tab_content($key, $tab){



            global $post;



            if ( !isset( $tab[ 'zpsc_size_chart_id' ] ) )

                return;



            /**

             * Get the size chart table id 

             */

            $zpsc_chart_id = $tab[ 'zpsc_size_chart_id' ];



            /**

             * Get the chart table data 

             */

            $zpsc_table_data = get_post_meta( $zpsc_chart_id, 'zpsc_table_data', true );



            /**

             * Decode the table data

             */

            //$zpsc_d_data = json_decode($zpsc_table_data);            

            //start code for removed column
            $zpsc_d_data_original = json_decode($zpsc_table_data);
            
            if(!empty($zpsc_d_data_original)){
                $rowCount = count($zpsc_d_data_original); 
                $colCount = count($zpsc_d_data_original[0]);                        
                
                //edit or add screen checked here.          
                if( $rowCount > 1 || $colCount > 1 ){
                    
                    //key store in array for every empty cell value 
                    $arrAllColsValue = [];

                    //array empty check here.
                    if(!empty($zpsc_d_data_original)){
                        foreach ($zpsc_d_data_original as $keyRow => $arrRow) {
                            foreach ($arrRow as $keyCol => $cellValue) {
                                //cell value empty then key value push in array 
                                if(empty($cellValue)){  
                                    array_push($arrAllColsValue, $keyCol);
                                }
                            }
                        }
                        
                        //key value count here.
                        $dubArrValue = array_count_values($arrAllColsValue);
                        
                        //all empty value column key separate here
                        $arrColRemove = [];

                        foreach ($dubArrValue as $key => $value) {

                            //max row value checked with value and make array here.
                            if($rowCount == $value){
                               array_push($arrColRemove, $key); 
                            }
                        }
                                
                        $zpsc_d_data = [];

                        for ($i = 0; $i < $rowCount ; $i++) {
                            for ($j = 0; $j < $colCount; $j++) {

                                //skip for empty column name
                                if (in_array($j, $arrColRemove)) {
                                  continue;
                                }

                                $zpsc_d_data[$i][$j] = $zpsc_d_data_original[$i][$j]; 
                            }
                        }
                    }
                }else{
                    // add new array then defult array set here
                    $zpsc_d_data = $zpsc_d_data_original;                
                }
            }else{
                $zpsc_d_data = [[""]];
            }
            //end code for removed column

            /**

             * Get the selected products

             */

            $zpsc_product_ids = get_post_meta( $zpsc_chart_id, 'zpsc_product', true );



            $zpsc_cat_id = wp_get_post_terms( $post->ID, 'product_cat', array('fields'=>'ids'));



            if( in_array($post->ID, $zpsc_product_ids) || array_intersect($zpsc_cat_id, $zpsc_product_ids) ){                     



            ?>



            <div class="zpsc-product-wrapper">

                <table id="zpsc-product-table">

                    <thead>

                    <tr>

                        <?php foreach($zpsc_d_data[0] as $zpsc_col): ?>

                        <th><?php echo esc_html__( $zpsc_col, ZPSC_TEXT_DOMAIN ); ?></th>

                        <?php endforeach; ?>

                    </tr>

                    </thead>



                    <tbody>

                        <?php foreach($zpsc_d_data as $zpsc_row_id => $zpsc_row):                              



                            if($zpsc_row_id > 0){ ?>

                            <tr>

                            <?php foreach($zpsc_row as $zpsc_col): ?>

                                <td><?php echo str_replace('"', '&quot;', $zpsc_col) ?></td>

                            <?php endforeach; ?>

                            </tr>

                          <?php  } 

                            endforeach; ?>

                    </tbody>

                </table>

            </div>

            <?php

           }

        }

    }

}; 





/**

 * Define object of class & access method through this 

 */

if (class_exists('ZPSC_product_size_chart')) {



    /**

     * Define object of class

     */

    $zpsc_size_chart = new ZPSC_product_size_chart();



    /**

     * Call member function of class 

     */

    $zpsc_size_chart->ZPSC_register();

}



/**

 * Register activation hook 

 */

register_activation_hook(__FILE__, array($zpsc_size_chart, 'ZPSC_activate'));



/**

 * Register Deactivation hook 

 */

register_deactivation_hook(__FILE__, array($zpsc_size_chart, 'ZPSC_deactivate'));



} else{



    /**

     * Display admin notice if woocommerce plugin not activate

     */

    add_action('admin_notices', 'ZPSC_adminNotice');



    /**

     * Callback function for admin notice

     */

    function ZPSC_adminNotice() {



            $zpsc_plugin = plugin_basename(__FILE__);      



            echo '<div class="error"><p>'; 

            echo esc_html__('Zestard Product Size Chart requires active version of ', ZPSC_TEXT_DOMAIN); 



            echo '<a href="https://wordpress.org/plugins/woocommerce/" target="_blank">'; 

            echo esc_html__('WooCommerce', ZPSC_TEXT_DOMAIN);

            echo "</a>";



            echo esc_html__(' plugin.', ZPSC_TEXT_DOMAIN);

            echo "</p></div>";



           /**

            * Do not allow this plugin to activate

            */

            deactivate_plugins( $zpsc_plugin );



           /**

            * Unset the $_GET variable which triggers the activation message

            */

            unset($_GET['activate']);

    }

}