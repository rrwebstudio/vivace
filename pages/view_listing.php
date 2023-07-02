<?php

// Connect to DB
require(ROOT_PATH.'db.php');

class ViewListing {

    public $id;

    function set_id($id) {
        $this->id = $id;
    }

    function get_page(){
        global $connect_db, $widget, $current_user;

        $post_id = $this->id;

        // Get post data from database
        $get_posts = $connect_db->prepare("SELECT ID, user, title, content, post_date, modified_date, rent_price, event_cat, event_type, venue_cat, venue_type, set_photo, discount FROM posts WHERE ID = ?");
        $get_posts->bind_param('i', $post_id);
        $get_posts->execute();
        $get_posts->store_result();
        $get_posts->bind_result($postid, $userid, $title, $contenttxt, $postdate, $modifieddate, $rentprice, $eventcat, $eventtype, $venuecat, $venuetype, $setphoto, $discount);
        $get_posts->fetch();
        

        //Get poster's contact details from database
        $get_author = $connect_db->prepare("SELECT ID, email, company_name, follower_discount FROM users WHERE ID = ?");
        $get_author->bind_param('i', $userid);
        $get_author->execute();
        $get_author->store_result();
        $get_author->bind_result($author_id, $author_email, $author_company, $global_discount);
        $get_author->fetch();

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

        // Set profiile id and listing id
        $widget->set_profile($userid);
        $widget->set_listing($post_id);

        // Discount if aplicable
        $applicable_discount = $discount > 0 ? $discount : $global_discount;     
        $discount_amount =  $rentprice * ($applicable_discount / 100);
        $discount_price = $rentprice - $discount_amount;
        
        // Check if current usser is following profile
        $is_following = $widget->is_following();

        $ref_url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; 

        // Display content of page
        $content = '
        <div class="container-lg py-5">
            <div class="row gx-5">
                <div class="col">
                    <div class="ratio ratio-1x1 bg-image" style="background-image:url('.$bg_setphoto.')">
                    <img class="listing-image d-none img-fluid" src="'.$bg_setphoto.'">
                    </div>
                </div>
                <div class="col-5">
                    <h1 class="h3">'.$title.'</h1>
                    <div class="h4 border-bottom pb-4 mb-3  fw-light">';
                    if($applicable_discount > 0 && $is_following == true ) {
                        $content .='<span class="text-decoration-line-through">₱'.$money_formatter->format($rentprice).'</span> ';
                        $content .='<span class="text-danger">₱'.$money_formatter->format($discount_price).'</span>';
                    }  else {
                        $content .='₱'.$money_formatter->format($rentprice);
                    } 
                    if($userid != $current_user) {
                        $content .='<p class="m-0"><a href="'.$ref_url.'&bookmark='.$post_id.'" class="bg-light btn d-inline py-1 px-2 small text-uppercase">Bookmark</a></p>';
                    }
                    $content .='
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
                    </div>';
                    if($current_user != $userid){
                        $content .='
                        <div class="d-grid gap-2">
                            <a class="btn btn-dark rounded-0 p-3 fw-bold" href="?page=account_dashboard&action=create_message&recipient_id='.$userid.'&listing_id='.$post_id.'" role="button">Send Inquiry</a>
                        </div>';
                    }
                    $content .='
                </div>
                <div class="col profile">
                '.$widget->profile_box().'
                '.$widget->profile_user_data().'
                </div>
            </div>
        </div>
        ';        

        return $content;

    }
}
?>