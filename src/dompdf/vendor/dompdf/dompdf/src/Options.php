<?php
namespace Dompdf;

class Options
{

    /**
     * The root of your DOMPDF installation
     *
     * @var string
     */
    private $rootDir;

    /**
     * The location of a temporary directory.
     *
     * The directory specified must be writable by the webserver process.
     * The temporary directory is required to download remote images and when
     * using the PFDLib back end.
     *
     * @var string
     */
    private $tempDir;

    /**
     * The location of the DOMPDF font directory
     *
     * The location of the directory where DOMPDF will store fonts and font metrics
     * Note: This directory must exist and be writable by the webserver process.
     *
     * @var string
     */
    private $fontDir;

    /**
     * The location of the DOMPDF font cache directory
     *
     * This directory contains the cached font metrics for the fonts used by DOMPDF.
     * This directory can be the same as $fontDir
     *
     * Note: This directory must exist and be writable by the webserver process.
     *
     * @var string
     */
    private $fontCache;

    /**
     * dompdf's "chroot"
     *
     * Prevents dompdf from accessing system files or other files on the webserver.
     * All local files opened by dompdf must be in a subdirectory of this directory
     * or array of directories.
     * DO NOT set it to '/' since this could allow an attacker to use dompdf to
     * read any files on the server.  This should be an absolute path.
     *
     * ==== IMPORTANT ====
     * This setting may increase the risk of system exploit. Do not change
     * this settings without understanding the consequences. Additional
     * documentation is available on the dompdf wiki at:
     * https://github.com/dompdf/dompdf/wiki
     *
     * @var array
     */
    private $chroot;

    /**
     * @var string
     */
    private $logOutputFile;

    /**
     * html target media view which should be rendered into pdf.
     * List of types and parsing rules for future extensions:
     * http://www.w3.org/TR/REC-html40/types.html
     *   screen, tty, tv, projection, handheld, print, braille, aural, all
     * Note: aural is deprecated in CSS 2.1 because it is replaced by speech in CSS 3.
     * Note, even though the generated pdf file is intended for print output,
     * the desired content might be different (e.g. screen or projection view of html file).
     * Therefore allow specification of content here.
     *
     * @var string
     */
    private $defaultMediaType = "screen";

    /**
     * The default paper size.
     *
     * North America standard is "letter"; other countries generally "a4"
     *
     * @see \Dompdf\Adapter\CPDF::PAPER_SIZES for valid sizes
     *
     * @var string
     */
    private $defaultPaperSize = "letter";

    /**
     * The default paper orientation.
     *
     * The orientation of the page (portrait or landscape).
     *
     * @var string
     */
    private $defaultPaperOrientation = "portrait";

    /**
     * The default font family
     *
     * Used if no suitable fonts can be found. This must exist in the font folder.
     *
     * @var string
     */
    private $defaultFont = "serif";

    /**
     * Image DPI setting
     *
     * This setting determines the default DPI setting for images and fonts.  The
     * DPI may be overridden for inline images by explicitly setting the
     * image's width & height style attributes (i.e. if the image's native
     * width is 600 pixels and you specify the image's width as 72 points,
     * the image will have a DPI of 600 in the rendered PDF.  The DPI of
     * background images can not be overridden and is controlled entirely
     * via this parameter.
     *
     * For the purposes of DOMPDF, pixels per inch (PPI) = dots per inch (DPI).
     * If a size in html is given as px (or without unit as image size),
     * this tells the corresponding size in pt at 72 DPI.
     * This adjusts the relative sizes to be similar to the rendering of the
     * html page in a reference browser.
     *
     * In pdf, always 1 pt = 1/72 inch
     *
     * @var integer
     */
    private $dpi = 96;

    /**
     * A ratio applied to the fonts height to be more like browsers' line height
     *
     * @var float
     */
    private $fontHeightRatio = 1.1;

    /**
     * Enable embedded PHP
     *
     * If this setting is set to true then DOMPDF will automatically evaluate
     * embedded PHP contained within <script type="text/php"> ... </script> tags.
     *
     * ==== IMPORTANT ====
     * Enabling this for documents you do not trust (e.g. arbitrary remote html
     * pages) is a security risk. Embedded scripts are run with the same level of
     * system access available to dompdf. Set this option to false (recommended)
     * if you wish to process untrusted documents.
     *
     * This setting may increase the risk of system exploit. Do not change
     * this settings without understanding the consequences. Additional
     * documentation is available on the dompdf wiki at:
     * https://github.com/dompdf/dompdf/wiki
     *
     * @var boolean
     */
    private $isPhpEnabled = false;

