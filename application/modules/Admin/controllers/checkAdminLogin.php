<?php
/**
 *	File: checkAdminLogin.php 
 *  Functionality: Check admin is login or not
 *  Author: Nic XIE
 *  Date: 2013-4-8
 */

session_start();	
if(!$_SESSION['adminID']){
	jsRedirect('/admin');
}