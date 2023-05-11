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
            return $account_dashboard->get_page();
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
        }
        
    }

}