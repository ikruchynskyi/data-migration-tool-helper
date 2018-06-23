<?php
namespace Ecg\DataMigration\Mode;

/**
 * Class AbstractMode
 */
class StepList extends \Migration\App\Mode\StepList
{
    /**
     * @return array
     */
    public function getSteps()
    {
        if (!$this->data) {
            $steps = [];
            if (is_array($this->mode)) {
                foreach ($this->mode as $singleMode) {
                    $steps = array_merge($steps, $this->config->getSteps($singleMode));
                }
            } else {
                $steps = $this->config->getSteps($this->mode);
            }
            $this->data = $steps;
        }
        $this->createInstances();
        return $this->data;
    }
}
