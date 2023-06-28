<?php

class Content extends Template {

    function get_template() {
        $page = $this->page;

        if($page=='home') {
            $homepage = new Homepage();
            return $homepage->get_page();
        }

        // Register Page
        else if($page=='register') {
            $register_page = new RegisterPage();
            return $register_page->get_page();
        }

        // Login Page
        else if($page=='login') {
            $login_page = new LoginPage();
            return $login_page->get_page();
        }

        // Account Dashboard
        else if($page=='account_dashboard') {
            $account_dashboard = new AccountDashboard();
            return $account_dashboard->get_page($page);
        }

        // View Search
        else if($page=='search') {
            $search = new Search();

            if(isset($_GET['view_listing'])) {
                $subpage = 'view_listing';
            } else if(isset($_GET['view_category'])) {
                $subpage = 'view_category';
            } else if(isset($_GET['view_tag'])) {
                $subpage = 'view_tag';
            } else {
                $subpage = 'search_main';
            }
            return $search->get_page($subpage);
        }

        // View Profile
        else if($page=='view_profile') {
            $user_id = isset($_GET['id']) ? $_GET['id'] : null;
            $profile = new ViewProfile();
            $profile->set_user($user_id);
            return $profile->get_profile();
        } else {
            return '
            <div class="row p-0 m-0 position-relative bg-image" style="background-image:url('.SITE_URL.'/assets/images/bg_search.jpg); height: 100px; background-position: 50% 50%;">
                <div class="col-auto mx-auto position-absolute top-100 start-50 translate-middle">
                
                </div>
            </div>
            <div class="row p-0 mx-0 mt-5">
                <div class="col">
                    <p class="display-6 text-center mt-0 pt-0">
                    
                    </p>
                </div>
            </div>
            <div class="container-lg py-5">
                <div class="row gx-5 py-5">
                    <div class="col text-center py-5">
                        <h2 class="h4">404 - Page Not Found</h2>
                        <p>Whoops! Sorry, but this page doesn\'t exist.</p>
                        <p>Why not use the links above or <a href="?page=search">search</a> to find what you\'re looking for?
                            Alternatively, you could go back to <a href="javascript:window.history.back();">where you were</a>
                            or start again from the <a href="/">home page</a>.</p>
                    </div>
                </div>
            </div>
        </div>
        '; 
        }
        
    }

}