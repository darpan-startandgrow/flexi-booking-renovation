<?php

namespace Sabberworm\CSS\Value;

use Sabberworm\CSS\OutputFormat;
use Sabberworm\CSS\Parsing\ParserState;
use Sabberworm\CSS\Parsing\UnexpectedEOFException;
use Sabberworm\CSS\Parsing\UnexpectedTokenException;

class Size extends PrimitiveValue
{
    /**
     * vh/vw/vm(ax)/vmin/rem are absolute insofar as they don’t scale to the immediate parent (only the viewport)
     *
     * @var array<int, string>
     */
    const ABSOLUTE_SIZE_UNITS = [
        'px',
        'cm',
        'mm',
        'mozmm',
        'in',
        'pt',
        'pc',
        'vh',
        'vw',
        'vmin',
        'vmax',
        'rem',
    ];

    /**
     * @var array<int, string>
     */
    const RELATIVE_SIZE_UNITS = [
        '%',
        'em',
        'ex',
        'ch',
        'fr',
    ];

    /**
     * @var array<int, string>
     */
    const NON_SIZE_UNITS = [
        'deg',
        'grad',
        'rad',
        's',
        'ms',
        'turns',
        'Hz',
        'kHz',
    ];

    /**
     * @var array<int, array<string, string>>|null
     */
    private static $SIZE_UNITS = null;

    /**
     * @var float
     */
    private $fSize;

    /**
     * @var string|null
     */
    private $sUnit;

    /**
     * @var boolean
     */
    private $bIsColorComponent;


    /**
     * @param float|int|string $fSize
     * @param string|null      $sUnit
     * @param bool             $bIsColorComponent
     * @param int              $iLineNo
     */
    public function __construct($fSize, $sUnit=null, $bIsColorComponent=false, $iLineNo=0)
    {
        parent::__construct($iLineNo);
        $this->fSize = (float) $fSize;
        $this->sUnit = $sUnit;
        $this->bIsColorComponent = $bIsColorComponent;

    }//end __construct()


    /**
     * @param bool $bIsColorComponent
     *
     * @return Size
     *
     * @throws UnexpectedEOFException
     * @throws UnexpectedTokenException
     */
    public static function parse(ParserState $oParserState, $bIsColorComponent=false)
    {
        $sSize = '';
        if ($oParserState->comes('-')) {
            $sSize .= $oParserState->consume('-');
        }

        while (is_numeric($oParserState->peek()) || $oParserState->comes('.')) {
            if ($oParserState->comes('.')) {
                $sSize .= $oParserState->consume('.');
            } else {
                $sSize .= $oParserState->consume(1);
            }
        }

        $sUnit      = null;
        $aSizeUnits = self::getSizeUnits();
        foreach ($aSizeUnits as $iLength => &$aValues) {
            $sKey = strtolower($oParserState->peek($iLength));
            if (array_key_exists($sKey, $aValues)) {
                if (($sUnit = $aValues[$sKey]) !== null) {
                    $oParserState->consume($iLength);
                    break;
                }
            }
        }

        return new Size((float) $sSize, $sUnit, $bIsColorComponent, $oParserState->currentLine());

    }//end parse()


    /**
     * @return array<int, array<string, string>>
     */
    private static function getSizeUnits()
    {
        if (!is_array(self::$SIZE_UNITS)) {
            self::$SIZE_UNITS = [];
            foreach (array_merge(self::ABSOLUTE_SIZE_UNITS, self::RELATIVE_SIZE_UNITS, self::NON_SIZE_UNITS) as $val) {
                $iSize = strlen($val);
                if (!isset(self::$SIZE_UNITS[$iSize])) {
                    self::$SIZE_UNITS[$iSize] = [];
                }

                self::$SIZE_UNITS[$iSize][strtolower($val)] = $val;
            }

            krsort(self::$SIZE_UNITS, SORT_NUMERIC);
        }

        return self::$SIZE_UNITS;

    }//end getSizeUnits()


    /**
     * @param string $sUnit
     *
     * @return void
     */
    public function setUnit($sUnit)
    {
        $this->sUnit = $sUnit;

    }//end setUnit()


    /**
     * @return string|null
     */
    public function getUnit()
    {
        return $this->sUnit;

    }//end getUnit()


    /**
     * @param float|int|string $fSize
     */
    public function setSize($fSize)
    {
        $this->fSize = (float) $fSize;

    }//end setSize()


    /**
     * @return float
     */
    public function getSize()
    {
        return $this->fSize;

    }//end getSize()


    /**
     * @return bool
     */
    public function isColorComponent()
    {
        return $this->bIsColorComponent;

    }//end isColorComponent()


    /**
     * Returns whether the number stored in this Size really represents a size (as in a length of something on screen).
     *
     * @return false if the unit an angle, a duration, a frequency or the number is a component in a Color object.
     */
    public function isSize()
    {
        if (in_array($this->sUnit, self::NON_SIZE_UNITS, true)) {
            return false;
        }

        return !$this->isColorComponent();

    }//end isSize()


    /**
     * @return bool
     */
    public function isRelative()
    {
        if (in_array($this->sUnit, self::RELATIVE_SIZE_UNITS, true)) {
            return true;
        }

        if ($this->sUnit === null && $this->fSize != 0) {
            return true;
        }

        return false;

    }//end isRelative()


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
        $l      = localeconv();
        $sPoint = preg_quote($l['decimal_point'], '/');
        $sSize  = preg_match("/[\d\.]+e[+-]?\d+/i", (string) $this->fSize) ? preg_replace("/$sPoint?0+$/", "", sprintf("%f", $this->fSize)) : $this->fSize;
        return preg_replace(["/$sPoint/", "/^(-?)0\./"], ['.', '$1.'], $sSize).($this->sUnit === null ? '' : $this->sUnit);

    }//end render()


}//end class
