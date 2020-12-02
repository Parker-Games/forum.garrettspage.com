<?php
// (c) Copyright by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Elastic_Prefilter
{
	/**
	 * Returns Elastic prefilter logic to possibly insert in a closure
	 * @param $fields
	 * @param $entry
	 * @return array
	 */
	public function get($fields, $entry)
	{
		return array_filter(
			$fields,
			function ($field) use ($entry) {
				return ! isset($entry[$field]);
			}
		);
	}
}
