<?php
class Template {
	
	private $const_regexp = 'const\.([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)';
	
	private $var_regexp = '(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)';
	
	private $array_regexp = '(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\.[\w->\.\$_\x7f-\xff]*)';
	
	private $loop_key_value_regexp = 'loop\s+(\$[\S]*)\s+(\$[\S]*)\s+(\$[\S]*)';
	
	private $loop_value_regexp = 'loop\s+(\$[\S]*)\s+(\$[\S]*)';
	
	private $template_var_regexp = '(template\.[\w\.]*)';
	
	private $function_regexp = '([a-zA-Z0-9_\x7f-\xff\.\$\-\>\']*\|.*)';
	
	private $left_tag = "{";
	private $right_tag = "}";
	private $html_path = "";
	private $template_path = "";
	private $html_suffix = "";
	private $vars = array ();
	private $template_vars = array ();
	private $avatar_cache_array = array ();
	//缓存成html暂时不写
	

	/**
	 * 构造函数
	 * @param str $html_path  html页面路径
	 * @param str $template_path 模板路径
	 * @param str $left_tag    左标识
	 * @param str $right_tag   右标识
	 * @param str $html_suffix  前端页面后缀
	 */
	public function __construct($html_path, $template_path, $left_tag = "{", $right_tag = "}", $html_suffix = 'html') {
		file_exists ( $html_path ) || exit ( "前端页面路径不正确" );
		file_exists ( $template_path ) || exit ( "模板生成路径不正确" );
		$this->left_tag = $this->preg_tag ( $left_tag );
		$this->right_tag = $this->preg_tag ( $right_tag );
		$this->html_path = $html_path;
		$this->template_path = $template_path;
		in_array ( $html_suffix, array ('html', 'htm', 'HTML', 'HTM' ) ) || exit ( '前端页面后缀名不支持' );
		$this->html_suffix = "." . $html_suffix;
	}
	
	public function assign($data) {
		if (is_array ( $data )) {
			$this->vars = array_merge ( $this->vars, $data );
		} elseif (is_object ( $data )) {
			$this->assign ( get_object_vars ( $data ) );
		}
	}
	/**
	 * 加载模板
	 * @param str $html_name前端页面的文件名字(不加后缀)
	 */
	public function display($html_name, $memory = 1) {
		global $Object_url;
		
		($memory == 1) && setcookie ( 'last_url', $Object_url->get_url (), time () + 3600, '/', SITECOOKIEPATH );
		$html_file = $this->html_path . $html_name . $this->html_suffix;
		$template_file = $this->template_path . $html_name . '.php';
		file_exists ( $html_file ) || exit ( $html_name . "文件不存在" );
		if (file_exists ( $template_file ) && (filemtime ( $template_file ) > filemtime ( $html_file ))) {
			include $template_file;
			return;
		}
		$template_path = dirname ( $template_file );
		(is_dir ( $template_path )) ? "" : mkdir ( $template_path );
		$template_content = file_get_contents ( $html_file );
		$template_content = $this->parse ( $template_content );
		$file_hand = fopen ( $template_file, 'w+' );
		fwrite ( $file_hand, $template_content );
		fclose ( $file_hand );
		include $template_file;
	}
	
	public function get_display_content($html_name, $memory = 1) {
		$this->display ( $html_name, $memory );
		return ob_get_clean ();
	}
	
	private function preg_tag($tagstr) {
		$tagstr = str_replace ( '\\', '\\\\', $tagstr ); //过滤\
		$tagstr = preg_replace ( '|\.|', '\.', $tagstr ); //过滤.
		$tagstr = preg_replace ( '|\?|', '\?', $tagstr ); //过滤?
		$tagstr = preg_replace ( '|\*|', '\*', $tagstr ); //过滤*
		$tagstr = preg_replace ( '|\+|', '\+', $tagstr ); //过滤+
		$tagstr = preg_replace ( '|\{|', '\{', $tagstr ); //过滤{
		$tagstr = preg_replace ( '|\}|', '\}', $tagstr ); //过滤}
		$tagstr = preg_replace ( '|\$|', '\$', $tagstr ); //过滤$
		$tagstr = preg_replace ( '|\||', '\|', $tagstr ); //过滤|
		return $tagstr;
	}
	
