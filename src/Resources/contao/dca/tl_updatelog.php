<?php

/**
 * WBGym
 * 
 * Copyright (C) 2015 Webteam Weinberg-Gymnasium Kleinmachnow
 * 
 * @package     WGBym
 * @author      Johannes Cram <j-cram@gmx.de>
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Table tl_updatelog
 */
$GLOBALS['TL_DCA']['tl_updatelog'] = array(
	// Config
	'config' => array(
		'dataContainer'		=> 'Table',
		'enableVersioning'	=> true,
		'sql' 				=> array(
			'keys' => array(
				'id' => 'primary'
			)
		)
	),

	// List
	'list' => array(
		'sorting' => array(
			'mode'	=> 2,
			'flag'		=> 1,
			'fields'	=> array('category'),
			'panelLayout' => 'filter;search,limit',
	),
	'label' => array(
		'fields'		=> array('name'),
	),
	'global_operations' => array(
			'all' => array(
				'label'		=> &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'		=> 'act=select',
				'class'		=> 'header_edit_all',
				'attributes'=> 'onclick="Backend.getScrollOffset();" accesskey="e"'
			)
		),
		'operations' => array(
			'edit' => array(
				'label'		=> &$GLOBALS['TL_LANG']['tl_updatelog']['edit'],
				'href'		=> 'act=edit',
				'icon'		=> 'edit.gif'
			),
			'copy' => array(
				'label'		=> &$GLOBALS['TL_LANG']['tl_updatelog']['copy'],
				'href'		=> 'act=copy',
				'icon'		=> 'copy.gif'
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_updatelog']['approved'],
				'icon'                => 'invisible.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'     => array('tl_updatelog', 'toggleIcon')
			),
			'delete' => array(
				'label'		=> &$GLOBALS['TL_LANG']['tl_updatelog']['delete'],
				'href'		=> 'act=delete',
				'icon'		=> 'delete.gif',
				'attributes'=> 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
			),
			'show' => array(
				'label'		=> &$GLOBALS['TL_LANG']['tl_updatelog']['show'],
				'href'		=> 'act=show',
				'icon'		=> 'show.gif'
			)
		)
	),

	// Palettes
	'palettes' => array(
		'__selector__' => array(''),
		'default' => '{general_header},category,name;{description_header},description;{visible_header},visible'
	),

	// Fields
	'fields' => array(
		'id' => array(
			'sql'		=> "int(10) unsigned NOT NULL auto_increment"
		),
		'tstamp' => array(
			'sql'		=> "int(10) unsigned NOT NULL default '0'"
		),
		'name' => array(
			'label'		=> &$GLOBALS['TL_LANG']['tl_updatelog']['name'],
			'exclude'	=> false,
			'inputType'	=> 'text',
			'search'	=> true,
			'eval'		=> array('mandatory' => true, 'tl_class' =>'w50'),
			'sql'		=> "varchar(64) NOT NULL default ''"
		),
		'category' => array(
			'label'		=> &$GLOBALS['TL_LANG']['tl_updatelog']['category'],
			'exclude'	=> false,
			'inputType'	=> 'select',
			'options' 	=> array(1 => 'Geplant', 2 => 'In Bearbeitung', 3 => 'Live'),
			'sorting'	=> true,
			'filter'	=> true,
			'eval'		=> array('mandatory' => true, 'tl_class' => 'w50'),
			'sql'		=> "tinyint(1) NOT NULL default '0'"
		),
		'description' => array(
			'label'		=> &$GLOBALS['TL_LANG']['tl_updatelog']['description'],
			'exclude'	=> false,
			'inputType'	=> 'textarea',
			'search'		=> true,
			'eval'		=> array('mandatory' => false, 'rte' => 'tinyMCE'),
			'sql'		=> "varchar(400) NOT NULL default ''"
		),
		'visible' => array(
			'label'				=> &$GLOBALS['TL_LANG']['tl_updatelog']['visible'],
			'exclude'			=> false,
			'filter'				=> true,
			'sorting'			=> true,
			'inputType'		=> 'checkbox',
			'eval'				=> array('tl_class' => 'w50'),
			'sql'				=> "tinyint(1) NOT NULL default '0'",
			'search'			=> true
		)
	)
);

class tl_updatelog extends Backend
{

	public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
	{
		if (strlen(Input::get('tid')))
		{
			$this->toggleVisibility(Input::get('tid'), (Input::get('state') == 1), (@func_get_arg(12) ?: null));
			$this->redirect($this->getReferer());
		}

		$href .= '&amp;tid='.$row['id'].'&amp;state='.$row['visible'];

		if ($row['visible'])
		{
			$icon = 'visible.gif';
		}

		return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
	}
	
		/**
	 * Disable/enable a user group
	 *
	 * @param integer       $intId
	 * @param boolean       $blnVisible
	 * @param DataContainer $dc
	 */
	public function toggleVisibility($intId, $blnVisible, DataContainer $dc=null)
	{


		$objVersions = new Versions('tl_updatelog', $intId);
		$objVersions->initialize();

		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_updatelog']['fields']['visible']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_updatelog']['fields']['visible']['save_callback'] as $callback)
			{
				if (is_array($callback))
				{
					$this->import($callback[0]);
					$blnVisible = $this->$callback[0]->$callback[1]($blnVisible, ($dc ?: $this));
				}
				elseif (is_callable($callback))
				{
					$blnVisible = $callback($blnVisible, ($dc ?: $this));
				}
			}
		}

		$time = time();

		// Update the database
		$this->Database->prepare("UPDATE tl_updatelog SET tstamp=$time, visible='" . ($blnVisible ? 1 : '') . "' WHERE id=?")
					   ->execute($intId);

		$objVersions->create();
		$this->log('A new version of record "tl_updatelog.id='.$intId.'" has been created'.$this->getParentEntries('tl_updatelog', $intId), __METHOD__, TL_GENERAL);


	}
}


?>