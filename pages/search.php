<?php

class Search {

    public $subpage;

    function get_page($subpage) {

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
            
            // Call widget class .. for displaying some widgets
            $widget = new Widget();
            return '
            <div class="container-lg py-5">
                <div class="row gx-5">
                    <div class="col">'
                    .$tag->view_tag($event_cat, $venue_cat, $event_tags, $venue_tags).
                    '</div>
                    <div class="col-3">'
                    .$widget->welcome_box().
                    '</div>
                </div>
            </div>
            '; 
        } else {
            if(!empty($_SESSION)) {
                // Call widget class .. for displaying some widgets later
                $widget = new Widget();

                // Require category arrays
                require(ROOT_PATH.'data/categories.php');

                // Get data from submitted search
                // $_POST data are keys to the category arrays
                $event_cat_key = is_numeric($_POST['event_category']) ? $_POST['event_category'] : null;
                $event_type_key = is_numeric($_POST['event_type']) ? $_POST['event_type'] : null;
                $venue_cat_key = is_numeric($_POST['venue_cat']) ? $_POST['venue_cat'] : null;
                $venue_type_key = is_numeric($_POST['venue_type']) ? $_POST['venue_type'] : null;
                $price_range_key = is_numeric($_POST['price_range']) ? $_POST['price_range'] : null;

                // From the keys above, we get the selected categories and types
                $selected_event_cat = $event_cat[$event_cat_key]; // eg birthday party
                $selected_event_type = $event_types[$event_type_key]; // local or foreign
                $selected_venue_cat = $venue_cat[$venue_cat_key]; // eg garden venue
                $selected_venue_type = $venue_type[$venue_type_key]; // indoor or outdoor      

                // Connect to mysql DB
                $connect_db = connect_db();
                // Check connection
                if ($connect_db->connect_error) {
                    die("Connection failed: " . $connect_db->connect_error);
                }


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
                    ");
                }

                // Price range key = 1 : BETWEEN '500000' AND '999000'
                else if ($price_range_key == 1) {
                    $get_posts = $connect_db->prepare("SELECT 
                    ID, user, title, content, post_date, modified_date, rent_price, event_cat, event_type, venue_cat, venue_type, set_photo
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
                    ");
                }

                // Price range key = 2 : >= 1000000
                else if ($price_range_key == 2) {
                    $get_posts = $connect_db->prepare("SELECT 
                    ID, user, title, content, post_date, modified_date, rent_price, event_cat, event_type, venue_cat, venue_type, set_photo
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
                        $get_author = $connect_db->prepare("SELECT email, company_name FROM users WHERE ID = ?");
                        $get_author->bind_param('i', $row['user']);
                        $get_author->execute();
                        $get_author->store_result();
                        $get_author->bind_result($author_email, $author_company);
                        $get_author->fetch();
                        // Get post thumbnail
                        $bg_setphoto = SITE_URL.'/uploads/'.$row['set_photo'];
                        // Format money
                        $money_formatter = new NumberFormatter('en_GB', NumberFormatter::DECIMAL);
                        $money_formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 2);
                        // Show result
                        $results .= '
                        <div class=" post post-'.$row['ID'].' card p-0 shadow mb-4">
                            <div class="card-body p-0">
                                <div class="row">
                                    <div class="col-3">
                                        <div class="ratio ratio-1x1 bg-image rounded-start" style="background-image:url('.$bg_setphoto.')"></div>
                                    </div>
                                    <div class="col p-3">                                                      
                                        <div class="post-content position-relative" style="height: 165px; overflow: hidden;">
                                            <h2 class="card-title h4 mb-1">'.$row['title'].'</h2>'
                                            .$row['content'].
                                        '</div>
                                    </div>  
                                    <div class="col-4 py-3 pe-4">
                                    <div class="alert alert-success py-1 px-2">
                                        <a class="alert-link" href="?page=search&view_company='.$row['user'].'">'.$author_company.'</a>
                                        <div class="row">
                                            <div class="col fw-bold price-text">
                                            ₱'.$money_formatter->format($row['rent_price']).'
                                            </div>
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
                                    <a href="#" class="btn rounded-0 d-inline py-1 px-2 small text-uppercase" style="background-color: #ffc107;">Voucher Code 1</a>
                                    <a href="#" class="btn rounded-0 d-inline py-1 px-2 small text-uppercase" style="background-color: #ffc107;">Voucher Code 2</a>
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
                        $get_users = $connect_db -> prepare('SELECT ID, company_name FROM users');
                        $get_users->execute();
                        $get_users->store_result();
                        $get_users->bind_result($user_id, $company_name);
                        while ($get_users->fetch()) {
                            $widget->set_user($user_id);
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

                // Close connection to db
                $connect_db->close();
            
                }
            } else {
                $results = '
                    <div class="container-lg py-5">
                        <div class="row gx-5 mb-5">
                            <div class="col text-center">';
                    $results .= '<h2 class="h4">You need to logn in order to view the results of your search.</h2>
                    <p>No account yet? Sign up!</p>';
                    $results .= '
                    <div class="d-grid gap-2 d-md-block mx-auto">
                    <button class="btn btn-primary" type="button">Button</button>
                    <button class="btn btn-primary" type="button">Button</button>
                    </div>';
                    $results .= '
                    </div>
                    </div>
                    </div>
                </div>
                '; 

            } 
            
            return $results;
                        
        } 
                    
    }
}