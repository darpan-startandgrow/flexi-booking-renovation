<?php

namespace Sabberworm\CSS\Value;

use Sabberworm\CSS\OutputFormat;
use Sabberworm\CSS\Parsing\ParserState;
use Sabberworm\CSS\Parsing\SourceException;
use Sabberworm\CSS\Parsing\UnexpectedEOFException;
use Sabberworm\CSS\Parsing\UnexpectedTokenException;

class URL extends PrimitiveValue
{

    /**
     * @var CSSString
     */
    private $oURL;


    /**
     * @param int $iLineNo
     */
    public function __construct(CSSString $oURL, $iLineNo=0)
    {
        parent::__construct($iLineNo);
        $this->oURL = $oURL;

    }//end __construct()


    /**
     * @return URL
     *
     * @throws SourceException
     * @throws UnexpectedEOFException
     * @throws UnexpectedTokenException
     */
    public static function parse(ParserState $oParserState)
    {
        $bUseUrl = $oParserState->comes('url', true);
        if ($bUseUrl) {
            $oParserState->consume('url');
            $oParserState->consumeWhiteSpace();
            $oParserState->consume('(');
        }

        $oParserState->consumeWhiteSpace();
        $oResult = new URL(CSSString::parse($oParserState), $oParserState->currentLine());
        if ($bUseUrl) {
            $oParserState->consumeWhiteSpace();
            $oParserState->consume(')');
        }

        return $oResult;

    }//end parse()


    /**
     * @return void
     */
    public function setURL(CSSString $oURL)
    {
        $this->oURL = $oURL;

    }//end setURL()


    /**
     * @return CSSString
     */
    public function getURL()
    {
        return $this->oURL;

    }//end getURL()


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
        return "url({$this->oURL->render($oOutputFormat)})";

    }//end render()


}//end class
