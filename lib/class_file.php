<?php
class File {
	
	private static $mimes = array ();
	
	/**
	 * 获取用户的头像
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	static function get_avatar_path($uid) {
		$uid = sprintf ( "%09d", $uid );
		$dir1 = substr ( $uid, 0, 3 );
		$dir2 = substr ( $uid, 3, 2 );
		$dir3 = substr ( $uid, 5, 2 );
		return $dir1 . '/' . $dir2 . '/' . $dir3;
	}
	
	/**
	 * 获取用户头像的前部分url
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	static function get_avatar_file_pre_path($uid) {
		$uid = abs ( intval ( $uid ) );
		$uid = sprintf ( "%09d", $uid );
		$dir1 = substr ( $uid, 0, 3 );
		$dir2 = substr ( $uid, 3, 2 );
		$dir3 = substr ( $uid, 5, 2 );
		return $dir1 . '/' . $dir2 . '/' . $dir3 . '/' . substr ( $uid, - 2 ) . "_avatar_";
	}
	
	/**
	 * 改变文件的名字为英文
	 * Enter description here ...
	 * @param unknown_type $file_name
	 */
	static function change_file_name_to_en($file_name) {
		return preg_replace ( '|[^\.]*\.|', rand ( 0, 1000 ) . time () . ".", $file_name );
	}
	
	/**
	 * 列举目录下的文件
	 * Enter description here ...
	 * @param unknown_type $folder
	 * @param unknown_type $levels
	 */
	static function list_files($folder = '', $levels = 100) {
		if (empty ( $folder ))
			return false;
		if (! $levels)
			return false;
		$files = array ();
		if ($dir = @opendir ( $folder )) {
			while ( ($file = readdir ( $dir )) !== false ) {
				if (in_array ( $file, array ('.', '..' ) ))
					continue;
				if (is_dir ( $folder . '/' . $file )) {
					$files2 = self::list_files ( $folder . '/' . $file, $levels - 1 );
					if ($files2)
						$files = array_merge ( $files, $files2 );
					else
						$files [] = realpath ( $folder . '/' . $file ) . '/';
				} else {
					$files [] = realpath ( $folder . '/' . $file );
				}
			}
		}
		@closedir ( $dir );
		return $files;
	}
	
	/**
	 * 创建上传目录
	 * Enter description here ...
	 */
	static function get_upload_dir($app = "", $type = "i", $uid = "") {
		$upload_base_dir = (empty ( $app )) ? UPLOAD_URL : ROOT . '/www/upload/' . $app . '/';
		($type == "i") ? $upload_base_dir .= 'images/' : $upload_base_dir .= 'file/';
		if (empty ( $uid )) {
			$time = date ( "Y-m" );
			$y = substr ( $time, 0, 4 );
			$m = substr ( $time, 5, 2 );
			$upload_dir = $upload_base_dir . "$y/$m/";
			if (! file_exists ( $upload_dir )) {
				self::mk_file ( $upload_base_dir );
				self::mk_file ( $upload_base_dir . "$y/" ) || exit ( '上传目录无法创建' );
				self::mk_file ( $upload_dir ) || exit ( '上传目录无法创建' );
			}
			return $upload_dir;
		} else {
			$uid = abs ( intval ( $uid ) );
			$uid = sprintf ( "%09d", $uid );
			$dir1 = substr ( $uid, 0, 3 );
			$dir2 = substr ( $uid, 3, 2 );
			$dir3 = substr ( $uid, 5, 2 );
			$dir4 = substr ( $uid, - 2 );
			$upload_dir = $upload_base_dir . "$dir1/$dir2/$dir3/$dir4/";
			if (! file_exists ( $upload_dir )) {
				self::mk_file ( $upload_base_dir );
				self::mk_file ( $upload_base_dir . "$dir1/" ) || exit ( '上传目录无法创建' );
				self::mk_file ( $upload_base_dir . "$dir1/$dir2/" ) || exit ( '上传目录无法创建' );
				self::mk_file ( $upload_base_dir . "$dir1/$dir2/$dir3/" ) || exit ( '上传目录无法创建' );
				self::mk_file ( $upload_dir ) || exit ( '上传目录无法创建' );
			}
			return $upload_dir;
		}
	
	}
	
	/**
	 * 创建目录
	 * Enter description here ...
	 * @param unknown_type $dir
	 */
	static function mk_file($dir) {
		if (file_exists ( $dir ))
			return true;
		if (@mkdir ( $dir )) {
			$stat = @stat ( dirname ( $dir ) );
			$dir_perms = $stat ['mode'] & 0007777; // Get the permission bits.
			@chmod ( $dir, $dir_perms );
			return true;
		}
		return false;
	}
	
