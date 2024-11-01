/**
 * Document ready function 
 */
jQuery(document).ready(function() {

    /**
     * Define variables
     */
    var zpsc_table = jQuery( '#zpsc-chart-table' );
    var zpsc_num_rows = zpsc_table.find( 'tr' ).length - 1;
    var zpsc_num_cols = zpsc_table.find( 'th' ).length - 1;
    var zpsc_h_table_input = jQuery( '#zpsc-chart-table-hidden' );

    /**
     * Create function for matrix of table
     */
    function zpsc_matrix_of_table() {

            var zpsc_matrix = [];

            zpsc_table.find( 'tbody tr' ).each( function () {

                var zpsc_cols   = [];
                var zpsc_all_td = jQuery( this ).find( 'td' );

                var zpsc_all_td_length = jQuery( this ).find( 'td' ).length;
                var cellFlag = 0;
                var rowFlagValue = true;

                zpsc_all_td.each( function () {

                    if ( !jQuery( this ).is( '.zpsc-chart-table-button-container' ) ) {

                        var zpsc_input_value = jQuery( this ).find( 'input' ).val();

                        /*if (zpsc_input_value != null && zpsc_input_value != '') {
                            zpsc_cols.push( zpsc_input_value );
                        }*/

                        zpsc_cols.push( zpsc_input_value );

                        if(zpsc_input_value.length == 0 ){
                            if(cellFlag <= zpsc_all_td_length ){
                                cellFlag++; 
                            }
                        }
                    }
                } );

                if (zpsc_cols.length == cellFlag) {
                    rowFlagValue = false;
                }

               /**
                * Add column array value in matrix array if no empty value inside the array
                */
                if (zpsc_cols.length != 0) {   
                    if(rowFlagValue){
                        zpsc_matrix.push( zpsc_cols );
                    }
                }
            } );   

           /**
            * add all input value in hidden field
            */
            zpsc_h_table_input.val( JSON.stringify( zpsc_matrix ) );
    };

    /**
     * Create function for create row
     * @return string zpsc_row_html
     */
    function zpsc_create_row() {

         var zpsc_row_html = '<tr>';

            for ( var i = 0; i < zpsc_num_cols; i++ ) {
               zpsc_row_html += '<td><input class="zpsc-chart-input-table" type="text"></td>';
            }

            zpsc_row_html += '<td class="zpsc-chart-table-button-container">';
            zpsc_row_html += '<input type="button" class="zpsc-chart-add-row zpsc-chart-table-btn-add" value="+">';
            zpsc_row_html += '<input type="button" class="zpsc-chart-del-row zpsc-chart-table-btn-del" value="-">';
            zpsc_row_html += '</tr>';

            return zpsc_row_html;
    }

    /**
     * Create function for create column
     */
    function zpsc_create_column(zpsc_cell_id) {

        var zpsc_tmp_col_btn  = '<th>';
            zpsc_tmp_col_btn  += '<input type="button" class="zpsc-chart-add-col zpsc-chart-table-button-add" value="+">';
            zpsc_tmp_col_btn  += '<input type="button" class="zpsc-chart-del-col zpsc-chart-table-button-del" value="-">';
            zpsc_tmp_col_btn  += '</th>';

        var zpsc_tmp_col = '<td><input class="zpsc-chart-input-table" type="text"></td>';

        zpsc_table.find( 'thead tr' ).find( 'th:eq(' + zpsc_cell_id + ')' ).after( zpsc_tmp_col_btn );

        zpsc_table.find( 'tbody tr' ).each( function () {
            jQuery( this ).find( 'td:eq(' + zpsc_cell_id + ')' ).after( zpsc_tmp_col );
        } );
    }

    /**
     * Create function for remove column
     */
     function zpsc_remove_col(zpsc_cell_id) {

        zpsc_table.find( 'thead tr' ).find( 'th:eq(' + zpsc_cell_id + ')' ).remove();
        zpsc_table.find( 'tbody tr' ).each( function () {
            jQuery( this ).find( 'td:eq(' + zpsc_cell_id + ')' ).remove();
        } );
     }

    /**
     * Add row button onclick event 
     */
    zpsc_table.on( 'click', '.zpsc-chart-add-row', function () {

        var zpsc_this_cell = jQuery( this ).closest( 'td' );
        var zpsc_this_row  = zpsc_this_cell.closest( 'tr' );

        zpsc_num_rows++;

        zpsc_this_row.after( zpsc_create_row() );
        zpsc_matrix_of_table();
    })

    /**
     * Remove row button onclick event 
     */
    zpsc_table.on( 'click', '.zpsc-chart-del-row', function () {

        if ( zpsc_num_rows < 2 )
            return;

        var zpsc_this_cell = jQuery( this ).closest( 'td' );
        var zpsc_this_row  = zpsc_this_cell.closest( 'tr' );

        zpsc_num_rows--;

        zpsc_this_row.remove();
        zpsc_matrix_of_table();
    } )

    /**
     * Add column button onclick event 
     */
    zpsc_table.on( 'click', '.zpsc-chart-add-col', function () {
        var zpsc_this_cell = jQuery( this ).closest( 'th' );
        var zpsc_cell_id = zpsc_this_cell.index();
        zpsc_num_cols++;

        zpsc_create_column(zpsc_cell_id);
        zpsc_matrix_of_table();
    })

    /**
     * Remove column button onclick event 
     */
    zpsc_table.on( 'click', '.zpsc-chart-del-col', function () {

        if ( zpsc_num_cols < 2 )
            return;

        var zpsc_this_cell = jQuery( this ).closest( 'th' );
        var zpsc_cell_id   = zpsc_this_cell.index();

            zpsc_num_cols--;

            zpsc_remove_col( zpsc_cell_id );
            zpsc_matrix_of_table();
        } )

    /**
     * add input value in hidden field
     */
    zpsc_table.on( 'keyup', 'input', function (e) {            

            var zpsc_this_input = jQuery( e.target );
            var zpsc_i_value = zpsc_this_input.val();

            // remove html tags and wrong apics
            if ( zpsc_i_value.search( /<[^>]+>/ig ) >= 0 || zpsc_i_value.search( '<>' ) >= 0 || zpsc_i_value.search( '“' ) >= 0 ) {
                zpsc_this_input.val( zpsc_i_value.replace( /<[^>]+>/ig, '' ).replace( '<>', '' ).replace( '“', '"' ) );
            }

            zpsc_matrix_of_table();
        } );

    /***************************************************************************************************************************/

    /**
     * Call select2 method for multiselect products or categories dropdown
     */
    jQuery('#zpsc_product').select2({
        placeholder: 'Select Product or Category'
    });

    /**
     * Add html for select all icon
     */
    jQuery('.zpsc-product-select[multiple]').siblings('.select2-container').append('<span class="zpsc-select-all"></span>');
    
    /**
     * Check total options with selected options when page load
     */
     var zpsc_selected_opt = jQuery('#zpsc_product').find("option:selected").length;
     var zpsc_total_opt = jQuery('#zpsc_product').find("option").length;
     
    if( zpsc_total_opt == zpsc_selected_opt){
        /**
         * Add check all icon if selected all options on previous
         */
         jQuery(".zpsc-select-all").addClass("zpsc-deselect-all");
     }

    /**
     * Call function onclick of select all
     */
    jQuery(document).on('click', '.zpsc-select-all', function () {
        jQuery(this).toggleClass("zpsc-deselect-all");
        zpsc_selectAll(jQuery(this).siblings('.selection').find('.select2-search__field'));
    });


    jQuery('#zpsc_product').on('select2:unselect', function() {
       /**
        * Remove check all icon
        */
        jQuery(".zpsc-select-all").removeClass("zpsc-deselect-all");
    });

        
   /**
    * Select all function
    */
    function zpsc_selectAll(that) {        

      var zpsc_selectAll = true;
      var zpsc_existUnselected = false;
      var zpsc_id = that.parents("span[class*='select2-container']").siblings('select[multiple]').attr('id');
      var zpsc_item = jQuery("#" + zpsc_id);

        zpsc_item.find("option").each(function (k, v) {           
                
            if (!jQuery(v).prop('selected')) {
                zpsc_existUnselected = true;
                return false;
            }
        });

        zpsc_selectAll = zpsc_existUnselected ? zpsc_selectAll : !zpsc_selectAll;
        zpsc_item.find("option").prop('selected', zpsc_selectAll).trigger('change');
    }

    /***************************************************************************************************************************/

    /**
     * Disabled save draft button if current post type is size chart
     */       
    jQuery('.post-type-zpsc-size-chart #save-post').attr("disabled", true);

})