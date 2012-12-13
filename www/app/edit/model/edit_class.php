<?php

define ( 'EDIT_USER_UPLOAD_NO', 0 );

define ( 'EDIT_USER_UPLOAD_IMAGE', 1 );

define ( 'EDIT_USER_UPLOAD_FILE', 2 );

define ( 'EDIT_USER_UPLOAD_ALL', 3 );

class Edit {
	
	public function __construct(){
	
	}
	
	static function create_eedit($html_name, $type = EDIT_USER_UPLOAD_ALL) {
		
		$edit_str = '<link rel="stylesheet" href="/css/edit/eedit/redactor.css" /><script src="/js/edit/eedit/redactor.js"></script><script src="/js/edit/eedit/zh_cn.js"></script><script type="text/javascript">$(document).ready(function(){$("#' . $html_name . '").redactor({';
		switch ($type) {
			case 0 :
				$edit_str .= '';
				break;
			case 1 :
				$edit_str .= '';
				break;
			case 2 :
				$edit_str .= '';
				break;
			case 3 :
				$edit_str .= 'imageUpload: "' . SITE_URL . '/edit/image_upload.html",fileUpload: "' . SITE_URL . '/edit/file_upload.html",imageGetJson: "' . SITE_URL . '/edit/show_upload_image.html",lang: "zh_cn",css: "style.css",autoresize: true,fixed: true';
				break;
		
		}
		$edit_str .= '});});</script>';
		return $edit_str;
	}

}