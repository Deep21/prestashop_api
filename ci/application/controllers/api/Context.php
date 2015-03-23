<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Example
 *
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array.
 *
 * @package   CodeIgniter
 * @subpackage  Rest Server
 * @category  Controller
 * @author    Phil Sturgeon
 * @link    http://philsturgeon.co.uk/code/
 */
class Context
{

    /**
     * @var Context
     */
    protected static $instance;

    /**
     * @var Cart
     */
    public $cart;

    /**
     * @var Customer
     */
    public $customer;

    /**
     * Get a singleton context
     *
     * @return Context
     */

    public function __construct()
    {

    }

    public static function getContext()
    {
        if (!isset(self::$instance)) self::$instance = new Context();
        return self::$instance;
    }
}