    /**
     * Enable remote file access
     *
     * If this setting is set to true, DOMPDF will access remote sites for
     * images and CSS files as required.
     *
     * ==== IMPORTANT ====
     * This can be a security risk, in particular in combination with isPhpEnabled and
     * allowing remote html code to be passed to $dompdf = new DOMPDF(); $dompdf->load_html(...);
     * This allows anonymous users to download legally doubtful internet content which on
     * tracing back appears to being downloaded by your server, or allows malicious php code
     * in remote html pages to be executed by your server with your account privileges.
     *
     * This setting may increase the risk of system exploit. Do not change
     * this settings without understanding the consequences. Additional
     * documentation is available on the dompdf wiki at:
     * https://github.com/dompdf/dompdf/wiki
     *
     * @var boolean
     */
    private $isRemoteEnabled = false;

    /**
     * Enable inline Javascript
     *
     * If this setting is set to true then DOMPDF will automatically insert
     * JavaScript code contained within <script type="text/javascript"> ... </script> tags.
     *
     * @var boolean
     */
    private $isJavascriptEnabled = true;

    /**
     * Use the more-than-experimental HTML5 Lib parser
     *
     * @var boolean
     */
    private $isHtml5ParserEnabled = false;

    /**
     * Whether to enable font subsetting or not.
     *
     * @var boolean
     */
    private $isFontSubsettingEnabled = true;

    /**
     * @var boolean
     */
    private $debugPng = false;

    /**
     * @var boolean
     */
    private $debugKeepTemp = false;

    /**
     * @var boolean
     */
    private $debugCss = false;

    /**
     * @var boolean
     */
    private $debugLayout = false;

    /**
     * @var boolean
     */
    private $debugLayoutLines = true;

    /**
     * @var boolean
     */
    private $debugLayoutBlocks = true;

    /**
     * @var boolean
     */
    private $debugLayoutInline = true;

    /**
     * @var boolean
     */
    private $debugLayoutPaddingBox = true;

    /**
     * The PDF rendering backend to use
     *
     * Valid settings are 'PDFLib', 'CPDF', 'GD', and 'auto'. 'auto' will
     * look for PDFLib and use it if found, or if not it will fall back on
     * CPDF. 'GD' renders PDFs to graphic files. {@link Dompdf\CanvasFactory}
     * ultimately determines which rendering class to instantiate
     * based on this setting.
     *
     * @var string
     */
    private $pdfBackend = "CPDF";

    /**
     * PDFlib license key
     *
     * If you are using a licensed, commercial version of PDFlib, specify
     * your license key here.  If you are using PDFlib-Lite or are evaluating
     * the commercial version of PDFlib, comment out this setting.
     *
     * @link http://www.pdflib.com
     *
     * If pdflib present in web server and auto or selected explicitly above,
     * a real license code must exist!
     *
     * @var string
     */
    private $pdflibLicense = "";


    /**
     * @param array $attributes
     */
    public function __construct(array $attributes=null)
    {
        $rootDir = realpath(__DIR__."/../");
        $this->setChroot([$rootDir]);
        $this->setRootDir($rootDir);
        $this->setTempDir(sys_get_temp_dir());
        $this->setFontDir($rootDir."/lib/fonts");
        $this->setFontCache($this->getFontDir());
        $this->setLogOutputFile($this->getTempDir()."/log.htm");

        if (null !== $attributes) {
            $this->set($attributes);
        }

    }//end __construct()


