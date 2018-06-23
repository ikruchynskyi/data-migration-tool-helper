<?php

namespace Ecg\DataMigration\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Migration\Console\AbstractMigrateCommand;

class CheckIntegrityCommand extends AbstractMigrateCommand
{
    /**
     * @var \Ecg\DataMigration\Mode\Integrity
     */
    private $integrityMode;

    /**
     * @param \Migration\Config $config
     * @param \Migration\Logger\Manager $logManager
     * @param \Migration\App\Progress $progress
     * @param \Migration\Mode\Data $integrityMode
     */
    public function __construct(
        \Migration\Config $config,
        \Migration\Logger\Manager $logManager,
        \Migration\App\Progress $progress,
        \Ecg\DataMigration\Mode\Integrity $integrityMode
    ) {
        $this->integrityMode = $integrityMode;
        parent::__construct($config, $logManager, $progress);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->integrityMode->run();
    }

    /**
     * Initialization of the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('ecg:fix-integrity')->setDescription('Run data migration integrity check');
        parent::configure();
    }
}
