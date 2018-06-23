<?php

namespace Ecg\DataMigration\Resolvers;

abstract class AbstractResolver implements ResolverInterface
{
    const MIGRATION_TOOL = 'Magento_DataMigrationTool';

    /**
     * @var \Migration\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    protected $moduleReader;

    /**
     * @var \Magento\Framework\App\Arguments\ValidationState
     */
    protected $validationState;

    /**
     * @var
     */
    protected $errorData;

    /**
     * @var
     */
    protected $errorType;

    /**
     * @param \Migration\Config $config
     * @param \Magento\Framework\App\Arguments\ValidationState $validationState
     * @param $errorData
     */
    public function __construct(
        \Migration\Config $config,
        \Magento\Framework\App\Arguments\ValidationState $validationState,
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        $errorData,
        $errorType
    ) {
        $this->config = $config;
        $this->moduleReader = $moduleReader;
        $this->validationState = $validationState;
        $this->errorData = $errorData;
        $this->errorType = $errorType;
    }

    /**
     * @return mixed
     */
    abstract public function resolve();

    /**
     * @return array
     */
    protected function convertErrorData()
    {
        $data = explode(',', $this->errorData);
        return $data;
    }

    /**
     *
     */
    protected function getMigrationModuleLocation()
    {
        $etcDir = $this->moduleReader->getModuleDir(
            \Magento\Framework\Module\Dir::MODULE_ETC_DIR,
            self::MIGRATION_TOOL
        );
        return preg_replace('@(.*)etc@', '$1', $etcDir);
    }
}
