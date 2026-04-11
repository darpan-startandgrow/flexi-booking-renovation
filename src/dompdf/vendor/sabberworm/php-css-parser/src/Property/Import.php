<?php

namespace Sabberworm\CSS\Property;

use Sabberworm\CSS\Comment\Comment;
use Sabberworm\CSS\OutputFormat;
use Sabberworm\CSS\Value\URL;

/**
 * Class representing an `@import` rule.
 */
class Import implements AtRule
{

    /**
     * @var URL
     */
    private $oLocation;

    /**
     * @var string
     */
    private $sMediaQuery;

    /**
     * @var integer
     */
    protected $iLineNo;

    /**
     * @var array<array-key, Comment>
     */
    protected $aComments;


    /**
     * @param URL    $oLocation
     * @param string $sMediaQuery
     * @param int    $iLineNo
     */
    public function __construct(URL $oLocation, $sMediaQuery, $iLineNo=0)
    {
        $this->oLocation   = $oLocation;
        $this->sMediaQuery = $sMediaQuery;
        $this->iLineNo     = $iLineNo;
        $this->aComments   = [];

    }//end __construct()


    /**
     * @return int
     */
    public function getLineNo()
    {
        return $this->iLineNo;

    }//end getLineNo()


    /**
     * @param URL $oLocation
     *
     * @return void
     */
    public function setLocation($oLocation)
    {
        $this->oLocation = $oLocation;

    }//end setLocation()


    /**
     * @return URL
     */
    public function getLocation()
    {
        return $this->oLocation;

    }//end getLocation()


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
        return "@import ".$this->oLocation->render($oOutputFormat).($this->sMediaQuery === null ? '' : ' '.$this->sMediaQuery).';';

    }//end render()


    /**
     * @return string
     */
    public function atRuleName()
    {
        return 'import';

    }//end atRuleName()


    /**
     * @return array<int, URL|string>
     */
    public function atRuleArgs()
    {
        $aResult = [$this->oLocation];
        if ($this->sMediaQuery) {
            array_push($aResult, $this->sMediaQuery);
        }

        return $aResult;

    }//end atRuleArgs()


    /**
     * @param array<array-key, Comment> $aComments
     *
     * @return void
     */
    public function addComments(array $aComments)
    {
        $this->aComments = array_merge($this->aComments, $aComments);

    }//end addComments()


    /**
     * @return array<array-key, Comment>
     */
    public function getComments()
    {
        return $this->aComments;

    }//end getComments()


    /**
     * @param array<array-key, Comment> $aComments
     *
     * @return void
     */
    public function setComments(array $aComments)
    {
        $this->aComments = $aComments;

    }//end setComments()


}//end class
