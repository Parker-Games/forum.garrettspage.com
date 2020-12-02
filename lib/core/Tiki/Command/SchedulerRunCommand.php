<?php
// (c) Copyright by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Psr\Log\LogLevel;
use TikiInit;

class SchedulerRunCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('scheduler:run')
			->setDescription('Run scheduled tasks')
			->addOption(
				'skip-check-user',
				null,
				InputOption::VALUE_NONE,
				'Skip system user check that is running the scheduler'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		global $prefs;

		if ($prefs['feature_scheduler'] != 'y') {
			$output->writeln("<error>Scheduler feature is not enabled.</error>");
			exit(1);
		}

		$verbosityLevelMap = [
			LogLevel::ERROR => OutputInterface::OUTPUT_NORMAL,
			LogLevel::NOTICE => OutputInterface::OUTPUT_NORMAL,
			LogLevel::INFO   => OutputInterface::VERBOSITY_VERY_VERBOSE,
			LogLevel::DEBUG   => OutputInterface::VERBOSITY_DEBUG,
		];

		$logger = new ConsoleLogger($output, $verbosityLevelMap);
		$manager = new \Scheduler_Manager($logger);

		if (! $input->getOption('skip-check-user') && ! TikiInit::isWindows() && function_exists('posix_getuid')) {
			$manager->setHasTempFolderOwnership(posix_getuid() === fileowner(TIKI_PATH . '/temp/cache'));
		}

		$manager->run();
	}
}
