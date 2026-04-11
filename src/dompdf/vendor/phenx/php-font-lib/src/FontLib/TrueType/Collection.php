<?php
/**
 * @package php-font-lib
 * @link    https://github.com/PhenX/php-font-lib
 * @author  Fabien Ménager <fabien.menager@gmail.com>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */

namespace FontLib\TrueType;

use Countable;
use FontLib\BinaryStream;
use Iterator;
use OutOfBoundsException;

/**
 * TrueType collection font file.
 *
 * @package php-font-lib
 */
class Collection extends BinaryStream implements Iterator, Countable
{

    /**
     * Current iterator position.
     *
     * @var integer
     */
    private $position = 0;

    protected $collectionOffsets = [];

    protected $collection = [];

    protected $version;

    protected $numFonts;


    function parse()
    {
        if (isset($this->numFonts)) {
            return;
        }

        $this->read(4);
        // tag name
        $this->version  = $this->readFixed();
        $this->numFonts = $this->readUInt32();

        for ($i = 0; $i < $this->numFonts; $i++) {
            $this->collectionOffsets[] = $this->readUInt32();
        }

    }//end parse()


    /**
     * @param int $fontId
     *
     * @throws OutOfBoundsException
     * @return File
     */
    function getFont($fontId)
    {
        $this->parse();

        if (!isset($this->collectionOffsets[$fontId])) {
            throw new OutOfBoundsException();
        }

        if (isset($this->collection[$fontId])) {
            return $this->collection[$fontId];
        }

        $font    = new File();
        $font->f = $this->f;
        $font->setTableOffset($this->collectionOffsets[$fontId]);

        return $this->collection[$fontId] = $font;

    }//end getFont()


    function current()
    {
        return $this->getFont($this->position);

    }//end current()


    function key()
    {
        return $this->position;

    }//end key()


    function next()
    {
        return ++$this->position;

    }//end next()


    function rewind()
    {
        $this->position = 0;

    }//end rewind()


    function valid()
    {
        $this->parse();

        return isset($this->collectionOffsets[$this->position]);

    }//end valid()


    function count()
    {
        $this->parse();

        return $this->numFonts;

    }//end count()


}//end class
