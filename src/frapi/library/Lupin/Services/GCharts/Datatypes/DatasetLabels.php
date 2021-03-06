<?php
/**
 * @author Echolibre ltd. 2009 <freedom@echolibre.com> 
 * @copyright Echolibre ltd. 2009
 */
class Lupin_Services_GCharts_Datatypes_DatasetLabels
{
    private $labels;
    
    public function __construct($labels)
    {
        $this->labels = $labels;
    }
    
    public function __toString()
    {
        return implode('|', $this->labels);
    }
    
}