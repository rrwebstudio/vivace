<?php

// Connect to DB
require(ROOT_PATH.'db.php');

class ViewProfile {

    public $user_id;

    function set_user($user_id) {
        $this->user_id = $user_id;
    }

    function get_profile() {
        global $connect_db, $widget, $current_user;
        $profile_id = $this->user_id;
        $user = $widget->get_user();
        $widget->set_profile($profile_id);

        $profile ='';

        if(empty($_SESSION['user_id'])) {
            $profile .= '
            <div class="row p-0 m-0 position-relative bg-image" style="background-image:url('.SITE_URL.'/assets/images/bg_search.jpg); height: 100px; background-position: 50% 50%;">
                <div class="col-auto mx-auto position-absolute top-100 start-50 translate-middle">
                
                </div>
            </div>
            <div class="row p-0 mx-0 mt-5">
                <div class="col">
                    <p class="display-6 text-center mt-0 pt-0">
                    
                    </p>
                </div>
            </div>';
            $profile .= '
            <div class="container-lg py-5">
                <div class="row gx-5 mb-5 py-5">
                    <div class="col text-center py-5">';
                        $profile  .= '<h2 class="h4">You need to login in order to view profiles.</h2>
                        <p>No account yet? Sign up!</p>';
                        $profile  .= '
                        <div class="d-grid gap-2 d-md-block mx-auto">
                            <a class="btn btn-dark rounded-0 btn-lg" href="?page=login" role="button">Login</a>
                            <a class="btn btn-primary rounded-0 btn-lg" href="?page=register" role="button">Register</a>
                        </div>';
                        $profile  .= '
                        </div>
                    </div>
                </div>
            </div>
            '; 
        } else {

            // Prepare sql statement to get profile data
            $check_user = $connect_db->prepare("SELECT email, company_name, company_address, first_name, last_name, phone_number, mobile_number, user_type FROM users WHERE ID = ?");
            $check_user -> bind_param('s', $profile_id);
            $check_user->execute();
            $check_user -> store_result();
            $check_user -> bind_result($email, $company_name, $company_address, $first_name, $last_name, $phone_number, $mobile_number, $user_type);    

            // statement result
            if($check_user->num_rows == 1) {

                // Get result
            if($check_user->fetch()) {
                $is_following = false;          
                $check_follow = $connect_db->prepare("SELECT company_id FROM follows WHERE client_id = ? AND company_id = ?");
                $check_follow -> bind_param('ii', $current_user, $profile_id);
                $check_follow->execute();
                $check_follow -> store_result();
                $check_follow -> bind_result($company_id);
                $check_follow->fetch();

                if($check_follow->num_rows == 1) {
                    $is_following = true;
                }

                if(isset($_GET['action']) && $_GET['action'] == 'follow') {     
                    if($check_follow->num_rows == 0) {
                        $follow_account = "INSERT INTO follows (
                            client_id,
                            company_id
                        )
                        VALUES (
                            '$current_user',
                            '$profile_id'
                        )";

                        if($connect_db->query($follow_account) === TRUE){
                            $is_following = true;
                        }
                    }                            
                }

                if(isset($_GET['action']) && $_GET['action'] == 'unfollow') {     
                    if($check_follow->num_rows == 1) {
                        $unfollow = $connect_db->prepare("DELETE FROM follows WHERE client_id = ? AND company_id = ?");
                        $unfollow->bind_param('ii', $current_user, $profile_id);
                        $unfollow->execute();
                        $is_following = false;
                        if(isset($_GET['ref']) && $_GET['ref'] == 'view_profile'){
                            redirect('?page=view_profile&id='.$current_user);
                        } else if(isset($_GET['ref']) && $_GET['ref'] == 'account_dashboard'){
                            redirect('?page=account_dashboard&view=following');
                        }
                    }
                }                
                $profile .= '
                <div class="row p-0 m-0 position-relative bg-image" style="background-image:url('.SITE_URL.'/assets/images/bg_search.jpg); height: 100px; background-position: 50% 50%;">
                    <div class="col-auto mx-auto position-absolute top-100 start-50 translate-middle">
                    '.$widget->get_avatar().'
                    </div>
                </div>
                <div class="row p-0 mx-0 mt-5">
                    <div class="col">
                        <p class="display-6 text-center mt-5 pt-5">
                        '.$company_name.'
                        </p>
                    </div>
                </div>';

                if($user_type == 'company') {
                    $profile .= '
                    <div class="row p-0 m-0">
                        <div class="col text-center">
                            <p class="fw-bold">
                            '.$widget->get_follower_count().' followers
                            </p>
                        </div>
                    </div>';
                }

                $profile .= '
                <div class="row p-0 m-0">
                    <div class="col-auto mx-auto">';
                        if($profile_id != $current_user) {
                            if($is_following == true) {
                                $profile .='<a class="following btn btn-light rounded-0 btn bi mx-1 border-3 border-light fw-bold" href="?page=view_profile&id='.$profile_id.'&action=unfollow"role="button">Following</a>';
                            } else {
                                $profile .='<a class="btn btn-outline-dark rounded-0 btn bi mx-1 border-3 border-light fw-bold" href="?page=view_profile&id='.$profile_id.'&action=follow" role="button">Follow</a>';
                            }
                            $profile .= '<a class="btn btn-outline-dark rounded-0 btn bi mx-1 border-3 border-light fw-bold" href="?page=account_dashboard&action=create_message&recipient_id='.$profile_id.'" role="button">Message</a>';   
                        }                                                                                           
                        if($profile_id == $current_user) {
                            $profile .='<a class="btn btn-outline-dark rounded-0 btn bi mx-1 border-3 border-light fw-bold" href="?page=account_dashboard" role="button">Account Dashboard</a>';
                            $profile .='<a class="btn btn-outline-dark rounded-0 btn bi mx-1 border-3 border-light fw-bold" href="?page=account_dashboard&action=edit_profile" role="button">Edit Profile</a>';                            
                        }
                    $profile .='
                    </div>
                </div>';  
                
                if($user_type == 'client') {
                    $profile .='
                    <div class="container py-5">
                        <div class="row gx-5">
                            <div class="col-3">
                            '.$widget->profile_box().'
                            </div>
                            <div class="col">                            
                            '.$widget->profile_user_data().'
                            </div>
                            <div class="col">                            
                            '.$widget->get_follow_list().'
                            </div>
                        </div>
                    </div>
                    ';
                } else {
                    $profile .='
                    <div class="container py-5">
                        <div class="row gx-5">
                            <div class="col">
                            <h3 class="mb-3">Listings by '.$company_name.'</h3>';

                            if(($user['user_type'] == 'company') && ($current_user != $profile_id)){
                                $profile .='<p>You are not allowed to view the listings by '.$company_name.'</p>';
                            } else if($is_following == false && ($current_user != $profile_id)){
                                $profile .='<p>You must follow '.$company_name.' in order to view the listings by '.$company_name.'</p>';
                            } else {
                                $profile .= $widget->get_listings();
                            }
                            $profile .='
                            </div>
                            <div class="col-3">
                            '.$widget->profile_box().'
                            '.$widget->profile_user_data().'
                            </div>
                        </div>
                    </div>
                    ';
                }                
            }

            }
        }

        return $profile;

    }
}