    /**
     * @param  array|string $attributes
     * @param  null|mixed   $value
     * @return $this
     */
    public function set($attributes, $value=null)
    {
        if (!is_array($attributes)) {
            $attributes = [$attributes => $value];
        }

        foreach ($attributes as $key => $value) {
            if ($key === 'tempDir' || $key === 'temp_dir') {
                $this->setTempDir($value);
            } else if ($key === 'fontDir' || $key === 'font_dir') {
                $this->setFontDir($value);
            } else if ($key === 'fontCache' || $key === 'font_cache') {
                $this->setFontCache($value);
            } else if ($key === 'chroot') {
                $this->setChroot($value);
            } else if ($key === 'logOutputFile' || $key === 'log_output_file') {
                $this->setLogOutputFile($value);
            } else if ($key === 'defaultMediaType' || $key === 'default_media_type') {
                $this->setDefaultMediaType($value);
            } else if ($key === 'defaultPaperSize' || $key === 'default_paper_size') {
                $this->setDefaultPaperSize($value);
            } else if ($key === 'defaultPaperOrientation' || $key === 'default_paper_orientation') {
                $this->setDefaultPaperOrientation($value);
            } else if ($key === 'defaultFont' || $key === 'default_font') {
                $this->setDefaultFont($value);
            } else if ($key === 'dpi') {
                $this->setDpi($value);
            } else if ($key === 'fontHeightRatio' || $key === 'font_height_ratio') {
                $this->setFontHeightRatio($value);
            } else if ($key === 'isPhpEnabled' || $key === 'is_php_enabled' || $key === 'enable_php') {
                $this->setIsPhpEnabled($value);
            } else if ($key === 'isRemoteEnabled' || $key === 'is_remote_enabled' || $key === 'enable_remote') {
                $this->setIsRemoteEnabled($value);
            } else if ($key === 'isJavascriptEnabled' || $key === 'is_javascript_enabled' || $key === 'enable_javascript') {
                $this->setIsJavascriptEnabled($value);
            } else if ($key === 'isHtml5ParserEnabled' || $key === 'is_html5_parser_enabled' || $key === 'enable_html5_parser') {
                $this->setIsHtml5ParserEnabled($value);
            } else if ($key === 'isFontSubsettingEnabled' || $key === 'is_font_subsetting_enabled' || $key === 'enable_font_subsetting') {
                $this->setIsFontSubsettingEnabled($value);
            } else if ($key === 'debugPng' || $key === 'debug_png') {
                $this->setDebugPng($value);
            } else if ($key === 'debugKeepTemp' || $key === 'debug_keep_temp') {
                $this->setDebugKeepTemp($value);
            } else if ($key === 'debugCss' || $key === 'debug_css') {
                $this->setDebugCss($value);
            } else if ($key === 'debugLayout' || $key === 'debug_layout') {
                $this->setDebugLayout($value);
            } else if ($key === 'debugLayoutLines' || $key === 'debug_layout_lines') {
                $this->setDebugLayoutLines($value);
            } else if ($key === 'debugLayoutBlocks' || $key === 'debug_layout_blocks') {
                $this->setDebugLayoutBlocks($value);
            } else if ($key === 'debugLayoutInline' || $key === 'debug_layout_inline') {
                $this->setDebugLayoutInline($value);
            } else if ($key === 'debugLayoutPaddingBox' || $key === 'debug_layout_padding_box') {
                $this->setDebugLayoutPaddingBox($value);
            } else if ($key === 'pdfBackend' || $key === 'pdf_backend') {
                $this->setPdfBackend($value);
            } else if ($key === 'pdflibLicense' || $key === 'pdflib_license') {
                $this->setPdflibLicense($value);
            }//end if
        }//end foreach

        return $this;

    }//end set()


