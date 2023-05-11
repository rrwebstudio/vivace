<?php

class Footer extends Template {

    function get_html() {
        
        # Website is running on Bootstrap
        # Bootstrap CSS and JS is being delivered via CDN

        $page = $this->page;

        $footer = '
                <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous"></script>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous"></script>
                <script src="../assets/js/custom.js?v=0.0.2"></script>';

                $footer .= '
                
                </div>';

                if(!in_array($page, array('login', 'register'))) {
                    $footer .= '
                    <div class="container-fluid bg-black text-white" id="main-footer">
                        <div class="container-lg py-5">
                            <div class="row gx-5">
                                <div class="col-4">
                                    <h4 class="h5 lh-base">Search for your multimedia equipment needs.</h4>
                                    Brief description of the site should go here. Suspendisse sollicitudin purus ac magna bibendum, ac hendrerit mauris sagittis.
                                </div>
                                <div class="col lh-base">
                                    <h4 class="h5">Vivace</h4>
                                    <ul class="list-unstyled">
                                    <li class="pb-1 mb-2 border-bottom border-dark"><a href="?page=login" class="text-white">Login</a></li>
                                    <li class="pb-1 mb-2 border-bottom border-dark"><a href="?page=register" class="text-white">Register</a></li>    
                                    <li class="pb-1 mb-2 border-bottom border-dark"><a href="#" class="text-white">About Us</a></li>
                                    <li class="pb-1 mb-2"><a href="#" class="text-white">Contact Us</a></li>
                                    </ul>
                                </div>
                                <div class="col">
                                    '.SITE_LOGO.'
                                    <p>Copyright &copy; 2023. All rights reserved.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    ';
                }
                

             $footer .= '

            </body>
        </html>
        ';
        return $footer;
    }
}