<?php
/**
 * File: F_Basic.php
 * Functionality: Global basic functions 
 * Author: Nic XIE & IT Technology Department
 * Date: 2011-11-20
 */

// Redirect directly
function redirect($URL = '', $second = 0) {
    if (!isset($URL)) {
        $URL = $_SERVER['HTTP_REFERER'];
    }
        ob_start();
        ob_end_clean();
        header("Location: ".$URL, TRUE, 302); //header("refresh:$second; url=$URL", TRUE, 302);
        ob_flush(); //可省略
        exit;
}


// Redirect and display message
function gotoURL($message = '', $URL = '') {
    if (!isset($URL)) {
        $URL = $_SERVER['HTTP_REFERER'];
    }

    if (isset($message)) {
        jsAlert($message);
    }

    echo "<script type='text/javascript'>window.location.href='$URL'</script>";
}


function generatePageLink4($page, $totalPages, $URL, $counts, $query = '') {
	$URL .= (strpos($URL, '?') === false ? '?' : '&');
    // First:
    $first = '首 页';
    $first = "<a href=".$URL."page=1$query>$first</a>";

    // Prev:
    $prev = '上一页';
    $previousPage = ($page > 1) ? $page - 1 : 1;
    $prev = "<a href=".$URL."page=$previousPage$query>$prev</a>";

    // Next:
    $next = '下一页';
    $nextPage = ($page == $totalPages) ? $totalPages : $page + 1;
    $next = "<a href=".$URL."page=$nextPage$query>$next</a>";

    // Last
    $last = '末 页';
    $last = "<a href=".$URL."page=$totalPages$query>$last</a>";

    $pageLink = $first . '&nbsp;&nbsp;' . $prev;
    $pageLink .= '&nbsp;&nbsp;' . $next . '&nbsp;&nbsp;' . $last;

    return $pageLink;
}

/*
 *Functionality: Generate Single-language[Chinese-simplified] pagenation navigator
  @Params:
  Int $page: current page
  Int $totalPages: total pages
  String $URL: target URL for pagenation
  Int $count: total records
  String $query: query string for SEARCH
 *  @Return: String pagenation navigator link
 */

function generatePageLink($page, $totalPages, $URL, $counts, $query = '') {
	$URL .= (strpos($URL, '?') === false ? '?' : '&');
    // First:
    $first = '首 页';
    $first = "<a href=".$URL."page=1$query>$first</a>";

    // Prev:
    $prev = '上一页';
    $previousPage = ($page > 1) ? $page - 1 : 1;
    $prev = "<a href=".$URL."page=$previousPage$query>$prev</a>";

    // Next:
    $next = '下一页';
    $nextPage = ($page == $totalPages) ? $totalPages : $page + 1;
    $next = "<a href=".$URL."page=$nextPage$query>$next</a>";

    // Last
    $last = '末 页';
    $last = "<a href=".$URL."page=$totalPages$query>$last</a>";

    // Total:
    $total = '共';

    $pageLink = $total . ' ' . $counts . '&nbsp;&nbsp;' . $first . '&nbsp;&nbsp;' . $prev;
    $pageLink .= '&nbsp;&nbsp;' . $next . '&nbsp;&nbsp;' . $last . '&nbsp;&nbsp;' . $page . '/' . $totalPages . '&nbsp';

    return $pageLink;
}

// Functionality: 生成带"转至"第几页的分页导航栏
function generatePageLink2($page, $totalPages, $URL, $counts, $query = '') {
	$sign = '?';
	if(strpos($URL, '?') !== FALSE){
		$sign = '&';
	}

    // First:
    $first = '首 页';
    $first = '<a href='.$URL.$sign.'page=1'.$query.'>'.$first.'</a>';

    // Prev:
    $prev = '上一页';
    $previousPage = ($page > 1) ? $page - 1 : 1;
    $prev = '<a href='.$URL.$sign.'page='.$previousPage.$query.'>'.$prev.'</a>';

    // Next:
    $next = '下一页';
    $nextPage = ($page == $totalPages) ? $totalPages : $page + 1;
    $next = '<a href='.$URL.$sign.'page='.$nextPage.$query.'>'.$next.'</a>';

    // Last
    $last = '末 页';
    $last = '<a href='.$URL.$sign.'page='.$totalPages.$query.'>'.$last.'</a>';

    // Total:
    $total = '共';

    $pageLink = $total . ' ' . $counts . '&nbsp;&nbsp;' . $first . '&nbsp;&nbsp;' . $prev;
    $pageLink .= '&nbsp;&nbsp;' . $next . '&nbsp;&nbsp;' . $last . '&nbsp;&nbsp;';

    $pageLink .= '<input type="text" id="txtGoto" name="txtGoto" size="5" maxlength="5" />';
    $pageLink .= '&nbsp;<input type ="button" class="btn btn-primary" id="btnGoto" name="btnGoto" value="转至" />';

    $pageLink .= '&nbsp;<span id="currentPage">' . $page . '</span>/<span id="totalPages">' . $totalPages . '</span>&nbsp';

    $pageLink .= '<br /><input type="hidden" id="self_url" name="self_url" value="' . $URL . '">';

    return $pageLink;
}


