<?php

namespace Ecg\DataMigration\Resolvers;

class DocumentsMapper extends AbstractResolver implements ResolverInterface
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
        $documentRulesNode = $xml->xpath("/map/$documentType/document_rules")[0];
        $errorData = $this->convertErrorData();
        foreach ($errorData as $tableName)
        {
            $newIgnore = $documentRulesNode->addChild('ignore');
            $newIgnore->addChild('document', $tableName);
        }

        $dom = new \DOMDocument("1.0");
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());
        file_put_contents($mapConfigPath, $dom->saveXML());
    }
}
