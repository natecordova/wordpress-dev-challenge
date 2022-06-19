<?php

require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php');

// List Table Verify Links

class VerifyLinksListTableClass extends WP_List_Table{

    public function vl_list_table_data(){

        $wrong_links_posts = get_option( 'wrong_links' );

        $links_options = []; 

        if (is_array($wrong_links_posts) || is_object($wrong_links_posts)){
            foreach( $wrong_links_posts as $val ){

                $links_options[] = array(      
                    "url" => $val['url'],
                    "status" => $val['status'],
                    "origin" => $val['origin']        
                );
                
            }
        }
        
        return $links_options;
    }


    public function prepare_items(){

        $this->items = $this->vl_list_table_data();

        $columns = $this->get_columns();

        $this->_column_headers = array($columns);

    }

    public function get_columns(){

        $columns = array(
            'url' => 'Url',
            'status' => 'Status',
            'origin' => 'Origin'
        ); 

        return $columns;

    }

    public function column_default($item, $column_name){
        switch ($column_name) {
            case 'url':
            case 'status':
            case 'origin':
                return $item[$column_name];
            default:
                return 'No value';
        }
    }

}

function verify_links_list_table_layout(){

    $verify_links_list_table = new VerifyLinksListTableClass();

    $verify_links_list_table->prepare_items();

    $verify_links_list_table->display();

}

verify_links_list_table_layout();