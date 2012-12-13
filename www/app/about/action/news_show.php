<?php
defined ( 'IS_ME' ) or exit ();
require_once 'model/news_class.php';
$news_object = new News ();
$page = isset ( $_GET ['page'] ) ? $_GET ['page'] : 0;
$all_news_num = $news_object->get_table_all_num ();
$star_num = System::get_page_star_num ( $page, 5, $all_news_num );
$page_html = System::get_page_html ( $page, 5, $all_news_num, $Object_url->get_url() );
$all_news = $news_object->get_all_value ( 'ctime', $star_num, 5 );
$Object_template->assign ( array ('title' => '趣友街-网站最新动态','type'=>2,'page' => $page, 'all_news' => $all_news, 'page_html' => $page_html ) );
$Object_template->display (APP . '/news_show', 0 );