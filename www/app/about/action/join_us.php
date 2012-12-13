<?php
defined ( 'IS_ME' ) or exit ();
$Object_template->assign(array('type'=>4,'join_us'=>$app_cache['join_us'],'title'=>'趣友街加入我们'));
$Object_template->display (APP . '/join_us' );