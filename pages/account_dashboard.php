<?php

class AccountDashboard {

    // Get Dashboard pages - Main Dashboard, Edit Profile, Post a Rental, Logout
    function get_page(){
        // First let's check if the usre is logged in
        // If not redirect to login page
        if(empty($_SESSION)) {
            redirect('?page=Login');
        }
        // User is logged in
        // In case the users wannts to login..
        // Destroy session in action=logout
        else if( isset($_GET['action']) && $_GET['action'] == 'logout' ) {
            session_destroy();
            redirect($_SERVER['PHP_SELF']);
        }
        // User is logged in
        // Show account dashboard
        else {
            $form = new Form();
            $widget = new Widget();
            $content = '
            <div class="container-lg py-5">
                <div class="row gx-5">
                    <div class="col-4">'
                        .$widget->dashboard_links()    
                        .$widget->total_sales()
                        .$widget->followers()
                        .$widget->message_box().
                    '</div>
                    <div class="col custom-padding">';
                    // action=post_listing = post equipment set for rent to database
                    if( isset($_GET['action']) && $_GET['action'] == 'post_listing' ) {

                        // Only procces the submission on POST method
                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                            // Require category arrays
                            require(ROOT_PATH.'data/categories.php');
                            // Get submitted data
                            $post_title = isset($_POST['set_name']) ? $_POST['set_name'] : null;
                            $post_content = isset($_POST['set_desc']) ? $_POST['set_desc'] : null;
                            $rent_price = isset($_POST['rent_price']) ? $_POST['rent_price'] : null;
                            $event_cat_selected = isset($_POST['event_category']) ? $_POST['event_category'] : null;
                            $event_type_local = is_numeric($_POST['event_type_Local']) ? $_POST['event_type_Local'] : null;
                            if(isset($_POST['event_type_Local'])) {
                                $event_type_local = is_numeric($_POST['event_type_Local']) ? $_POST['event_type_Local'] : null;
                            } else {
                                $event_type_local = null;
                            }  
                            if(isset($_POST['event_type_Foreign'])) {
                                $event_type_foreign = is_numeric($_POST['event_type_Foreign']) ? $_POST['event_type_Foreign'] : null;
                            } else {
                                $event_type_foreign = null;
                            }                            
                            $venue_cat_selected = isset($_POST['venue_category']) ? $_POST['venue_category'] : null;
                            if(isset($_POST['venue_type_Indoor'])){
                                $venue_type_indoor = is_numeric($_POST['venue_type_Indoor']) ? $_POST['venue_type_Indoor'] : null;
                            } else {
                                $venue_type_indoor = null;
                            }
                            if(isset($_POST['venue_type_Outdoor'])){
                                $venue_type_outdoor = is_numeric($_POST['venue_type_Outdoor']) ? $_POST['venue_type_Outdoor'] : null;
                            } else {
                                $venue_type_outdoor = null;
                            }
                            $event_cat_arr = [];
                            foreach($event_cat_selected as $event_selected) {
                                $event_cat_arr[] = $event_cat[$event_selected];
                            }
                            $event_category = count($event_cat_arr) == 1 ? $event_cat_arr[0] : implode(',',$event_cat_arr);
                            $event_type_arr = [];
                            if ($event_type_local === '0') {
                                array_push($event_type_arr, 'Local');
                            }
                            if($event_type_foreign === '1') {
                                array_push($event_type_arr, 'Foreign');
                            }
                            $event_type = count($event_type_arr) == 1 ? $event_type_arr[0] : implode(',',$event_type_arr);
                            $venue_cat_arr = [];
                            foreach($venue_cat_selected as $venue_selected) {
                                $venue_cat_arr[] = $venue_cat[$venue_selected];
                            }
                            $venue_category = count($venue_cat_arr) == 1 ? $venue_cat_arr[0] : implode(',',$venue_cat_arr);
                            $venue_type_arr = [];
                            if ($venue_type_indoor === '0') {
                                array_push($venue_type_arr, 'Indoor');
                            }
                            
                            if($venue_type_outdoor === '1') {
                                array_push($venue_type_arr, 'Outdoor');
                            }
                            $venue_type = count($venue_type_arr) == 1 ? $venue_type_arr[0] : implode(',',$venue_type_arr);
                            $user_id = $_SESSION['user_id'];
                            $post_date = new DateTime();
                            $post_date =  $post_date->format('Y-m-d H:i:s');
                            $set_photo = isset($_FILES["set_photo"]) ? $_FILES["set_photo"]["name"] : null;                                                
                            
                            // Let's upload the image file first
                            $target_dir = ROOT_PATH."uploads/";
                            $target_file = $target_dir . basename($_FILES["set_photo"]["name"]);
                            $uploadOk = 1;
                            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

                            // Check if image file is a actual image or fake image
                            if(isset($_POST["set_photo"])) {
                                $check = getimagesize($_FILES["set_photo"]["tmp_name"]);
                                if($check !== false) {
                                    $uploadOk = 1;
                                } else {
                                    $uploadOk = 0;
                                }
                            }

                            // Check if file already exists
                            if (file_exists($target_file)) {
                                $error = "Sorry, file already exists.";
                                $uploadOk = 0;
                            }

                            // Check file size
                            if ($_FILES["set_photo"]["size"] > 500000) {
                                $error = "Sorry, your file is too large.";
                                $uploadOk = 0;
                            }

                            // Allow certain file formats
                            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                            && $imageFileType != "gif" ) {
                                $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                                $uploadOk = 0;
                            }

                            // Check if $uploadOk is set to 0 by an error
                            if ($uploadOk == 0) {
                                $content .= '<div class="alert alert-danger" role="alert">'.$error.'</div>';
                            // if everything is ok, try to upload file
                            } else {
                                if (move_uploaded_file($_FILES["set_photo"]["tmp_name"], $target_file)) {
                                    // file uploaded
                                    //$content .= '<div class="alert alert-success" role="alert">File Uploaded</div>';
                                } else {
                                    $error = 'Sorry, there was an error uploading your file.';
                                    $content .= '<div class="alert alert-danger" role="alert">'.$error.'</div>';
                                }
                            }

                            // Now Let's post the data to the database!
                            // Connect to mySQL DB
                            $connect_db = connect_db();
                            // Check connection
                            if ($connect_db->connect_error) {
                                die("Connection failed: " . $connect_db->connect_error);
                            }

                            // Insert data to table posts
                            $user_data = "INSERT INTO posts (
                                user,
                                title,
                                content,
                                post_date,
                                modified_date,
                                rent_price,
                                event_cat,
                                event_type,
                                venue_cat,
                                venue_type,
                                set_photo
                            )
                            VALUES (
                                '$user_id',
                                '$post_title',
                                '$post_content',
                                '$post_date',
                                '$post_date',
                                '$rent_price',
                                '$event_category',
                                '$event_type',
                                '$venue_category',
                                '$venue_type',
                                '$set_photo'
                            )";

                            // If successful, show confirmation text and link
                            if ($connect_db->query($user_data) === TRUE) {
                                $content .= '<div class="alert alert-success" role="alert">Post submitted! <a href="?page=search&view_listing='.$connect_db->insert_id.'">View your listing</a>.</div>';
                            } else {
                                $error = 'Sorry, your post was not submitted. You may try again after a short while.';
                                $content .= '<div class="alert alert-danger" role="alert">'.$error.'</div>';
                            }

                            // Close connection to db
                            $connect_db->close();
                        }
                        $content .= '
                        <div class="card p-5 mb-5">
                            <h3 class="mb-5">Post a Listing</h3>'
                            .$form->post_form('post').'
                        </div>   
                        ';
                    } 
                    // Edit listing
                    else if( isset($_GET['action']) && $_GET['action'] == 'edit_listing' ) {                
                        if( !isset($_GET['id'])){
                            redirect('?page=account_dashboard');
                        } else { 
                            // Connect to mysql DB
                            $connect_db = connect_db();
                            // Check connection
                            if ($connect_db->connect_error) {
                                die("Connection failed: " . $connect_db->connect_error);
                            }

                            // Get post data from database
                            $get_posts = $connect_db->prepare("SELECT user FROM posts WHERE ID = ?");
                            $get_posts->bind_param('i', $_GET['id']);
                            $get_posts->execute();
                            $get_posts->store_result();
                            $get_posts->bind_result($userid);
                            $get_posts->fetch();
                            if( $userid != $_SESSION['user_id']){
                                redirect('?page=account_dashboard');
                            }
                            // Only procces the submission on POST method
                            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                // Require category arrays
                                require(ROOT_PATH.'data/categories.php');
                                // Get submitted data
                                $post_title = isset($_POST['set_name']) ? $_POST['set_name'] : null;
                                $post_content = isset($_POST['set_desc']) ? $_POST['set_desc'] : null;
                                $rent_price = isset($_POST['rent_price']) ? $_POST['rent_price'] : null;
                                $event_cat_selected = isset($_POST['event_category']) ? $_POST['event_category'] : null;
                                $event_type_local = is_numeric($_POST['event_type_Local']) ? $_POST['event_type_Local'] : null;
                                if(isset($_POST['event_type_Local'])) {
                                    $event_type_local = is_numeric($_POST['event_type_Local']) ? $_POST['event_type_Local'] : null;
                                } else {
                                    $event_type_local = null;
                                }  
                                if(isset($_POST['event_type_Foreign'])) {
                                    $event_type_foreign = is_numeric($_POST['event_type_Foreign']) ? $_POST['event_type_Foreign'] : null;
                                } else {
                                    $event_type_foreign = null;
                                }                            
                                $venue_cat_selected = isset($_POST['venue_category']) ? $_POST['venue_category'] : null;
                                if(isset($_POST['venue_type_Indoor'])){
                                    $venue_type_indoor = is_numeric($_POST['venue_type_Indoor']) ? $_POST['venue_type_Indoor'] : null;
                                } else {
                                    $venue_type_indoor = null;
                                }
                                if(isset($_POST['venue_type_Outdoor'])){
                                    $venue_type_outdoor = is_numeric($_POST['venue_type_Outdoor']) ? $_POST['venue_type_Outdoor'] : null;
                                } else {
                                    $venue_type_outdoor = null;
                                }
                                $event_cat_arr = [];
                                foreach($event_cat_selected as $event_selected) {
                                    $event_cat_arr[] = $event_cat[$event_selected];
                                }
                                $event_category = count($event_cat_arr) == 1 ? $event_cat_arr[0] : implode(',',$event_cat_arr);
                                $event_type_arr = [];
                                if ($event_type_local === '0') {
                                    array_push($event_type_arr, 'Local');
                                }
                                if($event_type_foreign === '1') {
                                    array_push($event_type_arr, 'Foreign');
                                }
                                $event_type = count($event_type_arr) == 1 ? $event_type_arr[0] : implode(',',$event_type_arr);
                                $venue_cat_arr = [];
                                foreach($venue_cat_selected as $venue_selected) {
                                    $venue_cat_arr[] = $venue_cat[$venue_selected];
                                }
                                $venue_category = count($venue_cat_arr) == 1 ? $venue_cat_arr[0] : implode(',',$venue_cat_arr);
                                $venue_type_arr = [];
                                if ($venue_type_indoor === '0') {
                                    array_push($venue_type_arr, 'Indoor');
                                }
                                
                                if($venue_type_outdoor === '1') {
                                    array_push($venue_type_arr, 'Outdoor');
                                }
                                $venue_type = count($venue_type_arr) == 1 ? $venue_type_arr[0] : implode(',',$venue_type_arr);
                                $user_id = $_SESSION['user_id'];
                                $set_photo = isset($_FILES["set_photo"]) ? $_FILES["set_photo"]["name"] : null; 
                                
                                
                                // Let's upload the image file first
                                $target_dir = ROOT_PATH."uploads/";
                                $target_file = $target_dir . basename($_FILES["set_photo"]["name"]);
                                $uploadOk = 1;
                                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

                                // Upload image if ther's any
                                if(isset($_POST["set_photo"])) {
                                    // Check if image file is a actual image or fake image
                                    $check = getimagesize($_FILES["set_photo"]["tmp_name"]);
                                    if($check !== false) {
                                        $uploadOk = 1;
                                    } else {
                                        $uploadOk = 0;
                                    }

                                    // Check if file already exists
                                    if (file_exists($target_file)) {
                                        $error = "Sorry, file already exists.";
                                        $uploadOk = 0;
                                    }

                                    // Check file size
                                    if ($_FILES["set_photo"]["size"] > 500000) {
                                        $error = "Sorry, your file is too large.";
                                        $uploadOk = 0;
                                    }

                                    // Allow certain file formats
                                    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                                    && $imageFileType != "gif" ) {
                                        $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                                        $uploadOk = 0;
                                    }

                                    // Check if $uploadOk is set to 0 by an error
                                    if ($uploadOk == 0) {
                                        $content .= '<div class="alert alert-danger" role="alert">'.$error.'</div>';
                                    // if everything is ok, try to upload file
                                    } else {
                                        if (move_uploaded_file($_FILES["set_photo"]["tmp_name"], $target_file)) {
                                            // file uploaded
                                            //$content .= '<div class="alert alert-success" role="alert">File Uploaded</div>';
                                        } else {
                                            $error = 'Sorry, there was an error uploading your file.';
                                            $content .= '<div class="alert alert-danger" role="alert">'.$error.'</div>';
                                        }
                                    }
                                }
                                
                                $modified_date = new DateTime();
                                $modified_date = $modified_date->format('Y-m-d H:i:s');
                                $listing_id = $_GET['id'];
                                if($set_photo) {
                                    $listing_update = "UPDATE posts SET
                                    title ='$post_title',
                                    content ='$post_content',
                                    rent_price ='$rent_price',
                                    modified_date = '$modified_date',
                                    event_cat = '$event_category',
                                    event_type = '$event_type',
                                    venue_cat = '$venue_category',
                                    venue_type = '$venue_type',
                                    set_photo = '$set_photo'
                                    WHERE id='$listing_id'"; 
                                } else {
                                    $listing_update = "UPDATE posts SET
                                    title ='$post_title',
                                    content ='$post_content',
                                    rent_price ='$rent_price',
                                    modified_date = '$modified_date',
                                    event_cat = '$event_category',
                                    event_type = '$event_type',
                                    venue_cat = '$venue_category',
                                    venue_type = '$venue_type'
                                    WHERE id='$listing_id'"; 
                                }                                                            
                                // If successful, show confirmation text and link
                                if ($connect_db->query($listing_update) === TRUE) {
                                    $content .= '<div class="alert alert-success" role="alert">Post updated! <a href="?page=search&view_listing='.$listing_id.'">View your listing</a>.</div>';
                                } else {
                                    $error = 'Sorry, your post was not updated. You may try again after a short while.';
                                    $content .= '<div class="alert alert-danger" role="alert">'.$error.'</div>';
                                }
                                
                            }
                            $content .= '
                            <div class="card p-5 mb-5">
                                <h3 class="mb-5">Edit a Listing</h3>'                                
                                .$form->post_form('update',$_GET['id']).'
                            </div> 
                            ';
                            // Close connection to db
                            $connect_db->close();
                        }
                    }
                    // My Listings
                    else {
                        // User id
                        $user_id = $_SESSION['user_id'];
                        $widget->set_user($user_id); 
                        $content .= $widget->get_listings();                                                          
                    }
                    $content .= '                        
                    </div>
                </div>
            </div>                
            ';
            return $content;
        }
    }

}