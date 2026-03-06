<?php
class HomeController {
    public static function index(): void {
        $featured_items = MenuModel::getFeaturedItems(6);
        $page_title     = 'Home';
        $active_page    = 'home';

        require 'view_layout_header.php';
        require 'view_home.php';
        require 'view_layout_footer.php';
    }
}
