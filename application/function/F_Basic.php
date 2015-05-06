<?php
/**
 * File: F_Basic.php
 * Functionality: Global basic functions 
 * Author: Nic XIE & IT Technology Department
 * Date: 2011-11-20
 */

// Anti_SQL Injection, escape quotes
function filter($content) {
    if (!get_magic_quotes_gpc()) {
        return addslashes($content);
    } else {
        return $content;
    }
}

//对字符串等进行过滤
function filterStr($arr) {  
    if (!isset($arr)) {
        return null;
    }

    if (is_array($arr)) {
        foreach ($arr as $k => $v) {
            $arr[$k] = filter(stripSQLChars(stripHTML(trim($v), true)));
        }
    } else {
        $arr = filter(stripSQLChars(stripHTML(trim($arr), true)));
    }

    return $arr;
}

function stripHTML($content, $xss = true) {
    $search = array("@<script(.*?)</script>@is",
        "@<iframe(.*?)</iframe>@is",
        "@<style(.*?)</style>@is",
        "@<(.*?)>@is"
    );

    $content = preg_replace($search, '', $content);

    if($xss){
        $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 
        'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 
        'layer', 'bgsound', 'title', 'base');
                                
        $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy',      'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
        $ra = array_merge($ra1, $ra2);
        
        $content = str_ireplace($ra, '', $content);
    }

    return strip_tags($content);
}

function removeXSS($val) {
    // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
    // this prevents some character re-spacing such as <javaΘscript>
    // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
    $val = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $val);

    // straight replacements, the user should never need these since they're normal characters
    // this prevents like <IMG SRC=&#X40&#X61&#X76&#X61&#X73&#X63&#X72&#X69&#X70&#X74&#X3A&#X61&#X6C&#X65&#X72&#X74&#X28&#X27&#X58&#X53&#X53&#X27&#X29>
    $search = 'abcdefghijklmnopqrstuvwxyz';
    $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $search .= '1234567890!@#$%^&*()';
    $search .= '~`";:?+/={}[]-_|\'\\';
    for ($i = 0; $i < strlen($search); $i++) {
        // ;? matches the ;, which is optional
        // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

        // &#x0040 @ search for the hex values
        $val = preg_replace('/(&#[x|X]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
        // @ @ 0{0,7} matches '0' zero to seven times
        $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
    }

    // now the only remaining whitespace attacks are \t, \n, and \r
    $ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 
                            'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 
                            'layer', 'bgsound', 'title', 'base');
                            
    $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy',      'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
    $ra = array_merge($ra1, $ra2);

    $found = true; // keep replacing as long as the previous round replaced something
    while ($found == true) {
        $val_before = $val;
        for ($i = 0; $i < sizeof($ra); $i++) {
            $pattern = '/';
            for ($j = 0; $j < strlen($ra[$i]); $j++) {
                if ($j > 0) {
                    $pattern .= '(';
                    $pattern .= '(&#[x|X]0{0,8}([9][a][b]);?)?';
                    $pattern .= '|(&#0{0,8}([9][10][13]);?)?';
                    $pattern .= ')?';
                }
                $pattern .= $ra[$i][$j];
            }
            $pattern .= '/i';
            $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
            $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
            if ($val_before == $val) {
                // no replacements were made, so exit the loop
                $found = false;
            }
        }
    }

    return $val;
}

/**
 *  Strip specail SQL chars
 */
function stripSQLChars($str) {
    $replace = array('SELECT', 'INSERT', 'DELETE', 'UPDATE', 'CREATE', 'DROP', 'VERSION', 'DATABASES',
        'TRUNCATE', 'HEX', 'UNHEX', 'CAST', 'DECLARE', 'EXEC', 'SHOW', 'CONCAT', 'TABLES', 'CHAR', 'FILE',
        'SCHEMA', 'DESCRIBE', 'UNION', 'JOIN', 'ALTER', 'RENAME', 'LOAD', 'FROM', 'SOURCE', 'INTO', 'LIKE', 'PING', 'PASSWD');
    
    return str_ireplace($replace, '', $str);
}

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