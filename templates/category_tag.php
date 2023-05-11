<?php
class CategoryTag {  
      
    public $tag;
    public $type;

    function set_tag($tag){
        $this->tag = $tag;
    }

    function set_type($type){
        $this->type = $type;
    }

    function view_tag($event_cat,$venue_cat, $event_tags, $venue_tags) {
        $tag = $this->tag;
        $type = $this->type;
        $cat_tags = ['foreign','local','indoor','outdoor'];

        // Connect to mysql DB
        $connect_db = connect_db();
        // Check connection
        if ($connect_db->connect_error) {
            die("Connection failed: " . $connect_db->connect_error);
        }

        // Get associated categories from specified tag or category
        $selected_tag = null;
        if($type == 'tag') {
            $event_key = null;
            foreach($event_tags as $key => $event_tags) {
                foreach($event_tags as $event_tag) {
                    if($tag == $event_tag) {
                        $event_key = $key;
                        break;
                    }
                }
            }
            
            $venue_key = null;
            foreach($venue_tags as $key => $venue_tags) {
                foreach($venue_tags as $venue_tag) {
                    if($tag == $venue_tag) {
                        $venue_key = $key;
                        break;
                    }
                }
            }

            if($event_key) {
                $selected_tag = $event_cat[$event_key];
            } else if($venue_key) {
                $selected_tag = $venue_cat[$venue_key];
            } else if(in_array($tag, $cat_tags)) {
                $selected_tag = ucwords($tag);              
            }
        }   
        
        else if($type == 'category') {
            $search  = array('(', ')', ' / ', ' - ',' ', '&');
            $replace = array('', '', '_', '_', '_','');
            foreach($event_cat as $event) {
                $slug = strtolower(str_replace($search, $replace, $event));
                if($slug == $tag) {
                    $selected_tag = $event;
                    break;
                }
            }
            foreach($venue_cat as $venue) {
                $slug = strtolower(str_replace($search, $replace, $venue));
                if($slug == $tag) {
                    $selected_tag = $venue;
                    break;
                }
            }
        }

        if($selected_tag) {
            // Get post data from database and look for posts assigned in requested category
            if($type == 'tag') {
                $get_posts = $connect_db->prepare("SELECT 
                ID, user, title, content, post_date, modified_date, rent_price, event_cat, event_type, venue_cat, venue_type, set_photo
                FROM posts
                WHERE
                event_cat LIKE '%$selected_tag%'
                OR
                event_type LIKE '%$selected_tag%'
                OR
                venue_cat LIKE '%$selected_tag%'
                OR
                venue_type LIKE '%$selected_tag%'
                ");
            }

            else if($type == 'category') { 
                $get_posts = $connect_db->prepare("SELECT 
                ID, user, title, content, post_date, modified_date, rent_price, event_cat, event_type, venue_cat, venue_type, set_photo
                FROM posts
                WHERE
                event_cat LIKE '%$selected_tag%'
                OR
                venue_cat LIKE '%$selected_tag%'
                ");
            }            
            $get_posts->execute();
            //$get_posts->store_result();
            //$get_posts->bind_result($postid, $userid, $title, $contenttxt, $postdate, $modifieddate, $rentprice, $eventcat, $eventtype, $venuecat, $venuetype, $setphoto);
            $result = $get_posts->get_result();            
            $results = $type == 'tag' ? '<h2 class="h4 mb-5">Search results for: #'.$tag.'</h2>' : '<h2 class="h4 mb-5">Search results for: '.$selected_tag.'</h2>';
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
        } else {
            $results = '<p class="h4">No results for the tag: '.$tag.'</p>';
        }

        // Close connection to db
        $connect_db->close();
        
        return $results;
    }

    function get_tags(
        $selected_event,
        $selected_venue,
        $event_tags,
        $venue_tags,
        $search,
        $replace,
        $event_cat,
        $foreign_events,
        $local_events,
        $venue_cat,
        $indoor_venue,
        $outdoor_venue
    ) {         
        
        foreach($event_cat as $key => $event_c) {
            $search  = array('(', ')', ' / ', ' - ',' ');
            $replace = array('', '', '_', '_', '_');
            $event_type_slug = strtolower(str_replace($search, $replace, $event_c));
            array_push($event_tags[$key], $event_type_slug);
            if(in_array($event_c, $foreign_events)) {
                array_push($event_tags[$key], 'foreign');
            }
            if(in_array($event_c, $local_events)) {
                array_push($event_tags[$key], 'local');
            }
        }                
        
        foreach($venue_cat as $key => $venue_c) {            
            $venue_type_slug = strtolower(str_replace($search, $replace, $venue_c));
            array_push($venue_tags[$key], $venue_type_slug);
            if(in_array($venue_c, $indoor_venue)) {
                array_push($venue_tags[$key], 'indoor');
            }
            if(in_array($venue_c, $outdoor_venue)) {
                array_push($venue_tags[$key], 'outdoor');
            }
        }

        $post_tags = [];
        foreach($selected_event as $ec){                                    
            $event_key = array_search($ec,$event_cat);                                
            foreach($event_tags[$event_key] as $key => $event_tag) {
                if($key != 1) {
                    if(!in_array($event_tag, $post_tags)) $post_tags[] = $event_tag;
                }                                 
            }
        }
        foreach($selected_venue as $vc){                                    
            $venue_key = array_search($vc,$venue_cat);                                
            foreach($venue_tags[$venue_key] as $key => $venue_tag) {
                if($key != 1) {
                    if(!in_array($venue_tag, $post_tags)) $post_tags[] = $venue_tag; 
                }                                  
            }
        }
        $tags = '';
        foreach($post_tags as $tag) {
            $tags .= '<a href="?page=search&view_tag='.$tag.'">#'.$tag.'</a> ';  
        }

        return $tags;
    }

    function get_events($selected_events, $search, $replace) {

        $event_category_links = '';
        $event_count = 1;
        foreach($selected_events as $event_category) {                                    
            $event_type_slug = strtolower(str_replace($search, $replace, $event_category));
            $event_category_links .= '<a href="?page=search&view_category='.$event_type_slug.'">'.$event_category.'</a>';
            if($event_count != count($selected_events))  $event_category_links .= ', ';
            $event_count++;
        }
        return  $event_category_links;
    }

    function get_venues($selected_venues, $search, $replace) {

        $venue_category_links = '';
        $venue_count = 1;
        foreach($selected_venues as $venue_category) {
            $venue_type_slug = strtolower(str_replace($search, $replace, $venue_category));
            $venue_category_links .= '<a href="?page=search&view_category='.$venue_type_slug.'">'.$venue_category.'</a>';
            if($venue_count != count($selected_venues))  $venue_category_links .= ', ';
            $venue_count++;
        }
        return $venue_category_links;
    }
    
}