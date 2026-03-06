<?php
class MenuController {
    public static function index(): void {
        $categories  = MenuModel::getAllCategories();
        $menu_items  = MenuModel::getAvailableItems();
        $page_title  = 'Our Menu';
        $active_page = 'menu';

        require 'view_layout_header.php';
        require 'view_menu.php';
        require 'view_layout_footer.php';
    }
}
