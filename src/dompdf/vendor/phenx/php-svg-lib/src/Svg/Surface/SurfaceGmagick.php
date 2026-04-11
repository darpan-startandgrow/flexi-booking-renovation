<?php
/**
 * @package php-svg-lib
 * @link    http://github.com/PhenX/php-svg-lib
 * @author  Fabien Ménager <fabien.menager@gmail.com>
 * @license GNU LGPLv3+ http://www.gnu.org/copyleft/lesser.html
 */

namespace Svg\Surface;

use Svg\Style;

class SurfaceGmagick implements SurfaceInterface
{
    const DEBUG = false;

    /**
     * @var \GmagickDraw
     */
    private $canvas;

    private $width;

    private $height;

    /**
     * @var Style
     */
    private $style;


    public function __construct($w, $h)
    {
        if (self::DEBUG) {
            echo __FUNCTION__."\n";
        }

        $this->width  = $w;
        $this->height = $h;

        $canvas = new \GmagickDraw();

        $this->canvas = $canvas;

    }//end __construct()


    function out()
    {
        if (self::DEBUG) {
            echo __FUNCTION__."\n";
        }

        $image = new \Gmagick();
        $image->newimage($this->width, $this->height);
        $image->drawimage($this->canvas);

        $tmp = tempnam(sys_get_temp_dir(), "gm");

        $image->write($tmp);

        return file_get_contents($tmp);

    }//end out()


    public function save()
    {
        if (self::DEBUG) {
            echo __FUNCTION__."\n";
        }

        $this->canvas->save();

    }//end save()


    public function restore()
    {
        if (self::DEBUG) {
            echo __FUNCTION__."\n";
        }

        $this->canvas->restore();

    }//end restore()


    public function scale($x, $y)
    {
        if (self::DEBUG) {
            echo __FUNCTION__."\n";
        }

        $this->canvas->scale($x, $y);

    }//end scale()


    public function rotate($angle)
    {
        if (self::DEBUG) {
            echo __FUNCTION__."\n";
        }

        $this->canvas->rotate($angle);

    }//end rotate()


    public function translate($x, $y)
    {
        if (self::DEBUG) {
            echo __FUNCTION__."\n";
        }

        $this->canvas->translate($x, $y);

    }//end translate()


    public function transform($a, $b, $c, $d, $e, $f)
    {
        if (self::DEBUG) {
            echo __FUNCTION__."\n";
        }

        $this->canvas->concat($a, $b, $c, $d, $e, $f);

    }//end transform()


    public function beginPath()
    {
        if (self::DEBUG) {
            echo __FUNCTION__."\n";
        }

        // TODO: Implement beginPath() method.

    }//end beginPath()


    public function closePath()
    {
        if (self::DEBUG) {
            echo __FUNCTION__."\n";
        }

        $this->canvas->closepath();

    }//end closePath()


    public function fillStroke()
    {
        if (self::DEBUG) {
            echo __FUNCTION__."\n";
        }

        $this->canvas->fill_stroke();

    }//end fillStroke()


    public function clip()
    {
        if (self::DEBUG) {
            echo __FUNCTION__."\n";
        }

        $this->canvas->clip();

    }//end clip()


    public function fillText($text, $x, $y, $maxWidth=null)
    {
        if (self::DEBUG) {
            echo __FUNCTION__."\n";
        }

        $this->canvas->set_text_pos($x, $y);
        $this->canvas->show($text);

    }//end fillText()


    public function strokeText($text, $x, $y, $maxWidth=null)
    {
        if (self::DEBUG) {
            echo __FUNCTION__."\n";
        }

        // TODO: Implement drawImage() method.

    }//end strokeText()


    public function drawImage($image, $sx, $sy, $sw=null, $sh=null, $dx=null, $dy=null, $dw=null, $dh=null)
    {
        if (self::DEBUG) {
            echo __FUNCTION__."\n";
        }

        if (strpos($image, "data:") === 0) {
            $data = substr($image, (strpos($image, ";") + 1));
            if (strpos($data, "base64") === 0) {
                $data = base64_decode(substr($data, 7));
            }

            $image = tempnam(sys_get_temp_dir(), "svg");
            file_put_contents($image, $data);
        }

        $img = $this->canvas->load_image("auto", $image, "");

        $sy = ($sy - $sh);
        $this->canvas->fit_image($img, $sx, $sy, 'boxsize={'."$sw $sh".'} fitmethod=entire');

    }//end drawImage()


    public function lineTo($x, $y)
    {
        if (self::DEBUG) {
            echo __FUNCTION__."\n";
        }

        $this->canvas->lineto($x, $y);

    }//end lineTo()


    public function moveTo($x, $y)
    {
        if (self::DEBUG) {
            echo __FUNCTION__."\n";
        }

        $this->canvas->moveto($x, $y);

    }//end moveTo()


    public function quadraticCurveTo($cpx, $cpy, $x, $y)
    {
        if (self::DEBUG) {
            echo __FUNCTION__."\n";
        }

        // TODO: Implement quadraticCurveTo() method.

    }//end quadraticCurveTo()


