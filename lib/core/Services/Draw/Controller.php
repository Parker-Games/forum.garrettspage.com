<?php
// (c) Copyright by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Draw_Controller
{
	function setUp()
	{
		global $prefs;

		if ($prefs['feature_file_galleries'] != 'y') {
			throw new Services_Exception_Disabled('feature_file_galleries');
		}

		if ($prefs['feature_draw'] != 'y') {
			throw new Services_Exception_Disabled('feature_draw');
		}
	}

	/**
	 * Returns the section for use with certain features like banning
	 * @return string
	 */
	function getSection()
	{
		return 'file_galleries';
	}

	function action_edit($input)
	{
		global $drawFullscreen;
		$headerlib = TikiLib::lib('header');
		$tikilib = TikiLib::lib('tiki');
		$access = TikiLib::lib('access');

		$drawFullscreen = true;

		$_REQUEST['fileId'] = $input->fileId->int();
		$_REQUEST['galleryId'] = $input->galleryId->int();
		$imgParams = $input->imgParams;
		if ($imgParams->fromFieldId) {
			$_REQUEST['fromFieldId'] = $input->imgParams->fromFieldId->int();
			$_REQUEST['fromItemId'] = $input->imgParams->fromItemId->int();
		}

		include_once 'tiki-edit_draw.php';
	}

	function action_replace($input)
	{
		//just a dummy for now, filegallery handles it all
	}

	function action_removeButtons()
	{
		global $prefs;
		return ['removeButtons' => $prefs['feature_draw_hide_buttons']];
	}
}