	/**
	 * 解析模板文件内容
	 * @param str $template
	 */
	function parse($template) {
		//获取三方模板内容然后编译进模板file
		$template = preg_replace ( '/' . $this->left_tag . 'file\s+\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)' . $this->right_tag . "/iesU", "\$this->get_file('\\1')", $template );
		//调用函数
		$template = preg_replace ( '/' . $this->left_tag . $this->function_regexp . $this->right_tag . "/iseU", "\$this->get_function('\\1')", $template );
		//直接的var
		$template = preg_replace ( '/' . $this->left_tag . $this->var_regexp . $this->right_tag . '/', "<?php echo \\1; ?>", $template );
		//多维数组显示
		$template = preg_replace ( '/' . $this->left_tag . $this->array_regexp . $this->right_tag . "/ise", "\$this->replace_var_in_array('<?php echo \\1]; ?>')", $template );
		//多维数组
		$template = preg_replace ( '/' . $this->array_regexp . "/ise", "\$this->replace_var_in_array('\\1]')", $template );
		//php
		$template = preg_replace ( '/' . $this->left_tag . 'php\s+(.*?)' . $this->right_tag . "/is", '<?php \\1; ?>', $template );
		//for	
		$template = preg_replace ( '/' . $this->left_tag . 'for\s*\((.*?)\)' . $this->right_tag . "/is", '<?php for(\\1) {?>', $template );
		//结束for
		$template = preg_replace ( '/' . $this->left_tag . '\/for' . $this->right_tag . "/is", "<?php } ?>", $template );
		//if
		$template = preg_replace ( '/' . $this->left_tag . 'if\s+(.+?)' . $this->right_tag . "/is", '<?php if(\\1) { ?>', $template );
		//结束if
		$template = preg_replace ( '/' . $this->left_tag . '\/if' . $this->right_tag . "/is", "<?php } ?>", $template );
		//else
		$template = preg_replace ( '/' . $this->left_tag . 'else' . $this->right_tag . "/is", "<?php } else { ?>", $template );
		//elseif
		$template = preg_replace ( '/' . $this->left_tag . 'elseif\s+(.+?)' . $this->right_tag . "/is", '<?php } elseif (\\1) { ?>', $template );
		//include 在类实例化类的php目录
		$template = preg_replace ( '/' . $this->left_tag . 'include\s+(.*?)' . $this->right_tag . "/is", '<?php include \'\\1\'; ?>', $template );
		//template
		$template = preg_replace ( '/' . $this->left_tag . 'template\s+([\w\/]+?)' . $this->right_tag . "/is", '<?php $this->display("\\1",0);?>', $template );
		//loop as key=>value
		$template = preg_replace ( '/' . $this->left_tag . $this->loop_key_value_regexp . $this->right_tag . "/ies", "\$this->loop('\\1', '\\2', '\\3')", $template );
		//loop as value
		$template = preg_replace ( '/' . $this->left_tag . $this->loop_value_regexp . $this->right_tag . "/ies", "\$this->loop('\\1', '', '\\2', '\\3')", $template );
		//loopelse
		$template = preg_replace ( '/' . $this->left_tag . 'loopelse' . $this->right_tag . "/is", "<?php }} else {{ ?>", $template );
		//结束/loop
		$template = preg_replace ( '/' . $this->left_tag . '\/loop' . $this->right_tag . "/is", "<?php }}?>", $template );
		//template.var
		$template = preg_replace ( '/' . $this->template_var_regexp . "/ise", "\$this->replace_var_in_template('\\1')", $template );
		//echo template.var
		$template = preg_replace ( '/' . $this->left_tag . $this->template_var_regexp . $this->right_tag . "/ise", "\$this->replace_var_in_template(<?php echo '\\1' ?>)", $template );
		//var
		$template = preg_replace ( '/\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/ise', "\$this->replace_var('\\1')", $template );
		//{const.}
		$template = preg_replace ( '/' . $this->left_tag . $this->const_regexp . $this->right_tag . "/isU", "<?php echo \\1; ?>", $template );
		//const.
		$template = preg_replace ( '/' . $this->const_regexp . "/isU", "\\1", $template );
		
		//解析左右鉴定符
		$template = preg_replace ( '/' . $this->left_tag . '/i', "<?php echo ", $template );
		$template = preg_replace ( '/' . $this->right_tag . '/i', ";?>", $template );
		
		return "$template";
	}
	
	private function get_file($file_dir) {
		$file_dir = $this->vars[$file_dir];
		$template_content = "";
		if (file_exists ( $file_dir )) {
			$template_content = file_get_contents ( $file_dir );
			$template_content = $this->parse ( $template_content );
		}
		return $template_content;
	}
	