    /**
     * @param  string $key
     * @return mixed
     */
    public function get($key)
    {
        if ($key === 'tempDir' || $key === 'temp_dir') {
            return $this->getTempDir();
        } else if ($key === 'fontDir' || $key === 'font_dir') {
            return $this->getFontDir();
        } else if ($key === 'fontCache' || $key === 'font_cache') {
            return $this->getFontCache();
        } else if ($key === 'chroot') {
            return $this->getChroot();
        } else if ($key === 'logOutputFile' || $key === 'log_output_file') {
            return $this->getLogOutputFile();
        } else if ($key === 'defaultMediaType' || $key === 'default_media_type') {
            return $this->getDefaultMediaType();
        } else if ($key === 'defaultPaperSize' || $key === 'default_paper_size') {
            return $this->getDefaultPaperSize();
        } else if ($key === 'defaultPaperOrientation' || $key === 'default_paper_orientation') {
            return $this->getDefaultPaperOrientation();
        } else if ($key === 'defaultFont' || $key === 'default_font') {
            return $this->getDefaultFont();
        } else if ($key === 'dpi') {
            return $this->getDpi();
        } else if ($key === 'fontHeightRatio' || $key === 'font_height_ratio') {
            return $this->getFontHeightRatio();
        } else if ($key === 'isPhpEnabled' || $key === 'is_php_enabled' || $key === 'enable_php') {
            return $this->getIsPhpEnabled();
        } else if ($key === 'isRemoteEnabled' || $key === 'is_remote_enabled' || $key === 'enable_remote') {
            return $this->getIsRemoteEnabled();
        } else if ($key === 'isJavascriptEnabled' || $key === 'is_javascript_enabled' || $key === 'enable_javascript') {
            return $this->getIsJavascriptEnabled();
        } else if ($key === 'isHtml5ParserEnabled' || $key === 'is_html5_parser_enabled' || $key === 'enable_html5_parser') {
            return $this->getIsHtml5ParserEnabled();
        } else if ($key === 'isFontSubsettingEnabled' || $key === 'is_font_subsetting_enabled' || $key === 'enable_font_subsetting') {
            return $this->getIsFontSubsettingEnabled();
        } else if ($key === 'debugPng' || $key === 'debug_png') {
            return $this->getDebugPng();
        } else if ($key === 'debugKeepTemp' || $key === 'debug_keep_temp') {
            return $this->getDebugKeepTemp();
        } else if ($key === 'debugCss' || $key === 'debug_css') {
            return $this->getDebugCss();
        } else if ($key === 'debugLayout' || $key === 'debug_layout') {
            return $this->getDebugLayout();
        } else if ($key === 'debugLayoutLines' || $key === 'debug_layout_lines') {
            return $this->getDebugLayoutLines();
        } else if ($key === 'debugLayoutBlocks' || $key === 'debug_layout_blocks') {
            return $this->getDebugLayoutBlocks();
        } else if ($key === 'debugLayoutInline' || $key === 'debug_layout_inline') {
            return $this->getDebugLayoutInline();
        } else if ($key === 'debugLayoutPaddingBox' || $key === 'debug_layout_padding_box') {
            return $this->getDebugLayoutPaddingBox();
        } else if ($key === 'pdfBackend' || $key === 'pdf_backend') {
            return $this->getPdfBackend();
        } else if ($key === 'pdflibLicense' || $key === 'pdflib_license') {
            return $this->getPdflibLicense();
        }//end if

        return null;

    }//end get()


    /**
     * @param  string $pdfBackend
     * @return $this
     */
    public function setPdfBackend($pdfBackend)
    {
        $this->pdfBackend = $pdfBackend;
        return $this;

    }//end setPdfBackend()


    /**
     * @return string
     */
    public function getPdfBackend()
    {
        return $this->pdfBackend;

    }//end getPdfBackend()


    /**
     * @param  string $pdflibLicense
     * @return $this
     */
    public function setPdflibLicense($pdflibLicense)
    {
        $this->pdflibLicense = $pdflibLicense;
        return $this;

    }//end setPdflibLicense()


    /**
     * @return string
     */
    public function getPdflibLicense()
    {
        return $this->pdflibLicense;

    }//end getPdflibLicense()


    /**
     * @param  array|string $chroot
     * @return $this
     */
    public function setChroot($chroot, $delimiter=',')
    {
        if (is_string($chroot)) {
            $this->chroot = explode($delimiter, $chroot);
        } else if (is_array($chroot)) {
            $this->chroot = $chroot;
        }

        return $this;

    }//end setChroot()


    /**
     * @return array
     */
    public function getChroot()
    {
        $chroot = [];
        if (is_array($this->chroot)) {
            $chroot = $this->chroot;
        }

        return $chroot;

    }//end getChroot()


    /**
     * @param  boolean $debugCss
     * @return $this
     */
    public function setDebugCss($debugCss)
    {
        $this->debugCss = $debugCss;
        return $this;

    }//end setDebugCss()


    /**
     * @return boolean
     */
    public function getDebugCss()
    {
        return $this->debugCss;

    }//end getDebugCss()


    /**
     * @param  boolean $debugKeepTemp
     * @return $this
     */
    public function setDebugKeepTemp($debugKeepTemp)
    {
        $this->debugKeepTemp = $debugKeepTemp;
        return $this;

    }//end setDebugKeepTemp()


    /**
     * @return boolean
     */
    public function getDebugKeepTemp()
    {
        return $this->debugKeepTemp;

    }//end getDebugKeepTemp()


    /**
     * @param  boolean $debugLayout
     * @return $this
     */
    public function setDebugLayout($debugLayout)
    {
        $this->debugLayout = $debugLayout;
        return $this;

    }//end setDebugLayout()


    /**
     * @return boolean
     */
    public function getDebugLayout()
    {
        return $this->debugLayout;

    }//end getDebugLayout()


    /**
     * @param  boolean $debugLayoutBlocks
     * @return $this
     */
    public function setDebugLayoutBlocks($debugLayoutBlocks)
    {
        $this->debugLayoutBlocks = $debugLayoutBlocks;
        return $this;

    }//end setDebugLayoutBlocks()


