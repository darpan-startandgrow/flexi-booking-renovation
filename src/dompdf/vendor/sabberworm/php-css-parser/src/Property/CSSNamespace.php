<?php

namespace Sabberworm\CSS\Property;

use Sabberworm\CSS\Comment\Comment;
use Sabberworm\CSS\OutputFormat;

/**
 * `CSSNamespace` represents an `@namespace` rule.
 */
class CSSNamespace implements AtRule
{

    /**
     * @var string
     */
    private $mUrl;

    /**
     * @var string
     */
    private $sPrefix;

    /**
     * @var integer
     */
    private $iLineNo;

    /**
     * @var array<array-key, Comment>
     */
    protected $aComments;


    /**
     * @param string      $mUrl
     * @param string|null $sPrefix
     * @param int         $iLineNo
     */
    public function __construct($mUrl, $sPrefix=null, $iLineNo=0)
    {
        $this->mUrl      = $mUrl;
        $this->sPrefix   = $sPrefix;
        $this->iLineNo   = $iLineNo;
        $this->aComments = [];

    }//end __construct()


    /**
     * @return int
     */
    public function getLineNo()
    {
        return $this->iLineNo;

    }//end getLineNo()


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
        return '@namespace '.($this->sPrefix === null ? '' : $this->sPrefix.' ').$this->mUrl->render($oOutputFormat).';';

    }//end render()


    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->mUrl;

    }//end getUrl()


    /**
     * @return string|null
     */
    public function getPrefix()
    {
        return $this->sPrefix;

    }//end getPrefix()


    /**
     * @param string $mUrl
     *
     * @return void
     */
    public function setUrl($mUrl)
    {
        $this->mUrl = $mUrl;

    }//end setUrl()


    /**
     * @param string $sPrefix
     *
     * @return void
     */
    public function setPrefix($sPrefix)
    {
        $this->sPrefix = $sPrefix;

    }//end setPrefix()


    /**
     * @return string
     */
    public function atRuleName()
    {
        return 'namespace';

    }//end atRuleName()


    /**
     * @return array<int, string>
     */
    public function atRuleArgs()
    {
        $aResult = [$this->mUrl];
        if ($this->sPrefix) {
            array_unshift($aResult, $this->sPrefix);
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