	private function replace_var($var_name) {
		if ($var_name == 'this')
			return '$this';
		else
			return "\$this->vars['{$var_name}']";
	}
	
	private function replace_var_in_array($string) {
		$string = preg_replace ( '/\.([>a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/', ".'\\1'", $string );
		$string = preg_replace ( '/^(\$[>a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\./', " \\1[", $string );
		$string = preg_replace ( '/ (\$[>a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\./', " \\1[", $string );
		$string = preg_replace ( '/\./', "][", $string );
		return $string;
	}
	
	private function replace_var_in_template($string) {
		$string = preg_replace ( '/\.([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/', ".'\\1'", $string );
		$string = preg_replace ( '/^([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\./', "\\1[", $string );
		$string = preg_replace ( '/\./', "][", $string );
		$string = preg_replace ( '/template/', "\$this->template_vars", $string );
		return $string . ']';
	}
	
	private function loop($array, $key, $value) {
		preg_match ( '/\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/', $array, $match_array );
		$loop_var_name = str_replace ( '$', "", $match_array [1] );
		if (empty ( $key )) {
			return "<?php \$this->template_vars ['loop'] ['{$loop_var_name}'] ['loop_num'] = 0; if(!empty(" . $array . ")){ foreach((array)" . $array . ' as ' . $value . ') { $this->template_vars ["loop"] [\'' . $loop_var_name . '\'] ["loop_num"]++;?>';
		}
		return "<?php \$this->template_vars ['loop'] ['{$loop_var_name}'] ['loop_num'] = 0; if(!empty(" . $array . ")){ foreach((array)" . $array . ' as ' . $key . '=>' . $value . ') { $this->template_vars ["loop"] [\'' . $loop_var_name . '\'] ["loop_num"]++; ?>';
	}
	
	private function get_function($string) {
		$string = preg_replace ( '/([a-zA-Z0-9_\x7f-\xff\.\$\-\>\']*)\|(\w+):?(.*)/is', " \$this->\\2(\\1,'\\3')", $string );
		return '<?php echo' . $string . '; ?>';
	}
	
	private function replace($string, $param_str) {
		$params = explode ( ":", $param_str );
		isset ( $params [1] ) || $params [1] = '';
		return str_replace ( $params [0], $params [1], $string );
	}
	
	private function ftime($tstring, $format) {
		return date ( $format, $tstring );
	}
	
	private function dtime($tsring, $add = null) {
		$time = time ();
		if ($tsring > $time) {
			return "0秒";
		}
		$sec = $time - $tsring;
		$minute = intval ( bcdiv ( $sec, 60 ) );
		if ($minute > 0) {
			$hour = intval ( bcdiv ( $minute, 60 ) );
			if ($hour > 0) {
				$day = intval ( bcdiv ( $hour, 24 ) );
				if ($day > 0) {
					$month = intval ( bcdiv ( $day, 60 ) );
					if ($month > 0)
						return $month . '个月';
				}
				return $hour . '小时';
			}
			return $minute . '分钟';
		}
		return $sec . '秒';
	}
	
	private function get_image_url($base_image_name, $ext) {
		require_once LIB_URL . 'class_file.php';
		return File::get_image_name ( $base_image_name, $ext );
	}
	
	private function make_url($base_param, $ext_param) {
		$url_data = explode ( ":", $ext_param );
		$url_data [] = $base_param;
		global $Object_url;
		return $Object_url->mk_url ( $url_data );
	}
	
	private function get_user_avatar($avatar_info, $size) {
		if (preg_match ( "/http|\./", $avatar_info ))
			return $avatar_info;
		if (preg_match ( "/^\d+$/", $avatar_info )) {
			$uid = intval ( $avatar_info );
			if (isset ( $this->avatar_cache_array [$uid] ))
				return $this->avatar_cache_array [$uid];
			require_once LIB_URL . 'class_file.php';
			$avatar_path = "avatars/" . File::get_avatar_file_pre_path ( $uid ) . $size . '.jpg';
			if (file_exists ( ROOT . '/www/upload/member/' . $avatar_path )) {
				$this->avatar_cache_array [$uid] = '/upload/member/' . $avatar_path;
				return '/upload/member/' . $avatar_path;
			}
			return '/images/member/noavatar_' . $size . '.jpg';
		}
		return $avatar_info . "$size.jpg";
	}
}