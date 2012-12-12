<?php
class System {
	
	static $seed = '';
	
	static function get_rand($min = 0, $max = 0) {
		// Reset $rnd_value after 14 uses
		// 32(md5) + 40(sha1) + 40(sha1) / 8 = 14 random numbers from $rnd_value
		$rnd_value = md5 ( uniqid ( microtime () . mt_rand (), true ) . self::$seed );
		$rnd_value .= sha1 ( $rnd_value );
		$rnd_value .= sha1 ( $rnd_value . self::$seed );
		self::$seed = md5 ( self::$seed . $rnd_value );
		// Take the first 8 digits for our value
		$value = substr ( $rnd_value, 0, 8 );
		// Strip the first eight, leaving the remainder for the next call to wp_rand().
		$rnd_value = substr ( $rnd_value, 8 );
		$value = abs ( hexdec ( $value ) );
		// Reduce the value to be within the min - max range
		// 4294967295 = 0xffffffff = max random number
		if ($max != 0)
			$value = $min + (($max - $min + 1) * ($value / (4294967295 + 1)));
		return abs ( intval ( $value ) );
	}
	
	static function get_random_string($length = 12, $special_chars = true, $extra_special_chars = false) {
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		if ($special_chars)
			$chars .= '!@#$%^&*()';
		if ($extra_special_chars)
			$chars .= '-_ []{}<>~`+=,.;:/?|';
		$string = '';
		for($i = 0; $i < $length; $i ++) {
			$string .= substr ( $chars, self::get_rand ( 0, strlen ( $chars ) - 1 ), 1 );
		}
		return $string;
	}
	
	static function sub_str($string, $length, $dot = ' ...') {
		$strcut = "";
		if (strlen ( $string ) <= $length) {
			return $string;
		}
		
		$string = str_replace ( array ('&amp;', '&quot;', '&lt;', '&gt;' ), array ('&', '"', '<', '>' ), $string );
		
		if (strtolower ( DB_CHAR ) == 'utf8') {
			
			$n = $tn = $noc = 0;
			while ( $n < strlen ( $string ) ) {
				
				$t = ord ( $string [$n] );
				if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
					$tn = 1;
					$n ++;
					$noc ++;
				} elseif (194 <= $t && $t <= 223) {
					$tn = 2;
					$n += 2;
					$noc += 2;
				} elseif (224 <= $t && $t < 239) {
					$tn = 3;
					$n += 3;
					$noc += 2;
				} elseif (240 <= $t && $t <= 247) {
					$tn = 4;
					$n += 4;
					$noc += 2;
				} elseif (248 <= $t && $t <= 251) {
					$tn = 5;
					$n += 5;
					$noc += 2;
				} elseif ($t == 252 || $t == 253) {
					$tn = 6;
					$n += 6;
					$noc += 2;
				} else {
					$n ++;
				}
				
				if ($noc >= $length) {
					break;
				}
			
			}
			if ($noc > $length) {
				$n -= $tn;
			}
			
			$strcut = substr ( $string, 0, $n );
		
		} else {
			for($i = 0; $i < $length; $i ++) {
				$strcut .= ord ( $string [$i] ) > 127 ? $string [$i] . $string [++ $i] : $string [$i];
			}
		}
		
		$strcut = str_replace ( array ('&', '"', '<', '>' ), array ('&amp;', '&quot;', '&lt;', '&gt;' ), $strcut );
		
