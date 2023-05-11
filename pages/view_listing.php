<?php

class ViewListing {

    public $id;

    function set_id($id) {
        $this->id = $id;
    }

    function get_page(){        

        // Connect to mysql DB
        $connect_db = connect_db();
        // Check connection
        if ($connect_db->connect_error) {
            die("Connection failed: " . $connect_db->connect_error);
        }

        $post_id = $this->id;

        // Get post data from database
        $get_posts = $connect_db->prepare("SELECT ID, user, title, content, post_date, modified_date, rent_price, event_cat, event_type, venue_cat, venue_type, set_photo FROM posts WHERE ID = ?");
        $get_posts->bind_param('i', $post_id);
        $get_posts->execute();
        $get_posts->store_result();
        $get_posts->bind_result($postid, $userid, $title, $contenttxt, $postdate, $modifieddate, $rentprice, $eventcat, $eventtype, $venuecat, $venuetype, $setphoto);
        $get_posts->fetch();
        

        //Get poster's contact details from database
        $get_author = $connect_db->prepare("SELECT ID, email, company_name FROM users WHERE ID = ?");
        $get_author->bind_param('i', $userid);
        $get_author->execute();
        $get_author->store_result();
        $get_author->bind_result($author_id, $author_email, $author_company);
        $get_author->fetch();

        // Close connection to db
        $connect_db->close();

        // Get image url
        $bg_setphoto = SITE_URL.'/uploads/'.$setphoto;

        // Format money
        $money_formatter = new NumberFormatter('en_GB', NumberFormatter::DECIMAL);
        $money_formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 2);

        // Get post categories
        $event_cats = explode(',', $eventcat);
        $venue_cats = explode(',', $venuecat); 
        $category_tag = new CategoryTag(); 
        
        // Require category arrays
        require(ROOT_PATH.'data/categories.php');

        $search  = array('(', ')', ' / ', ' - ',' ', '&');
        $replace = array('', '', '_', '_', '_','');
        

        // Display content of page
        $content = '
        <div class="container-lg py-5">
            <div class="row gx-5">
                <div class="col">
                    <div class="ratio ratio-1x1 bg-image" style="background-image:url('.$bg_setphoto.')">
                    </div>
                </div>
                <div class="col-5">
                    <h1 class="h3">'.$title.'</h1>
                    <div class="display-6 border-bottom pb-4 mb-3">
                    ₱'.$money_formatter->format($rentprice).'
                    </div>
                    <div class="list-content">
                    '.$contenttxt.'
                    <div class="tags my-4 py-1 border-top border-light">
                        Tags: ';                            
                        $content .= $category_tag->get_tags(
                                    $event_cats,
                                    $venue_cats,
                                    $event_tags,
                                    $venue_tags,
                                    $search,
                                    $replace,
                                    $event_cat,
                                    $foreign_events,
                                    $local_events,
                                    $venue_cat,
                                    $indoor_venue,
                                    $outdoor_venue);
                        $content .='
                    </div>
                    <div class="categories bg-light p-3 mb-4">
                            <div class="row mb-2">
                            <div class="col-auto">
                                Event Type: 
                            </div>
                            <div class="col">';                                
                            $content .= $category_tag->get_events($event_cats, $search, $replace);
                            $content .= '
                            </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-auto">
                                    Venue Type:
                                </div>
                                <div class="col">';
                                $content .= $category_tag->get_venues($venue_cats, $search, $replace);                                
                                $content .= '
                                </div>
                            </div> 
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <a class="btn btn-dark rounded-0 p-3" href="?page=search&view_listing='.$post_id.'&action=rent_set" role="button">Rent this Equipment Set</a>
                    </div>
                </div>
                <div class="col">
                    <div class="card shadow p-3 mt-2 px-3 mb-4">
                        <div class="mb-3">
                            <span class="lead d-block">'.$author_company.'</span>
                            <div class="d-grid gap-2">
                                    <a class="btn btn-outline-dark rounded-0 btn-sm" href="#" role="button">Follow</a>
                                    <a class="btn btn-outline-dark rounded-0 btn-sm" href="#" role="button">Message</a>
                                    <a class="btn btn-outline-dark rounded-0 btn-sm" href="#" role="button">View Profile</a>
                            </div>                            
                        </div>                        
                    </div>
                </div>
            </div>
        </div>
        ';        

        return $content;

    }
}
?>