	/**
	 * 图片上传
	 * @param file $file
	 * @param array $allow_ext_array
	 * @param array $min_size 
	 * @param app $app
	 * @return array('result'=>true,'filr'=>'filr_dir','error'=>'error');
	 */
	static function image_upload(&$file, $allow_ext_array, $min_size = array(), $app = "", $uid = "") {
		$result = array ('result' => false, 'error' => "上传文件失败" );
		if ($file ['error'] != 0)
			return $result;
		$file_type = self::get_filetype ( $file ['name'] );
		if (! in_array ( strtolower ( $file_type ['ext'] ), $allow_ext_array )) {
			$result ['error'] = '只支持' . implode ( ',', $allow_ext_array ) . '格式';
			return $result;
		}
		$upload_dir = self::get_upload_dir ( $app, 'i', $uid );
		if (is_uploaded_file ( $file ['tmp_name'] )) {
			list ( $width, $height, $type, $attr ) = getimagesize ( $file ['tmp_name'] );
			if (! empty ( $min_size ) && ($width < $min_size [0] && $height < $min_size [1])) {
				$result ['error'] = ('图片太小了^_^,你的让我把图片看清楚啊,我至少要' . implode ( '*', $min_size ) . '才可以哦');
				return $result;
			}
			$file ['name'] = self::change_file_name_to_en ( $file ['name'] );
			$file_name = self::unique_filename ( $upload_dir, $file ['name'] );
			if (move_uploaded_file ( $file ['tmp_name'], $upload_dir . $file_name )) {
				$result ['result'] = true;
				$result ['error'] = null;
				$result ['file'] = $upload_dir . $file_name;
				return $result;
			}
			return $result;
		}
		return $result;
	}
	
	static function images_upload(&$file, $allow_ext_array, $min_size = array(), $app = "", $uid = "") {
		$result = array ();
		foreach ( $file ['error'] as $key => $error ) {
			if ($error == 4)
				continue;
			$result [$key] ['result'] = false;
			$result [$key] ['info'] = "上传" . $file ['name'] [$key] . "文件失败";
			if ($error != 0)
				continue;
			$file_type = self::get_filetype ( $file ['name'] [$key] );
			if (! in_array ( strtolower ( $file_type ['ext'] ), $allow_ext_array )) {
				$result [$key] ['info'] .= ',只支持' . implode ( ',', $allow_ext_array ) . '格式';
				continue;
			}
			$upload_dir = self::get_upload_dir ( $app, $uid );
			if (is_uploaded_file ( $file ['tmp_name'] [$key] )) {
				list ( $width, $height, $type, $attr ) = getimagesize ( $file ['tmp_name'] [$key] );
				if (! empty ( $min_size ) && ($width < $min_size [0] && $height < $min_size [1])) {
					$result [$key] ['info'] .= ',图片太小了^_^,你的让我把图片看清楚啊,我至少要' . implode ( '*', $min_size ) . '才可以哦';
					continue;
				}
				$file ['name'] [$key] = self::change_file_name_to_en ( $file ['name'] [$key] );
				$file_name = self::unique_filename ( $upload_dir, $file ['name'] [$key] );
				if (move_uploaded_file ( $file ['tmp_name'] [$key], $upload_dir . $file_name )) {
					$result [$key] ['result'] = true;
					$result [$key] ['info'] = "上传" . $file ['name'] [$key] . '文件成功';
					$result [$key] ['images_url'] = $upload_dir . $file_name;
					continue;
				}
			}
		}
		return $result;
	}
	/**
	 * 
	 * 文件上传
	 * @param unknown_type $file
	 * @param unknown_type $allow_ext_array
	 * @param unknown_type $min_size
	 * @param unknown_type $upload_dir
	 */
	static function file_upload(&$file, $allow_ext_array, $min_size = 0, $app = "", $uid = "") {
		$result = array ('result' => false, 'error' => "上传文件失败" );
		if ($file ['error'] != 0)
			return $result;
		$file_type = self::get_filetype ( $file ['name'] );
		if (! in_array ( strtolower ( $file_type ['ext'] ), $allow_ext_array )) {
			$result ['error'] = '只支持' . implode ( ',', $allow_ext_array ) . '格式';
			return $result;
		}
		$upload_dir = self::get_upload_dir ( $app, 'f', $uid );
		if (is_uploaded_file ( $file ['tmp_name'] )) {
			if ($min_size > 0) {
				$file_size = filesize ( $file ['tmp_name'] );
				if ($min_size * 1024 < $file_size) {
					$result ['error'] = '文件大小不的超过' . $min_size . 'K';
					return $result;
				}
			}
			$file ['name'] = self::change_file_name_to_en ( $file ['name'] );
			$file_name = self::unique_filename ( $upload_dir, $file ['name'] );
			if (move_uploaded_file ( $file ['tmp_name'], $upload_dir . $file_name )) {
				$result ['result'] = true;
				$result ['error'] = null;
				$result ['file'] = $upload_dir . $file_name;
				return $result;
			}
			return $result;
		}
		return $result;
	}
	/**
	 * 获取不同名字的图片
	 * Enter description here ...
	 * @param unknown_type $base_image_name
	 * @param unknown_type $ext
	 */
	static function get_image_name($base_image_name, $ext = "") {
		$file_type = self::get_filetype ( $base_image_name );
		return str_replace ( "." . $file_type ['ext'], "-" . $ext . "." . $file_type ['ext'], $base_image_name );
	}
	
