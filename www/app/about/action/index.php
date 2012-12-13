<?php
defined ( 'IS_ME' ) or exit ();
$Object_template->assign(array('type'=>0,'about_us'=>$app_cache['about_us'],'title'=>'趣友街关于我们'));
$Object_template->display ( APP . '/index' );