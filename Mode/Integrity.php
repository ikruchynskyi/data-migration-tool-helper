<?php

namespace Ecg\DataMigration\Mode;

use Migration\App\Mode\StepList;
use Migration\App\Progress;
use Migration\Logger\Logger;
use Migration\Exception;
use Migration\Config;
use Migration\Mode\AbstractMode;
use Migration\App\Mode\StepListFactory;

/**
 * Class Integrity
 * @package Ecg\DataMigration\Mode
 */
class Integrity extends AbstractMode implements \Migration\App\Mode\ModeInterface
{
    /**
     * @inheritdoc
     */
    protected $mode = ["settings", "data"];

    /**
     * @var array
     */
    protected $mapping = [
        'Source documents are not mapped' => \Ecg\DataMigration\Resolvers\DocumentsMapper::class,
        'Destination documents are not mapped' => \Ecg\DataMigration\Resolvers\DocumentsMapper::class,
        'Source fields are not mapped' => \Ecg\DataMigration\Resolvers\FieldsMapper::class,
        'Destination fields are not mapped' => \Ecg\DataMigration\Resolvers\FieldsMapper::class,
    ];

    /**
     * @param Progress $progress
     * @param Logger $logger
     * @param $stepListFactory
     * @param Config $configReader
     */
    public function __construct(
        Progress $progress,
        Logger $logger,
        StepListFactory $stepListFactory,
        Config $configReader
    ) {
        $this->stepListFactory = $stepListFactory;
        $this->logger = $logger;
        $this->progress = $progress;
        $this->configReader = $configReader;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        /** @var StepList $steps */
        $steps = $this->stepListFactory->create(['mode' => $this->mode]);
        $this->runIntegrity($steps);
        $this->logger->info('Checks are completed');
        return true;
    }

    /**
     * @param StepList $steps
     * @throws Exception
     * @return void
     */
    protected function runIntegrity(StepList $steps)
    {
        $result = true;
        $steps = $steps->getSteps();
        $this->mode = 'integrity';
        foreach ($steps as $stepName => $step) {
            if (!empty($step['integrity'])) {
                $result = $this->runStage($step['integrity'], $stepName, 'integrity check') && $result;
            }
        }

        $this->logger->addInfo('Trying to resolve integrity errors...');
        foreach ($this->getErrors() as $error) {
            /** @var \Ecg\DataMigration\Resolvers\ResolverInterface $resolver */
            $resolver = $this->getErrorResolver($error);
            if ($resolver) {
                $this->logger->addInfo('Executing ' . get_class($resolver));
                $resolver->resolve();
            }
        }
    }

    /**
     * @return mixed
     */
    private function getErrors()
    {
        if (array_key_exists(\Monolog\Logger::ERROR, $this->logger->getMessages())) {
            return $this->logger->getMessages()[\Monolog\Logger::ERROR];
        }
        return [];
    }

    /**
     * @param $error
     * @return mixed
     */
    private function getErrorResolver($error)
    {
        $errorType = preg_replace('@(.*?)[:|\.]\s.*@', '$1', $error);
        $errorData = preg_replace('@.*?[:|\.]\s(.*)@', '$1', $error);

        if (array_key_exists($errorType, $this->mapping)) {
            return $this->objectManager->create($this->mapping[$errorType], [
                'errorData' => $errorData,
                'errorType' => $errorType
            ]);
        }
        return null;
    }
}