/**
 *  Functionality: 生成供静态化 URL 用并且带有 GOTO 功能的分页导航
 *  Remark: 首页, 上一页, 下一页, 末页中的 href 为 javascript:;
 *          而是赋予了class, 当前页与总页则使用了span, 模板中 JQuery 点击事件触发
 *          $('.pg_index').click(function(){ ... });
 */
function staticPageLink($page, $totalPages, $URL, $counts, $query = '') {

    // First:
    $first = '首 页';
    $first = "<a class='pg_index pointer'>$first</a>";

    // Prev:
    $prev = '上一页';
    $previousPage = ($page > 1) ? $page - 1 : 1;
    $prev = "<a class='pg_prev pointer' >$prev</a>";

    // Next:
    $next = '下一页';
    $nextPage = ($page == $totalPages) ? $totalPages : $page + 1;
    $next = "<a class='pg_next pointer'>$next</a>";

    // Last
    $last = '末 页';
    $last = "<a class='pg_last pointer'>$last</a>";

    // Total:
    $total = '共';

    $pageLink = $total . ' ' . $counts . '&nbsp;&nbsp;' . $first . '&nbsp;&nbsp;' . $prev;
    $pageLink .= '&nbsp;&nbsp;' . $next . '&nbsp;&nbsp;' . $last . '&nbsp;&nbsp;';

    $pageLink .= '<input type="text" id="txtGoto" name="txtGoto" size="3" maxlength="3" />';
    $pageLink .= '&nbsp;<input type ="button" id="btnGoto" name="btnGoto" value="转至" />';

    $pageLink .= '&nbsp;<span id="currentPage">' . $page . '</span>/<span id="totalPages">' . $totalPages . '</span>&nbsp';

    $pageLink .= '<br /><input type="hidden" id="self_url" name="self_url" value="' . $URL . '">';

    return $pageLink;
}


// Get current microtime
function calculateTime() {
    list($usec, $sec) = explode(' ', microtime());
    return ((float) $usec + (float) $sec);
}


/**
 * 裁剪中文
 * 
 * @param type $string
 * @param type $length
 * @param type $dot
 * @return type
 */
function cutstr($string, $length, $dot = ' ...') {
	if(strlen($string) <= $length) {
		return $string;
	}

	$pre = chr(1);
	$end = chr(1);
	$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), $string);

	$strcut = '';
	if(strtolower(CHARSET) == 'utf-8') {

		$n = $tn = $noc = 0;
		while($n < strlen($string)) {

			$t = ord($string[$n]);
			if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1; $n++; $noc++;
			} elseif(194 <= $t && $t <= 223) {
				$tn = 2; $n += 2; $noc += 2;
			} elseif(224 <= $t && $t <= 239) {
				$tn = 3; $n += 3; $noc += 2;
			} elseif(240 <= $t && $t <= 247) {
				$tn = 4; $n += 4; $noc += 2;
			} elseif(248 <= $t && $t <= 251) {
				$tn = 5; $n += 5; $noc += 2;
			} elseif($t == 252 || $t == 253) {
				$tn = 6; $n += 6; $noc += 2;
			} else {
				$n++;
			}

			if($noc >= $length) {
				break;
			}

		}
		if($noc > $length) {
			$n -= $tn;
		}

		$strcut = substr($string, 0, $n);

	} else {
		$_length = $length - 1;
		for($i = 0; $i < $length; $i++) {
			if(ord($string[$i]) <= 127) {
				$strcut .= $string[$i];
			} else if($i < $_length) {
				$strcut .= $string[$i].$string[++$i];
			}
		}
	}

	$strcut = str_replace(array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

	$pos = strrpos($strcut, chr(1));
	if($pos !== false) {
		$strcut = substr($strcut,0,$pos);
	}
	return $strcut.$dot;
}


function pr($arr) {
	echo '<pre>';
    print_r($arr);
	echo '</pre>';
}


function pp() {
	pr($_POST);
}


/**
 *  JavaScript alert
 */
function jsAlert($msg) {
    echo "<script type='text/javascript'>alert(\"$msg\")</script>";
}


/**
 *  JavaScript redirect
 */
function jsRedirect($url, $die = true) {
    echo "<script type='text/javascript'>window.location.href=\"$url\"</script>";
    if($die){
    	die;
    }
}


// verify page
function verifyPage($page, $totalPages){
	if ($page > $totalPages || !is_numeric($page) || $page <= 0) {
		$page = 1;
	}
	
	return $page;
}


/**
 * Echo and die
 */
function eand($msg){
	echo $msg; die;
}


/**
 * Echo html br
 */
function br(){
	echo '<br />';
}


/**
 * Echo html hr
 */
function hr(){
	echo '<hr/>';
}


// echo hidden div with msg
function echoHiddenDiv($msg){
	$html = '<div style="display:none">'.$msg.'</div>';
	echo $html;
}


// Highlight keyword
function highlight($str, $find, $color){
	return str_replace($find, '<font color="'.$color.'">'.$find.'</font>', $str);
}