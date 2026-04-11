<?php

namespace Sabberworm\CSS\Comment;

use Sabberworm\CSS\OutputFormat;
use Sabberworm\CSS\Renderable;

class Comment implements Renderable
{

    /**
     * @var integer
     */
    protected $iLineNo;

    /**
     * @var string
     */
    protected $sComment;


    /**
     * @param string $sComment
     * @param int    $iLineNo
     */
    public function __construct($sComment='', $iLineNo=0)
    {
        $this->sComment = $sComment;
        $this->iLineNo  = $iLineNo;

    }//end __construct()


    /**
     * @return string
     */
    public function getComment()
    {
        return $this->sComment;

    }//end getComment()


    /**
     * @return int
     */
    public function getLineNo()
    {
        return $this->iLineNo;

    }//end getLineNo()


    /**
     * @param string $sComment
     *
     * @return void
     */
    public function setComment($sComment)
    {
        $this->sComment = $sComment;

    }//end setComment()


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
        return '/*'.$this->sComment.'*/';

    }//end render()


}//end class
