<?php

/**
 * WBGym
 * 
 * Copyright (C) 2015 Webteam Weinberg-Gymnasium Kleinmachnow
 * 
 * @package 	WGBym
 * @version 	0.3.0
 * @author 		Johannes Cram <j-cram@gmx.de>
 * @license 	http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Namespace
 */
namespace WBGym;

class ModuleUpdatelog extends \Module
{
protected $strTemplate = 'wb_updatelog';

	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### WBGym Update-Log ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}
		
		return parent::generate();
		
	}

protected function compile(){
	  $this->import('Database');
	  
	$data = $this->Database->prepare("
	SELECT * FROM tl_updatelog WHERE visible = 1 ORDER BY category
	")->execute();
	  
	  while($arrData = $data->fetchAssoc()) {
			switch($arrData['category']){
				case '1' :
					$arrRows[$arrData['category']][] = $arrData;
					break;
				case '2' :
					$arrRows[$arrData['category']][] = $arrData;
					break;
				case '3' : 
					$arrRows[$arrData['category']][] = $arrData;
					break;
			}
	  }
	  $this->Template->data = $arrRows;

	
}
}
?>