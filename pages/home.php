<?php

class Homepage {
    
    function get_page(){
        // Require category arrays
        require(ROOT_PATH.'data/categories.php');

        $search  = array('(', ')', ' / ', ' - ',' ');
        $replace = array('', '', '_', '_', '_');

        // Connect to mysql DB
        $connect_db = connect_db();
        // Check connection
        if ($connect_db->connect_error) {
            die("Connection failed: " . $connect_db->connect_error);
        }

        $get_users = $connect_db -> prepare('SELECT ID, company_name FROM users');
        $get_users->execute();
        $get_users->store_result();
        $get_users->bind_result($user_id, $company_name);

        $widget = new Widget();        
    

        $content = '

        <div class="container-lg py-5">
            <div class="row gx-5">
                <div class="col">
                    <h2 class="h4 mb-4">Featured Sets</h2>
                </div>
            </div>
            <div class="row gx-3 mb-5">
                <div class="col-3">
                    <a href="?page=search&view_category=birthday_party" class="overflow-hidden position-relative text-center d-block ratio ratio-1x1 bg-image home-cat-link" style="background-image:url('.SITE_URL.'/assets/images/image_cat_birthday.jpg)">
                    <span class="link-title py-2 px-4 fw-bold rounded bg-white position-absolute top-50 start-50 translate-middle">Birthday Party</a>
                    </a>
                </div>
                <div class="col-3">
                    <a href="?page=search&view_category=graduation_recognition_day" class="overflow-hidden position-relative text-center d-block ratio ratio-1x1 bg-image home-cat-link" style="background-image:url('.SITE_URL.'/assets/images/image_cat_graduation.jpg)">
                    <span class="link-title py-2 px-4 fw-bold rounded bg-white position-absolute top-50 start-50 translate-middle">Graduation</a>
                    </a>
                </div>
                <div class="col-3">
                    <a href="?page=search&view_category=wedding" class="overflow-hidden position-relative text-center d-block ratio ratio-1x1 bg-image home-cat-link" style="background-image:url('.SITE_URL.'/assets/images/image_cat_wedding.jpg)">
                    <span class="link-title py-2 px-4 fw-bold rounded bg-white position-absolute top-50 start-50 translate-middle">Wedding</a>
                    </a>
                </div>
                <div class="col-3">
                    <a href="?page=search&view_category=concert_foreign" class="overflow-hidden position-relative text-center d-block ratio ratio-1x1 bg-image home-cat-link" style="background-image:url('.SITE_URL.'/assets/images/image_cat_concert.jpg)">
                    <span class="link-title py-2 px-4 fw-bold rounded bg-white position-absolute top-50 start-50 translate-middle">Concert</a>
                    </a>
                </div>
            </div>
            <div class="row gx-5">
                <div class="col-4">
                    <h2 class="h5 mb-4">Browse by Events</h2>
                    <div class="mb-3">';

                    foreach($event_types as $event_type){
                        $slug = strtolower(str_replace($search, $replace, $event_type));
                        $content .='<a href="?page=search&view_tag='.$slug.'" class="cat-link border rounded-0 py-1 px-2 d-inline-block me-1 mb-2 border-2 border-light text-dark fw-bold">'.$event_type.'</a> ';
                    }

                    $content .='
                    </div>
                    
                    <div>
                    ';

                    foreach($event_cat as $event){
                        $slug = strtolower(str_replace($search, $replace, $event));
                        $content .='<a href="?page=search&view_category='.$slug.'" class="cat-link border rounded-0 py-1 px-2 d-inline-block me-1 mb-2 border-2 border-light text-dark fw-bold">'.$event.'</a> ';
                    }
                    

                $content .='
                    </div>
                </div>
                <div class="col">
                    <h2 class="h5 mb-4">Browse by Venue</h2>
                    <div class="mb-3">
                    ';

                    foreach($venue_type as $venue_type){
                        $slug = strtolower(str_replace($search, $replace, $venue_type));
                        $content .='<a href="?page=search&view_tag='.$slug.'" class="cat-link border rounded-0 py-1 px-2 d-inline-block me-1 mb-2 border-2 border-light text-dark fw-bold">'.$venue_type.'</a> ';
                    }

                    $content .='
                    </div>
                    
                    <div>
                    ';

                    foreach($venue_cat as $venue){
                        $slug = strtolower(str_replace($search, $replace, $venue));
                        $content .='<a href="?page=search&view_category='.$slug.'" class="cat-link border rounded-0 py-1 px-2 d-inline-block me-1 mb-2 border-2 border-light text-dark fw-bold">'.$venue.'</a> ';
                    }

                $content .= '
                    </div>
                </div>
                <div class="col-3">
                    <h2 class="h5 mb-4">Featured Companies</h2>';
                    while ($get_users->fetch()) {
                        $widget->set_user($user_id);
                        $content .='<div class="row gx-1 mb-3 pb-3 border-bottom border-light">
                        <div class="col-auto">
                            <div style="width: 30px;">';
                            $content .= $widget->get_avatar().'
                            </div>
                        </div>
                        <div class="col">
                            <a class="text-dark fw-bold" href="?page=view_profile&id='.$user_id.'">'.$company_name.'</a>
                        </div>
                        </div>';
                    }
                $content .= '
                </div>
            </div>
        </div>
    ';

    // Close connection to db
    $connect_db->close();

    return $content;
    }
}