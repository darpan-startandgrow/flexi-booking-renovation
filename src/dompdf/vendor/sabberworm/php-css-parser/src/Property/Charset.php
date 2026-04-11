<?php

namespace Sabberworm\CSS\Property;

use Sabberworm\CSS\Comment\Comment;
use Sabberworm\CSS\OutputFormat;

/**
 * Class representing an `@charset` rule.
 *
 * The following restrictions apply:
 * - May not be found in any CSSList other than the Document.
 * - May only appear at the very top of a Document’s contents.
 * - Must not appear more than once.
 */
class Charset implements AtRule
{

    /**
     * @var string
     */
    private $sCharset;

    /**
     * @var integer
     */
    protected $iLineNo;

    /**
     * @var array<array-key, Comment>
     */
    protected $aComments;


    /**
     * @param string $sCharset
     * @param int    $iLineNo
     */
    public function __construct($sCharset, $iLineNo=0)
    {
        $this->sCharset  = $sCharset;
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
     * @param string $sCharset
     *
     * @return void
     */
    public function setCharset($sCharset)
    {
        $this->sCharset = $sCharset;

    }//end setCharset()


    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->sCharset;

    }//end getCharset()


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
        return "@charset {$this->sCharset->render($oOutputFormat)};";

    }//end render()


    /**
     * @return string
     */
    public function atRuleName()
    {
        return 'charset';

    }//end atRuleName()


    /**
     * @return string
     */
    public function atRuleArgs()
    {
        return $this->sCharset;

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
