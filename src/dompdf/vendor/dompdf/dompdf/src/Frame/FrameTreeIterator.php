<?php
namespace Dompdf\Frame;

use Iterator;
use Dompdf\Frame;

/**
 * Pre-order Iterator
 *
 * Returns frames in preorder traversal order (parent then children)
 *
 * @access  private
 * @package dompdf
 */
class FrameTreeIterator implements Iterator
{

    /**
     * @var Frame
     */
    protected $_root;

    /**
     * @var array
     */
    protected $_stack = [];

    /**
     * @var integer
     */
    protected $_num;


    /**
     * @param Frame $root
     */
    public function __construct(Frame $root)
    {
        $this->_stack[] = $this->_root = $root;
        $this->_num     = 0;

    }//end __construct()


    /**
     *
     */
    public function rewind()
    {
        $this->_stack = [$this->_root];
        $this->_num   = 0;

    }//end rewind()


    /**
     * @return bool
     */
    public function valid()
    {
        return count($this->_stack) > 0;

    }//end valid()


    /**
     * @return int
     */
    public function key()
    {
        return $this->_num;

    }//end key()


    /**
     * @return Frame
     */
    public function current()
    {
        return end($this->_stack);

    }//end current()


    /**
     * @return Frame
     */
    public function next()
    {
        $b = end($this->_stack);

        // Pop last element
        unset($this->_stack[key($this->_stack)]);
        $this->_num++;

        // Push all children onto the stack in reverse order
        if ($c = $b->get_last_child()) {
            $this->_stack[] = $c;
            while ($c = $c->get_prev_sibling()) {
                $this->_stack[] = $c;
            }
        }

        return $b;

    }//end next()


}//end class
