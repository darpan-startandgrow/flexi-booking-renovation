<?php

namespace Sabberworm\CSS\RuleSet;

use Sabberworm\CSS\OutputFormat;
use Sabberworm\CSS\Property\AtRule;

/**
 * A RuleSet constructed by an unknown at-rule. `@font-face` rules are rendered into AtRuleSet objects.
 */
class AtRuleSet extends RuleSet implements AtRule
{

    /**
     * @var string
     */
    private $sType;

    /**
     * @var string
     */
    private $sArgs;


    /**
     * @param string $sType
     * @param string $sArgs
     * @param int    $iLineNo
     */
    public function __construct($sType, $sArgs='', $iLineNo=0)
    {
        parent::__construct($iLineNo);
        $this->sType = $sType;
        $this->sArgs = $sArgs;

    }//end __construct()


    /**
     * @return string
     */
    public function atRuleName()
    {
        return $this->sType;

    }//end atRuleName()


    /**
     * @return string
     */
    public function atRuleArgs()
    {
        return $this->sArgs;

    }//end atRuleArgs()


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
        $sArgs = $this->sArgs;
        if ($sArgs) {
            $sArgs = ' '.$sArgs;
        }

        $sResult  = "@{$this->sType}$sArgs{$oOutputFormat->spaceBeforeOpeningBrace()}{";
        $sResult .= parent::render($oOutputFormat);
        $sResult .= '}';
        return $sResult;

    }//end render()


}//end class