    /**
     * @return boolean
     */
    public function getDebugLayoutBlocks()
    {
        return $this->debugLayoutBlocks;

    }//end getDebugLayoutBlocks()


    /**
     * @param  boolean $debugLayoutInline
     * @return $this
     */
    public function setDebugLayoutInline($debugLayoutInline)
    {
        $this->debugLayoutInline = $debugLayoutInline;
        return $this;

    }//end setDebugLayoutInline()


    /**
     * @return boolean
     */
    public function getDebugLayoutInline()
    {
        return $this->debugLayoutInline;

    }//end getDebugLayoutInline()


    /**
     * @param  boolean $debugLayoutLines
     * @return $this
     */
    public function setDebugLayoutLines($debugLayoutLines)
    {
        $this->debugLayoutLines = $debugLayoutLines;
        return $this;

    }//end setDebugLayoutLines()


    /**
     * @return boolean
     */
    public function getDebugLayoutLines()
    {
        return $this->debugLayoutLines;

    }//end getDebugLayoutLines()


    /**
     * @param  boolean $debugLayoutPaddingBox
     * @return $this
     */
    public function setDebugLayoutPaddingBox($debugLayoutPaddingBox)
    {
        $this->debugLayoutPaddingBox = $debugLayoutPaddingBox;
        return $this;

    }//end setDebugLayoutPaddingBox()


    /**
     * @return boolean
     */
    public function getDebugLayoutPaddingBox()
    {
        return $this->debugLayoutPaddingBox;

    }//end getDebugLayoutPaddingBox()


    /**
     * @param  boolean $debugPng
     * @return $this
     */
    public function setDebugPng($debugPng)
    {
        $this->debugPng = $debugPng;
        return $this;

    }//end setDebugPng()


    /**
     * @return boolean
     */
    public function getDebugPng()
    {
        return $this->debugPng;

    }//end getDebugPng()


    /**
     * @param  string $defaultFont
     * @return $this
     */
    public function setDefaultFont($defaultFont)
    {
        $this->defaultFont = $defaultFont;
        return $this;

    }//end setDefaultFont()


    /**
     * @return string
     */
    public function getDefaultFont()
    {
        return $this->defaultFont;

    }//end getDefaultFont()


    /**
     * @param  string $defaultMediaType
     * @return $this
     */
    public function setDefaultMediaType($defaultMediaType)
    {
        $this->defaultMediaType = $defaultMediaType;
        return $this;

    }//end setDefaultMediaType()


    /**
     * @return string
     */
    public function getDefaultMediaType()
    {
        return $this->defaultMediaType;

    }//end getDefaultMediaType()


    /**
     * @param  string $defaultPaperSize
     * @return $this
     */
    public function setDefaultPaperSize($defaultPaperSize)
    {
        $this->defaultPaperSize = $defaultPaperSize;
        return $this;

    }//end setDefaultPaperSize()


    /**
     * @param  string $defaultPaperOrientation
     * @return $this
     */
    public function setDefaultPaperOrientation($defaultPaperOrientation)
    {
        $this->defaultPaperOrientation = $defaultPaperOrientation;
        return $this;

    }//end setDefaultPaperOrientation()


    /**
     * @return string
     */
    public function getDefaultPaperSize()
    {
        return $this->defaultPaperSize;

    }//end getDefaultPaperSize()


    /**
     * @return string
     */
    public function getDefaultPaperOrientation()
    {
        return $this->defaultPaperOrientation;

    }//end getDefaultPaperOrientation()


    /**
     * @param  int $dpi
     * @return $this
     */
    public function setDpi($dpi)
    {
        $this->dpi = $dpi;
        return $this;

    }//end setDpi()


    /**
     * @return int
     */
    public function getDpi()
    {
        return $this->dpi;

    }//end getDpi()


    /**
     * @param  string $fontCache
     * @return $this
     */
    public function setFontCache($fontCache)
    {
        $this->fontCache = $fontCache;
        return $this;

    }//end setFontCache()


    /**
     * @return string
     */
    public function getFontCache()
    {
        return $this->fontCache;

    }//end getFontCache()


    /**
     * @param  string $fontDir
     * @return $this
     */
    public function setFontDir($fontDir)
    {
        $this->fontDir = $fontDir;
        return $this;

    }//end setFontDir()


    /**
     * @return string
     */
    public function getFontDir()
    {
        return $this->fontDir;

    }//end getFontDir()


    /**
     * @param  float $fontHeightRatio
     * @return $this
     */
    public function setFontHeightRatio($fontHeightRatio)
    {
        $this->fontHeightRatio = $fontHeightRatio;
        return $this;

    }//end setFontHeightRatio()


