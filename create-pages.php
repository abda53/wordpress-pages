<?php


/* This should be included in your functions.php file and called once (or whenever you need it). See notes at the bottom on how to use it */

function create_new_pages(){

    $needed_pages = array(
        'Products' => array(
            'Scanners', 'Rabbit Printers', 'Visioneer Software', 'ABBYY Software', 'Visioneer Vast Network'
        ),
        'Solutions' => array(
            'Out-of-the-Box', 'Scanner Rental', 'Professional Services'
        ),
        'Support' => array(
            'Drivers and Manuals', 'FAQs', 'Parts and Accessories', 'Visioneer Video Library', 'Product Registration', 'Warranties', 'Leave a Review'
        ),
        'Company' => array(
            'About Us', 'Sustainability', 'Partner Program'
        ),
        'Contact' => array(
            'Where to Buy'
        )
    );
    $needed_pages_total = count($needed_pages) + array_sum(array_map("count", $needed_pages));

    $debug = @$_GET['debug'] ? TRUE : FALSE;

    // CHECK PARENT PAGES

    $existing_pages = get_pages();
    $existing_pages_array = array();

    foreach($existing_pages AS $existing_page){
        if($existing_page->post_title!='Homepage'){
            if(!$existing_page->post_parent){
                $existing_pages_array[$existing_page->post_title] = array();
            }
            else{
                $this_parent = get_post($existing_page->post_parent);
                $parent_slug = $this_parent->post_title;
                $existing_pages_array[$parent_slug][] = $existing_page->post_title;
            }
        }
    }



    // check if needed top level page exists
    foreach($needed_pages AS $parent=>$needed){
        if(!array_key_exists($parent, $existing_pages_array)){
            $this_user_id = get_current_user_id() ? get_current_user_id() : 0;
            $page_info = array(
                'comment_status' => 'close',
                'ping_status'    => 'close',
                'post_author'    => $this_user_id,
                'post_title'     => $parent,
                'post_status'    => 'publish',
                'post_content'   => '',
                'post_type'      => 'page',
                //'post_parent'    => 'id_of_the_parent_page_if_it_available'
            );
            $page_id = wp_insert_post( $page_info );
        }
    }

    if($debug){echo 'Added parent pages<br>';}



    // CHECK SUBPAGES

    //recheck existing pages
    $existing_pages = get_pages();
    $existing_pages_array = array();

    if($debug){
        echo '
        <div style="display: block;">
            <pre style="display: block; width: 48%; margin-right: 1px; padding-right: 1px; float: left; height: 500px; overflow: auto; border-bottom: 1px solid black; border-right: 1px solid black">'; print_r($needed_pages); echo '</pre>
            <pre style="display: block; width: 50%; float: left; height: 500px; overflow: auto; border-bottom: 1px solid black">'; print_r($existing_pages); echo '</pre>
            <div style="clear:both"></div>
        </div>
            <div style="clear:both"></div>
        ';
    }

    $this_user_id = get_current_user_id() ? get_current_user_id() : 0;


    // loop through each existing main page, get ID
    $i = 0;
    foreach($existing_pages AS $page){
        if($debug){echo '<strong>'.$page->post_title.'</strong><Br>';}
        // look for corresponding needed page if has no parent
        if($page->post_parent==0){
            //make sure parent is an array
            if(is_array($needed_pages[$page->post_title])){
                // loop through each needed page's child
                foreach($needed_pages[$page->post_title] AS $needed_child){
                    if(!get_page_by_title($needed_child)){
                        $page_info = array(
                            'comment_status' => 'close',
                            'ping_status'    => 'close',
                            'post_author'    => $this_user_id,
                            'post_title'     => $needed_child,
                            'post_status'    => 'publish',
                            'post_content'   => '',
                            'post_type'      => 'page',
                            'post_parent'    => $page->ID
                        );
                       $page_id = wp_insert_post( $page_info );
                    }
                }
            }
        }
        ++$i;
        if($i > $needed_pages_total){
            echo 'ERROR: You might be in an infinite loop'; exit;
        }
    }

    echo 'Created pages';     exit;

}

// load this in the admin, append &create_pages='' whatever your secret key is below
if(is_admin() && (isset($_GET['create_pages']) && $_GET['create_pages']=='mc893nois')){
    add_action('init','create_new_pages');
}

