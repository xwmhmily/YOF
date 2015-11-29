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
    $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 
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
	$page = $page ? $page : 1;

    $URL .= (strpos($URL, '?') === FALSE ? '?' : '&');

    $link = '<ul class="pagination pull-right no-margin">';
    if($page == 1){
        $link .= '<li class="prev disabled">
                    <a href="#">
                        <i class="ace-icon fa fa-angle-double-left"></i>
                    </a>
                </li>';
    }else{
        $prev  = $URL.'page='.($page - 1).$query;
        $link .= '<li class="prev">
                    <a href="'.$prev.'">
                        <i class="ace-icon fa fa-angle-double-left"></i>
                    </a>
                </li>';
    }

    // 超过 10 页则要考虑前二后二中四
    if($totalPages > 10){
        $sep = TRUE;
    }

    $first = $URL.'page=1'.$query;
    if($page == 1){
        $active = 'active';
    }else{
        $active = '';
    }

    $link .= '<li class="'.$active.'">
                <a href="'.$first.'">1</a>
            </li>';

    if($totalPages >= 2){
        $second = $URL.'page=2'.$query;
        if($page == 2){
            $active = 'active';
        }else{
            $active = '';
        }

        $link .= '<li class="'.$active.'">
                    <a href="'.$second.'">2</a>
                </li>';
    }

    if($sep){
        if(($page - 2) > 3){
            $link .= '<li>
                   <a href="#">...</a>
                </li>';
        } 

        // 取中间四个
        for($i = ($page - 2); $i <= ($page + 2); $i++){
            if($i <= 2 || $i > ($totalPages - 2)){
                continue;
            }

            if($i > $totalPages){
                break;
            }

            if($page == ($totalPages - 1)){
                break;
            }

            $p = $URL.'page='.$i.$query;
            if($page == $i){
                $active = 'active';
            }else{
                $active = '';
            }

            $link .= '<li class="'.$active.'">
                        <a href="'.$p.'">'.$i.'</a>
                    </li>';
        }

        if(($page + 2) < ($totalPages - 2)){
            $link .= '<li>
                    <a href="#">...</a>
                </li>';
        }
    }else{
        for($i = 3; $i <= ($totalPages - 2); $i++){
            $p = $URL.'page='.$i.$query;
            if($page == $i){
                $active = 'active';
            }else{
                $active = '';
            }

            $link .= '<li class="'.$active.'">
                        <a href="'.$p.'">'.$i.'</a>
                    </li>';
        }
    }

    if($totalPages > 2){
        if(($totalPages - 1) != 2){
            $p = $URL.'page='.($totalPages-1).$query;
            if($page == ($totalPages-1)){
                $active = 'active';
            }else{
                $active = '';
            }

            $link .= '<li class="'.$active.'">
                    <a href="'.$p.'">'.($totalPages-1).'</a>
                </li>';
        }

        $p = $URL.'page='.$totalPages.$query;
        if($page == $totalPages){
            $active = 'active';
        }else{
            $active = '';
        }

        $link .= '<li class="'.$active.'">
                    <a href="'.$p.'">'.$totalPages.'</a>
                </li>';
    }

    if($page == $totalPages){
        $link .= '<li class="next disabled">
            <a href="#">
                <i class="ace-icon fa fa-angle-double-right"></i>
            </a>
        </li>';
    }else{
        $next  = $URL.'page='.($page + 1).$query;
        $link .= '<li class="next">
                    <a href="'.$next.'">
                        <i class="ace-icon fa fa-angle-double-right"></i>
                    </a>
                </li>';
    }

    $link .= '</ul>';

    return $link;
}

// Get current microtime
function calculateTime() {
    list($usec, $sec) = explode(' ', microtime());
    return ((float) $usec + (float) $sec);
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