<?php

/**
 * WBGym
 *
 * Copyright (C) 2008-2013 Webteam Weinberg-Gymnasium Kleinmachnow
 *
 * @package 	WGBym
 * @author 		Marvin Ritter <marvin.ritter@gmail.com>
 * @license 	http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

array_insert($GLOBALS['BE_MOD'], 5, array('content' => array(
	'updatelog' => array(
		'tables'	=> array('tl_updatelog'),
)
)));

/*
 * Front end modules
 */

$GLOBALS['FE_MOD']['wbgym']['wb_updatelog']  = 'WBGym\ModuleUpdatelog';
?>