	/**
	 * 删除多不同图片
	 * Enter description here ...
	 * @param unknown_type $base_image_name
	 * @param unknown_type $image_dir
	 */
	static function del_image($base_image_name, $image_dir = UPLOAD_URL) {
		$file_type = self::get_filetype ( $base_image_name );
		for($i = 1; $i < 5; $i ++) {
			$file_name = str_replace ( "." . $file_type ['ext'], "-" . $i . "." . $file_type ['ext'], $base_image_name );
			(file_exists ( $image_dir . $file_name )) && unlink ( $image_dir . $file_name );
		}
	}
	
	/**
	 * 获取文件目录下唯一的文件名字
	 * Enter description here ...
	 * @param unknown_type $dir
	 * @param unknown_type $filename
	 */
	static function unique_filename($dir, $filename) {
		// sanitize the file name before we begin processing
		$filename = self::sanitize_file_name ( $filename );
		
		// separate the filename into a name and extension
		$info = pathinfo ( $filename );
		$ext = ! empty ( $info ['extension'] ) ? '.' . $info ['extension'] : '';
		$name = basename ( $filename, $ext );
		
		// edge case: if file is named '.ext', treat as an empty name
		if ($name === $ext)
			$name = '';
		
		// Increment the file number until we have a unique file to save in $dir. Use callback if supplied.
		$number = '';
		// change '.ext' to lower case
		if ($ext && strtolower ( $ext ) != $ext) {
			$ext2 = strtolower ( $ext );
			$filename2 = preg_replace ( '|' . preg_quote ( $ext ) . '$|', $ext2, $filename );
			// check for both lower and upper case extension or image sub-sizes may be overwritten
			while ( file_exists ( $dir . "/$filename" ) || file_exists ( $dir . "/$filename2" ) ) {
				$new_number = $number + 1;
				$filename = str_replace ( "$number$ext", "$new_number$ext", $filename );
				$filename2 = str_replace ( "$number$ext2", "$new_number$ext2", $filename2 );
				$number = $new_number;
			}
			return $filename2;
		}
		while ( file_exists ( $dir . "/$filename" ) ) {
			if ('' == "$number$ext")
				$filename = $filename . ++ $number . $ext;
			else
				$filename = str_replace ( "$number$ext", ++ $number . $ext, $filename );
		}
		return $filename;
	}
	
	/**
	 * 格式化文件名
	 * Enter description here ...
	 * @param unknown_type $filename
	 */
	static function sanitize_file_name($filename) {
		$filename_raw = $filename;
		$special_chars = array ("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}", chr ( 0 ) );
		$filename = str_replace ( $special_chars, '', $filename );
		$filename = preg_replace ( '/[\s-]+/', '-', $filename );
		$filename = trim ( $filename, '.-_' );
		
		// Split the filename into a base and extension[s]
		$parts = explode ( '.', $filename );
		
		// Return if only one extension
		if (count ( $parts ) <= 2)
			return $filename;
		
		// Process multiple extensions
		$filename = array_shift ( $parts );
		$extension = array_pop ( $parts );
		$mimes = self::get_allowed_mime_types ();
		
		// Loop over any intermediate extensions.  Munge them with a trailing underscore if they are a 2 - 5 character
		// long alpha string not in the extension whitelist.
		foreach ( ( array ) $parts as $part ) {
			$filename .= '.' . $part;
			if (preg_match ( "/^[a-zA-Z]{2,5}\d?$/", $part )) {
				$allowed = false;
				foreach ( $mimes as $ext_preg => $mime_match ) {
					$ext_preg = '!^(' . $ext_preg . ')$!i';
					if (preg_match ( $ext_preg, $part )) {
						$allowed = true;
						break;
					}
				}
				if (! $allowed)
					$filename .= '_';
			}
		}
		$filename .= '.' . $extension;
		return $filename;
	}
	