    public function bezierCurveTo($cp1x, $cp1y, $cp2x, $cp2y, $x, $y)
    {
        if (self::DEBUG) {
            echo __FUNCTION__."\n";
        }

        $this->canvas->curveto($cp1x, $cp1y, $cp2x, $cp2y, $x, $y);

    }//end bezierCurveTo()


    public function arcTo($x1, $y1, $x2, $y2, $radius)
    {
        if (self::DEBUG) {
            echo __FUNCTION__."\n";
        }

    }//end arcTo()


    public function arc($x, $y, $radius, $startAngle, $endAngle, $anticlockwise=false)
    {
        if (self::DEBUG) {
            echo __FUNCTION__."\n";
        }

        $this->canvas->arc($x, $y, $radius, $startAngle, $endAngle);

    }//end arc()


    public function circle($x, $y, $radius)
    {
        if (self::DEBUG) {
            echo __FUNCTION__."\n";
        }

        $this->canvas->circle($x, $y, $radius);

    }//end circle()


    public function ellipse($x, $y, $radiusX, $radiusY, $rotation, $startAngle, $endAngle, $anticlockwise)
    {
        if (self::DEBUG) {
            echo __FUNCTION__."\n";
        }

        $this->canvas->ellipse($x, $y, $radiusX, $radiusY);

    }//end ellipse()


    public function fillRect($x, $y, $w, $h)
    {
        if (self::DEBUG) {
            echo __FUNCTION__."\n";
        }

        $this->rect($x, $y, $w, $h);
        $this->fill();

    }//end fillRect()


    public function rect($x, $y, $w, $h, $rx=0, $ry=0)
    {
        if (self::DEBUG) {
            echo __FUNCTION__."\n";
        }

        $this->canvas->rect($x, $y, $w, $h);

    }//end rect()


    public function fill()
    {
        if (self::DEBUG) {
            echo __FUNCTION__."\n";
        }

        $this->canvas->fill();

    }//end fill()


    public function strokeRect($x, $y, $w, $h)
    {
        if (self::DEBUG) {
            echo __FUNCTION__."\n";
        }

        $this->rect($x, $y, $w, $h);
        $this->stroke();

    }//end strokeRect()


    public function stroke()
    {
        if (self::DEBUG) {
            echo __FUNCTION__."\n";
        }

        $this->canvas->stroke();

    }//end stroke()


    public function endPath()
    {
        if (self::DEBUG) {
            echo __FUNCTION__."\n";
        }

        // $this->canvas->endPath();

    }//end endPath()


    public function measureText($text)
    {
        if (self::DEBUG) {
            echo __FUNCTION__."\n";
        }

        $style = $this->getStyle();
        $font  = $this->getFont($style->fontFamily, $style->fontStyle);

        return $this->canvas->stringwidth($text, $font, $this->getStyle()->fontSize);

    }//end measureText()


    public function getStyle()
    {
        if (self::DEBUG) {
            echo __FUNCTION__."\n";
        }

        return $this->style;

    }//end getStyle()


    public function setStyle(Style $style)
    {
        if (self::DEBUG) {
            echo __FUNCTION__."\n";
        }

        $this->style = $style;
        $canvas      = $this->canvas;

        if (is_array($style->stroke) && $stroke = $style->stroke) {
            $canvas->setcolor("stroke", "rgb", ($stroke[0] / 255), ($stroke[1] / 255), ($stroke[2] / 255), null);
        }

        if (is_array($style->fill) && $fill = $style->fill) {
            // $canvas->setcolor("fill", "rgb", $fill[0] / 255, $fill[1] / 255, $fill[2] / 255, null);
        }

        $opts = [];
        if ($style->strokeWidth > 0.000001) {
            $opts[] = "linewidth=$style->strokeWidth";
        }

        if (in_array($style->strokeLinecap, ["butt", "round", "projecting"])) {
            $opts[] = "linecap=$style->strokeLinecap";
        }

        if (in_array($style->strokeLinejoin, ["miter", "round", "bevel"])) {
            $opts[] = "linejoin=$style->strokeLinejoin";
        }

        $canvas->set_graphics_option(implode(" ", $opts));

        $font = $this->getFont($style->fontFamily, $style->fontStyle);
        $canvas->setfont($font, $style->fontSize);

    }//end setStyle()


    private function getFont($family, $style)
    {
        $map = [
            "serif"      => "Times",
            "sans-serif" => "Helvetica",
            "fantasy"    => "Symbol",
            "cursive"    => "serif",
            "monospance" => "Courier",
        ];

        $family = strtolower($family);
        if (isset($map[$family])) {
            $family = $map[$family];
        }

        return $this->canvas->load_font($family, "unicode", "fontstyle=$style");

    }//end getFont()


    public function setFont($family, $style, $weight)
    {
        // TODO: Implement setFont() method.

    }//end setFont()


}//end class
