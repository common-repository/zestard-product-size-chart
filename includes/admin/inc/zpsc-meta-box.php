<?php

/**

 * Register meta box called "Create Size Chart"

 */

add_meta_box( 'zpsc-create-size-chart', esc_html__( 'Create Size Chart', ZPSC_TEXT_DOMAIN ), 'ZPSC_size_chart_html', 'zpsc-size-chart', 'normal', 'high');



/**

 * Create Size Chart Meta box display callback

 * @param string $post

 */

function ZPSC_size_chart_html($post) {



            global $pagenow;



            /**

             * Check if current page is post.php or post-new.php

             */

            if ( !in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {

                return;

            }



            /**

             * Add a nonce field so we can check for it later.

             */

            wp_nonce_field( 'zpsc_chart_meta_box', 'zpsc_chart_meta_box_nonce' );



            /**

             * Retrieve an existing value from the database.

             */

            $zpsc_table_data = get_post_meta( $post->ID, 'zpsc_table_data', true ) ? get_post_meta( $post->ID, 'zpsc_table_data', true ) : '[[""]]';



            /**

             * Decode table data

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
            ?>



            <div id="zpsc-chart-metabox" class="zpsc-chart-metabox">



                <input id="zpsc-chart-table-hidden" type="hidden" name="zpsc_table_data" 

                value='<?php echo str_replace( '\'', '&apos;', $zpsc_table_data ); ?>'>



                <table id="zpsc-chart-table">

                    <thead>

                    <tr>

                        <?php foreach($zpsc_d_data[0] as $zpsc_col): ?>

                        <th>

                            <input type="button" class="zpsc-chart-add-col zpsc-chart-table-button-add" value="+">

                            <input type="button" class="zpsc-chart-del-col zpsc-chart-table-button-del" value="-">

                        </th>

                        <?php endforeach; ?>

                        <th></th>

                    </tr>

                    </thead>



                    <tbody>

                        <?php foreach($zpsc_d_data as $zpsc_row): ?>

                        <tr>



                         <?php foreach($zpsc_row as $zpsc_col): ?>

                            <td>

                               <input class="zpsc-chart-input-table" type="text" name="zpsc_chart_input" id="zpsc_chart_input" value="<?php echo str_replace('"', '&quot;', $zpsc_col) ?>">

                            </td>

                            <?php endforeach; ?>



                            <td class="zpsc-chart-table-button-container">

                                <input type="button" class="zpsc-chart-add-row zpsc-chart-table-btn-add" value="+">

                                <input type="button" class="zpsc-chart-del-row zpsc-chart-table-btn-del" value="-">

                            </td>

                        </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

        </div>

    <?php

}



/**

 * Register meta box called "Create Chart Options"

 */

add_meta_box( 'zpsc-chart-options', esc_html__( 'Create Chart Options', ZPSC_TEXT_DOMAIN ), 'ZPSC_chart_option_html' , 'zpsc-size-chart', 'normal', 'default' );



/**

 * Chart Option Meta box display callback

 */

function ZPSC_chart_option_html($post) {



            global $pagenow;



            /**

             * Check if current page is post.php or post-new.php

             */

            if ( !in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {

                return;

            }



            /**

             * Add a nonce field so we can check for it later.

             */

            wp_nonce_field( 'zpsc_chart_opt_meta_box', 'zpsc_chart_opt_meta_box_nonce' );



            /**

             * Retrieve an existing value from the database.

             */

            $zpsc_pro_cat = get_post_meta( $post->ID, 'zpsc_product', true );      



            /**

             * Create Product argument

             */

            $zpsc_prod_args = array(

                'post_type'      => 'product',

                'post_status'    => 'publish',

                'posts_per_page' => -1,                

                'orderby'        => 'title',

                'order'          => 'ASC',

                'fields'         => 'ids'

            );





            /**

             * Get default product category id

             */

            $zpsc_default_cat = get_option( 'default_product_cat' );  



            /**

             * Get Product data

             */

            $zpsc_products = get_posts($zpsc_prod_args);



            $zpsc_cat_args = array(

                'orderby'    => 'name',

                'order'      => 'asc',

                'exclude'    => $zpsc_default_cat,

                'hide_empty' => true

            );

             

            $zpsc_categories = get_terms( 'product_cat', $zpsc_cat_args );

    ?>



    <div class="zpsc-option-metabox">

        <div id="zpsc-product-container" class="zpsc-product-container">

            <label for="zpsc_product"><?php echo esc_html__( 'Select Product or Category', ZPSC_TEXT_DOMAIN ); ?></label>

            <div class="zpsc-product-wrapper">

                <select id="zpsc_product" name="zpsc_product[]" class="zpsc-product-select" multiple="multiple">

                <?php



               /**

                * Count category data

                */

                if ( count( $zpsc_categories ) > 0 ) { ?>

                    <optgroup label="Category">                    

                    <?php



                    foreach ( $zpsc_categories as $zpsc_category ) {



                            $zpsc_cat_id = $zpsc_category->term_id;

                            $zpsc_cat_name = $zpsc_category->name;

                            $zpsc_cat_slug = $zpsc_category->slug;  

                            if(!empty($zpsc_pro_cat)){
                                $zpsc_cat_selected = selected(true, in_array($zpsc_cat_id, $zpsc_pro_cat) , false);
                            }else{
                                $zpsc_cat_selected = "";
                            }

                            ?>

                            <option value="<?php echo $zpsc_cat_id; ?>" <?php echo $zpsc_cat_selected; ?> >

                               <?php echo esc_html__( $zpsc_cat_name, ZPSC_TEXT_DOMAIN ); ?>

                            </option>

                        <?php

                    }

                    ?>

                </optgroup>

                <?php                

                }



               /**

                * Count Product data

                */

                if ( count( $zpsc_products ) > 0 ) {

                    ?>

                    <optgroup label="Product"> 

                    <?php



                    foreach ( $zpsc_products as $zpsc_product_id ) {

                        $zpsc_product_title = get_the_title( $zpsc_product_id );

                        if(!empty($zpsc_pro_cat)){
                            $zpsc_pro_selected = selected(true, in_array($zpsc_product_id, $zpsc_pro_cat) , false);
                        }else{
                            $zpsc_pro_selected = "";
                        }

                        ?>



                        <option value="<?php echo $zpsc_product_id; ?>" <?php echo $zpsc_pro_selected; ?> >

                           <?php echo esc_html__( $zpsc_product_title, ZPSC_TEXT_DOMAIN); ?>

                        </option>

                        <?php

                    }

                    ?>

                    </optgroup>

                <?php

                } 

                ?>                

            </select>    

            <span class="zpsc-product-desc"><?php echo esc_html__( 'Select the product or category in which this size chart has to be shown.', ZPSC_TEXT_DOMAIN ); ?></span>   

            </div>

        </div>      

    </div>

<?php

}

?>