    /**
     * @return float
     */
    public function getFontHeightRatio()
    {
        return $this->fontHeightRatio;

    }//end getFontHeightRatio()


    /**
     * @param  boolean $isFontSubsettingEnabled
     * @return $this
     */
    public function setIsFontSubsettingEnabled($isFontSubsettingEnabled)
    {
        $this->isFontSubsettingEnabled = $isFontSubsettingEnabled;
        return $this;

    }//end setIsFontSubsettingEnabled()


    /**
     * @return boolean
     */
    public function getIsFontSubsettingEnabled()
    {
        return $this->isFontSubsettingEnabled;

    }//end getIsFontSubsettingEnabled()


    /**
     * @return boolean
     */
    public function isFontSubsettingEnabled()
    {
        return $this->getIsFontSubsettingEnabled();

    }//end isFontSubsettingEnabled()


    /**
     * @param  boolean $isHtml5ParserEnabled
     * @return $this
     */
    public function setIsHtml5ParserEnabled($isHtml5ParserEnabled)
    {
        $this->isHtml5ParserEnabled = $isHtml5ParserEnabled;
        return $this;

    }//end setIsHtml5ParserEnabled()


    /**
     * @return boolean
     */
    public function getIsHtml5ParserEnabled()
    {
        return $this->isHtml5ParserEnabled;

    }//end getIsHtml5ParserEnabled()


    /**
     * @return boolean
     */
    public function isHtml5ParserEnabled()
    {
        return $this->getIsHtml5ParserEnabled();

    }//end isHtml5ParserEnabled()


    /**
     * @param  boolean $isJavascriptEnabled
     * @return $this
     */
    public function setIsJavascriptEnabled($isJavascriptEnabled)
    {
        $this->isJavascriptEnabled = $isJavascriptEnabled;
        return $this;

    }//end setIsJavascriptEnabled()


    /**
     * @return boolean
     */
    public function getIsJavascriptEnabled()
    {
        return $this->isJavascriptEnabled;

    }//end getIsJavascriptEnabled()


    /**
     * @return boolean
     */
    public function isJavascriptEnabled()
    {
        return $this->getIsJavascriptEnabled();

    }//end isJavascriptEnabled()


    /**
     * @param  boolean $isPhpEnabled
     * @return $this
     */
    public function setIsPhpEnabled($isPhpEnabled)
    {
        $this->isPhpEnabled = $isPhpEnabled;
        return $this;

    }//end setIsPhpEnabled()


    /**
     * @return boolean
     */
    public function getIsPhpEnabled()
    {
        return $this->isPhpEnabled;

    }//end getIsPhpEnabled()


    /**
     * @return boolean
     */
    public function isPhpEnabled()
    {
        return $this->getIsPhpEnabled();

    }//end isPhpEnabled()


    /**
     * @param  boolean $isRemoteEnabled
     * @return $this
     */
    public function setIsRemoteEnabled($isRemoteEnabled)
    {
        $this->isRemoteEnabled = $isRemoteEnabled;
        return $this;

    }//end setIsRemoteEnabled()


    /**
     * @return boolean
     */
    public function getIsRemoteEnabled()
    {
        return $this->isRemoteEnabled;

    }//end getIsRemoteEnabled()


    /**
     * @return boolean
     */
    public function isRemoteEnabled()
    {
        return $this->getIsRemoteEnabled();

    }//end isRemoteEnabled()


    /**
     * @param  string $logOutputFile
     * @return $this
     */
    public function setLogOutputFile($logOutputFile)
    {
        $this->logOutputFile = $logOutputFile;
        return $this;

    }//end setLogOutputFile()


    /**
     * @return string
     */
    public function getLogOutputFile()
    {
        return $this->logOutputFile;

    }//end getLogOutputFile()


    /**
     * @param  string $tempDir
     * @return $this
     */
    public function setTempDir($tempDir)
    {
        $this->tempDir = $tempDir;
        return $this;

    }//end setTempDir()


    /**
     * @return string
     */
    public function getTempDir()
    {
        return $this->tempDir;

    }//end getTempDir()


    /**
     * @param  string $rootDir
     * @return $this
     */
    public function setRootDir($rootDir)
    {
        $this->rootDir = $rootDir;
        return $this;

    }//end setRootDir()


    /**
     * @return string
     */
    public function getRootDir()
    {
        return $this->rootDir;

    }//end getRootDir()


}//end class
