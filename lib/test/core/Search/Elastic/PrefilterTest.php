<?php

use PHPUnit\Framework\TestCase;
use Search\Federated\UrlPrefixTransform;

class Search_Elastic_PrefilterTest extends PHPUnit\Framework\TestCase
{
	static $restorePrefs;

	public static function setUpBeforeClass(): void
	{
		global $prefs;

		static::$restorePrefs['unified_engine'] = $prefs['unified_engine'];
		static::$restorePrefs['unified_elastic_mysql_search_fallback'] = $prefs['unified_elastic_mysql_search_fallback'];
		static::$restorePrefs['unified_elastic_url'] = $prefs['unified_elastic_url'];
	}

	public static function tearDownAfterClass(): void
	{
		global $prefs;

		foreach(static::$restorePrefs as $pref => $value) {
			$prefs[$pref] = $value;
		}
	}

	/**
	 * Test Non-working ElasticSearch using MySQL fallback
	 * We need to ensure that the MySQL prefilter is working properly
	 * @throws Exception
	 */
	public function testPrefilterFallbackToMySQL()
	{
		global $prefs;

		$prefs['unified_engine'] = 'elastic';
		$prefs['unified_elastic_mysql_search_fallback'] = 'y';
		$prefs['unified_elastic_url'] = 'invalid-url';

		$unifiedsearchlib = TikiLib::lib('unifiedsearch');
		$datasource = $unifiedsearchlib->getDataSource();
		$callback = $datasource->getPrefilter();

		// Check if prefilter is checking for MYSQL related tokens (Fallback working properly)
		$this->assertEquals(['test'], $callback(['test'], ['test' => 'tokenqpqrqfqywauwauawtrxytazetaxzpfcr']));
		$this->assertEmpty($callback(['test'], ['test' => 'invalid_token']));
	}

	/**
	 * Test Non-working ElasticSearch without MySQL fallback
	 * We need to ensure that the prefilter is not set and there's an error message within
	 * @throws Exception
	 */
	public function testPrefilterWithoutFallback()
	{
		global $prefs;

		$prefs['unified_engine'] = 'elastic';
		$prefs['unified_elastic_mysql_search_fallback'] = 'n';
		$prefs['unified_elastic_url'] = 'invalid-url';

		$unifiedsearchlib = TikiLib::lib('unifiedsearch');
		$datasource = $unifiedsearchlib->getDataSource();
		$this->assertNull($datasource->getPrefilter());

		$feedbackMessages = [];

		foreach ($_SESSION['tikifeedback'] as $feedbackMessages) {
			$feedbackMessages[] = reset($feedbackMessages['mes']);
		}

		$this->assertContains(tr("The main search engine is not working properly and the fallback is also not set.<br>Search engine results might not be properly displayed."), $feedbackMessages);
	}
}
