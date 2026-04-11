<?php

namespace Sabberworm\CSS\Value;

use Sabberworm\CSS\OutputFormat;

class CSSFunction extends ValueList
{

    /**
     * @var string
     */
    protected $sName;


    /**
     * @param string                                                                                 $sName
     * @param RuleValueList|array<int, RuleValueList|CSSFunction|CSSString|LineName|Size|URL|string> $aArguments
     * @param string                                                                                 $sSeparator
     * @param int                                                                                    $iLineNo
     */
    public function __construct($sName, $aArguments, $sSeparator=',', $iLineNo=0)
    {
        if ($aArguments instanceof RuleValueList) {
            $sSeparator = $aArguments->getListSeparator();
            $aArguments = $aArguments->getListComponents();
        }

        $this->sName   = $sName;
        $this->iLineNo = $iLineNo;
        parent::__construct($aArguments, $sSeparator, $iLineNo);

    }//end __construct()


    /**
     * @return string
     */
    public function getName()
    {
        return $this->sName;

    }//end getName()


    /**
     * @param string $sName
     *
     * @return void
     */
    public function setName($sName)
    {
        $this->sName = $sName;

    }//end setName()


    /**
     * @return array<int, RuleValueList|CSSFunction|CSSString|LineName|Size|URL|string>
     */
    public function getArguments()
    {
        return $this->aComponents;

    }//end getArguments()


    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render(new OutputFormat());

    }//end __toString()


    /**
     * @return string
     */
    public function render(OutputFormat $oOutputFormat)
    {
        $aArguments = parent::render($oOutputFormat);
        return "{$this->sName}({$aArguments})";

    }//end render()


}//end class