	/**
	 * 检查文件类型
	 * Enter description here ...
	 * @param unknown_type $filename
	 */
	static function get_filetype($filename) {
		$mimes = self::get_allowed_mime_types ();
		$type = false;
		$ext = false;
		foreach ( $mimes as $ext_preg => $mime_match ) {
			$ext_preg = '!\.(' . $ext_preg . ')$!i';
			if (preg_match ( $ext_preg, $filename, $ext_matches )) {
				$type = $mime_match;
				$ext = $ext_matches [1];
				break;
			}
		}
		return array ('ext' => $ext, 'type' => $type );
	}
	
	/**
	 * 获取允许的文件类型
	 * Enter description here ...
	 */
	static function get_allowed_mime_types() {
		if (empty ( self::$mimes )) {
			// Accepted MIME types are set here as PCRE unless provided.
			self::$mimes = array ('jpg|jpeg|jpe' => 'image/jpeg', 'gif' => 'image/gif', 'png' => 'image/png', 'bmp' => 'image/bmp', 'tif|tiff' => 'image/tiff', 'ico' => 'image/x-icon', 'asf|asx|wax|wmv|wmx' => 'video/asf', 'avi' => 'video/avi', 'divx' => 'video/divx', 'flv' => 'video/x-flv', 'mov|qt' => 'video/quicktime', 'mpeg|mpg|mpe' => 'video/mpeg', 'txt|asc|c|cc|h' => 'text/plain', 'csv' => 'text/csv', 'tsv' => 'text/tab-separated-values', 'ics' => 'text/calendar', 'rtx' => 'text/richtext', 'css' => 'text/css', 'htm|html' => 'text/html', 'mp3|m4a|m4b' => 'audio/mpeg', 'mp4|m4v' => 'video/mp4', 'ra|ram' => 'audio/x-realaudio', 'wav' => 'audio/wav', 'ogg|oga' => 'audio/ogg', 'ogv' => 'video/ogg', 'mid|midi' => 'audio/midi', 'wma' => 'audio/wma', 'mka' => 'audio/x-matroska', 'mkv' => 'video/x-matroska', 'rtf' => 'application/rtf', 'js' => 'application/javascript', 'pdf' => 'application/pdf', 'doc|docx' => 'application/msword', 'pot|pps|ppt|pptx|ppam|pptm|sldm|ppsm|potm' => 'application/vnd.ms-powerpoint', 'wri' => 'application/vnd.ms-write', 'xla|xls|xlsx|xlt|xlw|xlam|xlsb|xlsm|xltm' => 'application/vnd.ms-excel', 'mdb' => 'application/vnd.ms-access', 'mpp' => 'application/vnd.ms-project', 'docm|dotm' => 'application/vnd.ms-word', 'pptx|sldx|ppsx|potx' => 'application/vnd.openxmlformats-officedocument.presentationml', 'xlsx|xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml', 'docx|dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml', 'onetoc|onetoc2|onetmp|onepkg' => 'application/onenote', 'swf' => 'application/x-shockwave-flash', 'class' => 'application/java', 'tar' => 'application/x-tar', 'zip' => 'application/zip', 'gz|gzip' => 'application/x-gzip', 'rar' => 'application/rar', '7z' => 'application/x-7z-compressed', 'exe' => 'application/x-msdownload', // openoffice formats
'odt' => 'application/vnd.oasis.opendocument.text', 'odp' => 'application/vnd.oasis.opendocument.presentation', 'ods' => 'application/vnd.oasis.opendocument.spreadsheet', 'odg' => 'application/vnd.oasis.opendocument.graphics', 'odc' => 'application/vnd.oasis.opendocument.chart', 'odb' => 'application/vnd.oasis.opendocument.database', 'odf' => 'application/vnd.oasis.opendocument.formula', // wordperfect formats
'wp|wpd' => 'application/wordperfect' );
		}
		return self::$mimes;
	}
	
	static function write($content, $file_dir) {
		if (file_exists ( $file_dir )) {
			$file_hand = fopen ( $file_dir, 'w' );
			fwrite ( $file_hand, $content );
			fclose ( $file_hand );
			return true;
		}
		return false;
	}
}