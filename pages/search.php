<?php

// Connect to DB
require(ROOT_PATH.'db.php');

class Search {

    public $subpage;

    function get_page($subpage) {
        global $connect_db, $widget, $current_user;
        $user = $widget->get_user();
        $ref_url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    

        $content ='';
        
        if($user['user_type'] == 'client') {
            // Follow company
            if(isset($_GET['follow'])) {
                $account_id = $_GET['follow'];
                $check_follow = $connect_db->prepare("SELECT company_id FROM follows WHERE client_id = ? AND company_id = ?");
                $check_follow -> bind_param('ii', $current_user, $_GET['follow']);
                $check_follow->execute();
                $check_follow -> store_result();
                $check_follow -> bind_result($account_id);
                $check_follow->fetch();
                
                if($check_follow->num_rows == 0) {
                    $follow_account = "INSERT INTO follows (
                        client_id,
                        company_id
                    )
                    VALUES (
                        '$current_user',
                        '$account_id'
                    )";

                    if($connect_db->query($follow_account) === TRUE){
                        
                    }
                }                            
            }

            // Unfollow company
            if(isset($_GET['unfollow'])) {
                $account_id = $_GET['follow'];
                $check_follow = $connect_db->prepare("SELECT company_id FROM follows WHERE client_id = ? AND company_id = ?");
                $check_follow -> bind_param('ii', $current_user, $_GET['unfollow']);
                $check_follow->execute();
                $check_follow -> store_result();
                $check_follow -> bind_result($account_id);
                $check_follow->fetch();
                
                if($check_follow->num_rows == 1) {
                    $unfollow = $connect_db->prepare("DELETE FROM follows WHERE client_id = ? AND company_id = ?");
                    $unfollow->bind_param('ii', $current_user, $account_id);
                    $unfollow->execute();
                }                            
            }

            // Bookmark Listing
            if(isset($_GET['bookmark'])) {
                $listing_id = $_GET['bookmark'];
                $check_bookmark = $connect_db->prepare("SELECT ID FROM bookmarks WHERE client_id = ? AND listing_id = ?");
                $check_bookmark -> bind_param('ii', $current_user, $listing_id);
                $check_bookmark->execute();
                $check_bookmark -> store_result();
                $check_bookmark -> bind_result($bookmark_id);
                $check_bookmark->fetch();
                $bookmark_date = new DateTime();
                $bookmark_date =  $bookmark_date->format('Y-m-d H:i:s');
                
                if($check_bookmark->num_rows == 0) {
                    $bookmark_listing = "INSERT INTO bookmarks (
                        client_id,
                        listing_id,
                        bookmark_date
                    )
                    VALUES (
                        '$current_user',
                        '$listing_id',
                        '$bookmark_date'
                    )";

                    if($connect_db->query($bookmark_listing) === TRUE){
                        $content .= '
                        <div class="container-lg pt-5 pb-0">
                            <div class="row gx-5">
                                <div class="col">
                                    <div class="alert alert-success border-2 mb-4 p-3 shadow overflow-hidden" role="alert">Listing bookmarked.</div>
                                </div>
                            </div>
                        </div>';
                    }
                } else {
                    $content .= '
                        <div class="container-lg pt-5 pb-0">
                            <div class="row gx-5">
                                <div class="col">
                                    <div class="alert alert-danger border-2 mb-4 p-3 shadow overflow-hidden" role="alert">Listing already bookmarked.</div>
                                </div>
                            </div>
                        </div>';
                }                           
            }
        } else {
            if(isset($_GET['follow']) || isset($_GET['unfollow']) || isset($_GET['bookmark'])) {
                redirect($ref_url);
            }            
        }       

        // Sub page = view_listing
        // Specific page for a listing, opens when clicking "view details"
        if($subpage == 'view_listing') {

            // Get listing id and displays content
            $listing = new ViewListing();
            $listing_id = isset($_GET['view_listing']) ? $_GET['view_listing'] : null;
            $listing->set_id($listing_id);
            return $listing->get_page();

        }
        // Sub page = view_tag
        // Page for viewing a tag
        else if($subpage == 'view_tag' || $subpage == 'view_category') {
            // Require category arrays
            require(ROOT_PATH.'data/categories.php');
            // Get tag to be searched
            $selected_tag = isset($_GET['view_tag']) ? $_GET['view_tag'] : (isset($_GET['view_category']) ? $_GET['view_category'] : null);
            $type= isset($_GET['view_tag']) ? 'tag' : (isset($_GET['view_category']) ? 'category' : null);

            // Display posts under the searched tag or category
            $tag = new CategoryTag();
            $tag->set_type($type);
            $tag->set_tag($selected_tag);

            if(!empty($_SESSION['user_id'])) {
                $content .= '
                <div class="container-lg py-5">
                    <div class="row gx-5">
                        <div class="col">'
                        .$tag->view_tag($event_cat, $venue_cat, $event_tags, $venue_tags).
                        '</div>
                        <div class="col-3">'
                        .$widget->browse_by_events()
                        .$widget->browse_by_venue().
                        '</div>
                    </div>
                </div>
                '; 
            }
            else {
                $content .= '
                <div class="container-lg py-5">
                    <div class="row gx-5 mb-5">
                        <div class="col text-center">';
                            $content .= '<h2 class="h4">You need to login in order to view the results of your search.</h2>
                            <p>No account yet? Sign up!</p>';
                            $content .= '
                            <div class="d-grid gap-2 d-md-block mx-auto">
                                <a class="btn btn-dark rounded-0 btn-lg" href="?page=login" role="button">Login</a>
                                <a class="btn btn-primary rounded-0 btn-lg" href="?page=register" role="button">Register</a>
                            </div>';
                            $content .= '
                        </div>
                    </div>
                </div>
                '; 
             }            

            return $content;
        } else {
            $results = '';
            if(!empty($_SESSION['user_id'])) {

                // Require category arrays
                require(ROOT_PATH.'data/categories.php');

                // Get data from submitted search
                // $_POST data are keys to the category arrays
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $event_cat_key = is_numeric($_POST['event_category']) ? $_POST['event_category'] : null;
                    $event_type_key = is_numeric($_POST['event_type']) ? $_POST['event_type'] : null;
                    $venue_cat_key = is_numeric($_POST['venue_cat']) ? $_POST['venue_cat'] : null;
                    $venue_type_key = is_numeric($_POST['venue_type']) ? $_POST['venue_type'] : null;
                    $price_range_key = is_numeric($_POST['price_range']) ? $_POST['price_range'] : null;
                } else {

                    $event_cat_key = (isset($_SESSION['event_cat_key']) ? $_SESSION['event_cat_key'] : null);
                    $event_type_key = (isset($_SESSION['event_type_key']) ? $_SESSION['event_type_key'] : null);
                    $venue_cat_key = (isset($_SESSION['venue_cat_key']) ? $_SESSION['venue_cat_key'] : null);
                    $venue_type_key =(isset($_SESSION['venue_type_key']) ? $_SESSION['venue_type_key'] : null);
                    $price_range_key = (isset($_SESSION['price_range_key']) ? $_SESSION['price_range_key'] : null);
                }                

                // From the keys above, we get the selected categories and types
                $selected_event_cat = isset($event_cat_key) ? $event_cat[$event_cat_key] : null; // eg birthday party
                $selected_event_type = isset($event_type_key) ? $event_types[$event_type_key] : null; // local or foreign
                $selected_venue_cat = isset($venue_cat_key) ? $venue_cat[$venue_cat_key] : null; // eg garden venue
                $selected_venue_type = isset($venue_type_key) ? $venue_type[$venue_type_key] : null; // indoor or outdoor      

                
                if($selected_event_cat && $selected_event_type && $selected_venue_cat && $selected_venue_type){
                    
                    // Search the database based on selected search and price range
                    // Price range key = 0 : <= 499,000
                    if ($price_range_key == 0) {
                        $get_posts = $connect_db->prepare("SELECT 
                        ID, user, title, content, post_date, modified_date, rent_price, event_cat, event_type, venue_cat, venue_type, set_photo
                        FROM posts
                        WHERE
                        rent_price <= 499000
                        AND
                        event_cat LIKE '%$selected_event_cat%'
                        AND
                        event_type LIKE '%$selected_event_type%'
                        AND
                        venue_cat LIKE '%$selected_venue_cat%'
                        AND
                        venue_type LIKE '%$selected_venue_type%'
                        ORDER BY modified_date DESC
                        ");
                    }

                    // Price range key = 1 : BETWEEN '500000' AND '999000'
                    else if ($price_range_key == 1) {
                        $get_posts = $connect_db->prepare("SELECT 
                        ID, user, title, content, post_date, modified_date, rent_price, event_cat, event_type, venue_cat, venue_type, set_photo, discount
                        FROM posts
                        WHERE
                        rent_price BETWEEN '500000' AND '999000'
                        AND
                        event_cat LIKE '%$selected_event_cat%'
                        AND
                        event_type LIKE '%$selected_event_type%'
                        AND
                        venue_cat LIKE '%$selected_venue_cat%'
                        AND
                        venue_type LIKE '%$selected_venue_type%'
                        ORDER BY modified_date DESC
                        ");
                    }

                    // Price range key = 2 : >= 1000000
                    else if ($price_range_key == 2) {
                        $get_posts = $connect_db->prepare("SELECT 
                        ID, user, title, content, post_date, modified_date, rent_price, event_cat, event_type, venue_cat, venue_type, set_photo, discount
                        FROM posts
                        WHERE
                        rent_price >= 1000000
                        AND
                        event_cat LIKE '%$selected_event_cat%'
                        AND
                        event_type LIKE '%$selected_event_type%'
                        AND
                        venue_cat LIKE '%$selected_venue_cat%'
                        AND
                        venue_type LIKE '%$selected_venue_type%'
                        ORDER BY modified_date DESC
                        ");
                    }

                    // Execute the database search and get the results
                    $get_posts->execute();
                    $result = $get_posts->get_result();                                    
                    if($result->num_rows > 0) {
                        
                        $results = '
                        <div class="container-lg py-5">
                            <div class="row gx-5">
                                <div class="col">';
                        $results .= '<h2 class="h4 mb-5">Search results:</h2>';
                        while ($row = $result -> fetch_assoc()) {
                            //Get poster's contact details from database
                            $get_author = $connect_db->prepare("SELECT email, company_name, follower_discount FROM users WHERE ID = ?");
                            $get_author->bind_param('i', $row['user']);
                            $get_author->execute();
                            $get_author->store_result();
                            $get_author->bind_result($author_email, $author_company, $global_discount);
                            $get_author->fetch();
                            // Get post thumbnail
                            $bg_setphoto = SITE_URL.'/uploads/'.$row['set_photo'];
                            // Format money
                            $money_formatter = new NumberFormatter('en_GB', NumberFormatter::DECIMAL);
                            $money_formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 2);  
                            // Get discount if applicable
                            $applicable_discount = isset($row['discount']) && $row['discount'] > 0 ? $row['discount'] : $global_discount;     
                            $discount_amount =  $row['rent_price'] * ($applicable_discount / 100);
                            $discount_price = $row['rent_price'] - $discount_amount;
                            $widget->set_profile($row['user']);                                          
                            // Show result
                            $results .= '
                            <div class=" post post-'.$row['ID'].' card p-0 shadow mb-4">
                                <div class="card-body p-0">
                                    <div class="row">
                                        <div class="col-3 bg-image" style="background-image:url('.$bg_setphoto.')">
                                        </div>
                                        <div class="col p-3">                                                      
                                            <div class="post-content position-relative" style="height: 260px; overflow: hidden;">
                                                <h2 class="card-title h4 mb-1">'.$row['title'].'</h2>'
                                                .$row['content'].
                                            '</div>
                                        </div>  
                                        <div class="col-4 py-3 pe-4">
                                        <div class="alert alert-success py-1 px-2">';
                                            $results .='<div class="row gx-1 d-flex align-items-center mb-2 bg-white p-1 rounded">
                                            <div class="col-auto">
                                                <div class="round-circle" style="width: 20px;">';
                                                $results .= $widget->get_avatar().'
                                                </div>
                                            </div>
                                            <div class="col">
                                                <a class="text-dark fw-bold" href="?page=view_profile&id='.$row['user'].'">'.$author_company.'</a>
                                            </div>
                                            </div>';
                                            $is_following = $widget->is_following();                                          
                                            $results .='
                                            <div class="row">
                                                <div class="col fw-bold price-text text-center">';
                                                if($applicable_discount > 0 && $is_following == true ) {
                                                    $results .='<span class="text-decoration-line-through">₱'.$money_formatter->format($row['rent_price']).'</span>
                                                    <span class="text-danger fw-bold">₱'.$money_formatter->format($discount_price).'</span></p>';
                                                } else {
                                                    $results .='₱'.$money_formatter->format($row['rent_price']).'</p>';
                                                }                                                
                                                $results .='
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                <div class="d-grid gap-2">
                                                    <a class="btn btn-success btn-sm" href="?page=search&view_listing='.$row['ID'].'" role="button">View Details</a>
                                                </div>                                    
                                                </div>
                                            </div>
                                        </div>
                                        <div class="alert alert-light border py-1 px-2">
                                            <div class="post-meta">
                                                <div class="meta-date">';
                                                $results .= '
                                                    Posted on '.$row['post_date'].'
                                                </div>
                                            </div>
                                        </div>                                                                                
                                        <p>';
                                        if($is_following == false){
                                            $results .='<p class="mt-2 small text-muted">Follow '.$author_company.' to receive discounts and more!</p>
                                            <a href="'.$ref_url.'&follow='.$row['user'].'" class="bg-light btn d-inline py-1 px-2 small text-uppercase">Follow</a>';
                                        }
                                        $check_bookmark = $connect_db->prepare("SELECT ID FROM bookmarks WHERE client_id = ? AND listing_id = ?");
                                        $check_bookmark -> bind_param('ii', $current_user, $row['ID']);
                                        $check_bookmark->execute();
                                        $check_bookmark -> store_result();
                                        $check_bookmark -> bind_result($bookmark_id);
                                        if($check_bookmark->num_rows == 0) {
                                            $results .='                                        
                                            <a href="'.$ref_url.'&bookmark='.$row['ID'].'" class="bg-light btn d-inline py-1 px-2 small text-uppercase">Bookmark</a>';
                                        }                                        
                                        $results .=' 
                                        </p>
                                    </div>
                                    </div>                    
                                </div>
                            </div>
                            ';
                        }
                        $results .='
                            </div>
                            <div class="col-3">'
                            .$widget->browse_by_events()
                            .$widget->browse_by_venue().
                            '</div>
                        </div>
                    </div>
                    '; 
                    } else {
                        $results = '
                        <div class="container-lg py-5">
                            <div class="row gx-5 mb-5">
                                <div class="col text-center">';
                                $results .= '<p class="h4">No results for your search</p>';
                                $results .='
                                    </div>
                                    </div>
                                    <div class="row gx-5">
                                    <div class="col">'
                                    .$widget->browse_by_events();
                                    $results .='</div>
                                    <div class="col">'
                                    .$widget->browse_by_venue().
                                    '</div>
                                    <div class="col">
                                    <div class="card p-4 mb-4">
                                    <h2 class="h5 mb-4">Featured Companies</h2>';
                                    $get_users = $connect_db -> prepare('SELECT ID, company_name FROM users WHERE user_type = "company"');
                                    $get_users->execute();
                                    $get_users->store_result();
                                    $get_users->bind_result($user_id, $company_name);
                                    while ($get_users->fetch()) {
                                        $widget->set_profile($user_id);
                                        $results .='<div class="row gx-1 mb-3 pb-3 border-bottom border-light">
                                        <div class="col-auto">
                                            <div style="width: 30px;">';
                                            $results .= $widget->get_avatar().'
                                            </div>
                                        </div>
                                        <div class="col">
                                            <a class="text-dark fw-bold" href="?page=view_profile&id='.$user_id.'">'.$company_name.'</a>
                                        </div>
                                        </div>';
                                    }
                                $results .= '
                                </div>
                                </div>
                                </div>
                            </div>
                            ';                         
                    }

                    // Clear the search in $_SESSION since user is already logged in
                    if(isset($_SESSION['event_cat_key'])) {
                        $_SESSION['event_cat_key'] = '';
                    }
                    if (isset($_SESSION['event_type_key'])) {
                        $_SESSION['event_type_key'] = '';
                    }
                    if(isset($_SESSION['venue_cat_key'])){
                        $_SESSION['venue_cat_key'] = '';
                    }
                    if(isset($_SESSION['venue_type_key'])){
                        $_SESSION['venue_type_key'] = '';
                    }
                    if(isset($_SESSION['price_range_key'])) {
                        $_SESSION['price_range_key'] = '';
                    }

                } else {
                    $homepage = new Homepage();
                    $results = $homepage->get_page();
                }

            } else {
                if(!empty($_POST)) {
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $_SESSION['event_cat_key'] = is_numeric($_POST['event_category']) ? $_POST['event_category'] : null;
                        $_SESSION['event_type_key'] = is_numeric($_POST['event_type']) ? $_POST['event_type'] : null;
                        $_SESSION['venue_cat_key'] = is_numeric($_POST['venue_cat']) ? $_POST['venue_cat'] : null;
                        $_SESSION['venue_type_key'] = is_numeric($_POST['venue_type']) ? $_POST['venue_type'] : null;
                        $_SESSION['price_range_key'] = is_numeric($_POST['price_range']) ? $_POST['price_range'] : null;
                    }              
                    $ref =  "https://$_SERVER[HTTP_HOST]?page=search";
                    $results .= '
                        <div class="container-lg py-5">
                            <div class="row gx-5 mb-5">
                                <div class="col text-center">';
                        $results .= '<h2 class="h4">You need to login in order to view the results of your search.</h2>
                        <p>No account yet? Sign up!</p>';
                        $results .= '
                        <div class="d-grid gap-2 d-md-block mx-auto">
                        <a class="btn btn-dark rounded-0 btn-lg" href="?page=login&ref='.$ref.'" role="button">Login</a>
                        <a class="btn btn-primary rounded-0 btn-lg" href="?page=register&ref='.$ref.'" role="button">Register</a>
                        </div>';
                        $results .= '
                        </div>
                        </div>
                        </div>
                    </div>
                    '; 
                } else {
                    $homepage = new Homepage();
                    $results .= $homepage->get_page();
                }
                

            } 
            
            return $results;
                        
        } 
                    
    }
}