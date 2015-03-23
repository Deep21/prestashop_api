<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 *
 */
class Cart_Product_Model extends CI_Model
{
    private static $table = 'cart_product';
    public $id_cart;
    public $id_product;
    public $id_address_delivery;
    public $id_shop;
    public $id_product_attribute;
    public $quantity;
    public $date_add;

    public function __construct()
    {
        parent::__construct();
    }

    public function getCartProductById($id_cart)
    {
        return $this->db->get_where(self::$table, array('id_cart' => $id_cart))->result('Cart_Product_Model');
    }
}

?>