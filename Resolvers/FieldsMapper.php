<?php

namespace Ecg\DataMigration\Resolvers;

class FieldsMapper extends AbstractResolver implements ResolverInterface
{
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
        parent::__construct($config, $validationState, $moduleReader, $errorData, $errorType);
    }

    /**
     *
     */
    public function resolve()
    {
        $documentType = stripos($this->errorType, 'source') !== false ? 'source' : 'destination';
        $mapConfigPath = $this->getMigrationModuleLocation() . $this->config->getOption('map_file');
        $xml = new \SimpleXMLElement(file_get_contents($mapConfigPath));
        $documentRulesNode = $xml->xpath("/map/$documentType/field_rules")[0];
        $errorData = $this->convertErrorData();
        if (!empty($errorData)) {
            foreach ($errorData['fields'] as $fieldName)
            {
                $newIgnore = $documentRulesNode->addChild('ignore');
                $newIgnore->addChild('field', $errorData['document']. '.' . $fieldName);
            }

            $dom = new \DOMDocument("1.0");
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->loadXML($xml->asXML());
            file_put_contents($mapConfigPath, $dom->saveXML());
        }
    }

    /**
     * @return array
     */
    protected function convertErrorData()
    {
        preg_match('@Document:\s(.*?)\..*Fields:\s(.*)@', $this->errorData, $matches);
        return empty($matches) ? [] : ['document' => $matches[1], 'fields' => explode(',', $matches[2])];
    }
}