		return $strcut . $dot;
	}
	
	static function sub_html_string($text, $length = 100, $ending = '...', $considerHtml = false) {
		if ($considerHtml) {
			// if the plain text is shorter than the maximum length, return the whole text
			if (strlen ( preg_replace ( '/<.*?>/', '', $text ) ) <= $length) {
				return $text;
			}
			// splits all html-tags to scanable lines
			preg_match_all ( '/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER );
			$total_length = 0; //strlen ( $ending );
			$open_tags = array ();
			$truncate = '';
			//print_r($lines);
			foreach ( $lines as $line_matchings ) {
				// if the maximum length is reached, get off the loop
				if ($total_length >= $length) {
					break;
				}
				// if there is any html-tag in this line, handle it and add it (uncounted) to the output
				if (! empty ( $line_matchings [1] )) {
					// if it's an "empty element" with or without xhtml-conform closing slash (f.e.)
					if (preg_match ( '/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings [1] )) {
						// do nothing
					// 是关闭标签
					} else if (preg_match ( '/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings [1], $tag_matchings )) {
						// 删除这个关闭标签
						$pos = array_search ( $tag_matchings [1], $open_tags );
						if ($pos !== false) {
							unset ( $open_tags [$pos] );
						}
					
		// 是开始标签
					} else if (preg_match ( '/^<\s*([^\s>!]+).*?>$/s', $line_matchings [1], $tag_matchings )) {
						// 添加这个标签
						array_unshift ( $open_tags, strtolower ( $tag_matchings [1] ) );
					}
					// add html-tag to $truncate'd text
					$truncate .= $line_matchings [1];
				}
				$pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
				$t_string = array ();
				preg_match_all ( $pa, $line_matchings [2], $t_string );
				$content_length = count ( $t_string [0] );
				if ($total_length + $content_length > $length) {
					// the number of characters which are left
					$left = $length - $total_length;
					$truncate .= join ( '', array_slice ( $t_string [0], 0, $left ) );
					break;
				} else {
					$truncate .= $line_matchings [2];
					$total_length += $content_length;
				}
			}
		} else {
			if (strlen ( $text ) <= $length) {
				return $text;
			} else {
				$truncate = self::sub_str ( $text, 0, $length - strlen ( $ending ), $ending );
			}
		}
		if ($considerHtml) {
			// close all unclosed html-tags
			foreach ( $open_tags as $tag ) {
				$truncate .= '</' . $tag . ">";
			}
		}
		// add the defined ending to the text
		return $truncate;
	}
	
	/**
	 * @deprecated 对翻页的起始位置进行判断和调整
	 * @param int $page 页码
	 * @param int $ppp 每页大小
	 * @param int $totalnum 总纪录数
	 * @return unknown
	 */
	static function get_page_star_num($page, $ppp, $totalnum) {
		$totalpage = ceil ( $totalnum / $ppp ); //====ceil函数取正再加1，例如：ceil(2.99)=3
		$page = max ( 1, min ( $totalpage, intval ( $page ) ) ); //=====min返回最小的那个数
		return ($page - 1) * $ppp;
	}
	
	/**
	 * @deprecated 翻页函数
	 * @param int $num 总纪录条数
	 * @param int $perpage 每页显示的条数
	 * @param int $curpage 当前页面
	 * @param string $mpurl url
	 * @return string 类似于: <div class="page">***</div>
	 * @param string $pages 按照每页$perpage条信息可以排列的页数
	 * @param float $page 当前页面的前后可见页数链接数目；例如：...<<3456789 10 11 12>>...3到12的个数为10个
	 * @param float $offset 当前页数前显示的链接数目；例如：...<<34[5]6789 10 11 12>>...当前页面是5，他前面可见链接2个3，4
	 */
	static function get_page_html($curpage, $perpage, $num, $mpurl) {
		$multipage = '';
		$mpurl .= strpos ( $mpurl, '?' ) ? '&' : '?';
		//note 需要分页
		if ($num > $perpage) {
			//note why
			$page = 10;
			$offset = 2;
			//note 总页数
			$pages = @ceil ( $num / $perpage );
			//note 如果所有的页数不超过10页； 例如：<<123456789>>
			if ($page > $pages) {
				$from = 1;
				$to = $pages;
			} else //note 如果所有的页数不超过10页； 例如：...<<123456789>>...
{
				$from = $curpage - $offset; //note 从当前页的前2页开始，开始链接的页数值
				$to = $from + $page - 1; //note 开始链接的页数值+1=显示可见的结尾链接的页数值
				if ($from < 1) //note 如果起始页小于1
{
					$to = $curpage + 1 - $from; //note 超前1几位
					$from = 1;
					if ($to - $from < $page) {
						$to = $page;
					}
				} elseif ($to > $pages) //note 如果最后一页大于总页数
{
					$from = $pages - $page + 1;
					$to = $pages;
				}
			}
			
			$multipage = ($curpage - $offset > 1 && $pages > $page ? '<a href="' . $mpurl . 'page=1" class="first"' . '>1 ...</a>&nbsp;&nbsp;' : '') . ($curpage > 1 ? '<a href="' . $mpurl . 'page=' . ($curpage - 1) . '" class="prev"' . '>&lsaquo;&lsaquo;</a>&nbsp;&nbsp;' : '');
			for($i = $from; $i <= $to; $i ++) {
				$multipage .= $i == $curpage ? '<strong>' . $i . '</strong>&nbsp;&nbsp;' : '<a href="' . $mpurl . 'page=' . $i . ($i == $pages ? '' : '') . '"' . '>' . $i . '</a>&nbsp;&nbsp;';
			}
			
			$multipage .= ($curpage < $pages ? '<a href="' . $mpurl . 'page=' . ($curpage + 1) . '" class="next"' . '>&rsaquo;&rsaquo;</a>&nbsp;&nbsp;' : '') . ($to < $pages ? '<a href="' . $mpurl . 'page=' . $pages . '" class="last"' . '>... ' . '</a>&nbsp;&nbsp;' : '') . ($pages > $page ? '<kbd><input type="text" name="custompage" size="3" onkeydown="if(event.keyCode==13) {window.location=\'' . $mpurl . 'page=\'+this.value; return false;}" /></kbd>' : '');
			
			$multipage = $multipage ? '<div class="pages"><em>&nbsp;一共有' . $num . '项结果&nbsp;&nbsp;</em>' . $multipage . '</div>' : '';
		}
		return $multipage;
	}

}