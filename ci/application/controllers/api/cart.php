<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

/**
 * Order
 * Definition de la classe Order
 *
 * @package    CodeIgniter
 * @subpackage REST_Controller
 * @category   Order
 * @author     Deeptha WICKREMA
 * @version    1.2
 */
class Cart extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('encrypt');
        $this->encrypt->set_cipher(MCRYPT_BLOWFISH);
        $this->load->helper('cookie');
        $this->cookie = json_decode($this->encrypt->decode(get_cookie('my_prestashop_ci')));
        $this->load->model('Cart_Model');
        $this->load->library('oauth');

        //$this->load->library('server');
        //$this->server->verifyResourceRequest();


    }

    /**
     * Retrieve Order by order id
     * @param $id_order the id of order
     * @return
     */
    public function getLastNoneOrderedCart_get($id_customer = null)
    {
        $cart = $this->_getLastNoneOrderedCar($id_customer);
        if ($cart == null) $this->response(array('id_cart' => null), 200);

        $this->response(array('id_cart' => (int)$cart->id_cart), 200);
    }

    private function _getLastNoneOrderedCar($id_customer)
    {
        $this->load->model('Cart_Model');

        if ($this->Cart_Model->getLastNoneOrderedCart($id_customer) != null) return $this->Cart_Model->getLastNoneOrderedCart($id_customer);
    }

    public function addCartFirstTime_post()
    {
        $cart_new = array();
        if ($cart = $this->post("cart")) {
            $this->load->model('Cart_Model');
            $cart['date_add'] = date('Y-m-d H:i:s');
            $cart['date_upd'] = date('Y-m-d H:i:s');
            $cart_new = array('id_cart' => $cart['id_cart'], 'id_shop_group' => $cart['id_shop_group'], 'id_shop' => $cart['id_shop'], 'id_carrier' => $cart['id_carrier'], 'delivery_option' => $cart['delivery_option'], 'id_lang' => $cart['id_lang'], 'id_address_delivery' => $cart['id_address_delivery'], 'id_address_invoice' => $cart['id_address_invoice'], 'id_currency' => $cart['id_currency'], 'id_customer' => $cart['id_customer'], 'id_guest' => $cart['id_guest'], 'secure_key' => $cart['secure_key'], 'gift' => $cart['gift'], 'gift_message' => $cart['gift_message'], 'mobile_theme' => $cart['mobile_theme'], 'allow_seperated_package' => $cart['allow_seperated_package'], 'date_add' => $cart['date_add'], 'date_upd' => $cart['date_upd']);

            if ($id_cart = $this->Cart_Model->addCart($cart_new)) {
                $product = array_merge(array('id_cart' => $id_cart), $cart['product']);
                $id = $this->Cart_Model->addProductToCart($product);
                $this->response(array('cart' => $id_cart), 202);
            }
        } else {
            die('nardine');
        }
    }

    public function createCart_post()
    {
        $cart_new = array();
        if ($cart = $this->post("cart")) {
            $this->load->model('Cart_Model');
            $cart['date_add'] = date('Y-m-d H:i:s');
            $cart['date_upd'] = date('Y-m-d H:i:s');
            $cart_new = array('id_cart' => $cart['id_cart'], 'id_shop_group' => $cart['id_shop_group'], 'id_shop' => $cart['id_shop'], 'id_carrier' => $cart['id_carrier'], 'delivery_option' => $cart['delivery_option'], 'id_lang' => $cart['id_lang'], 'id_address_delivery' => $cart['id_address_delivery'], 'id_address_invoice' => $cart['id_address_invoice'], 'id_currency' => $cart['id_currency'], 'id_customer' => $cart['id_customer'], 'id_guest' => $cart['id_guest'], 'secure_key' => $cart['secure_key'], 'gift' => $cart['gift'], 'gift_message' => $cart['gift_message'], 'mobile_theme' => $cart['mobile_theme'], 'allow_seperated_package' => $cart['allow_seperated_package'], 'date_add' => $cart['date_add'], 'date_upd' => $cart['date_upd']);
        }
        if ($id_cart = $this->Cart_Model->addCart($cart_new)) {
            $this->response(array('cart' => $id_cart), 202);
        }
    }

    public function insertProductToCartById_post()
    {
        $cart = $this->post();
        $id_cart = null;
        if (!empty($cart)) {

            if (isset($this->cookie) && !empty($this->cookie)) {
                $cart_array = $this->Cart_Model->getLastNoneOrderedCart((int)$this->cookie->customer->id_customer);
                if (empty($cart_array)) {
                    $this->Cart_Model->id_cart = null;
                    $this->Cart_Model->id_shop_group = 1;
                    $this->Cart_Model->id_shop = 1;
                    $this->Cart_Model->id_address_delivery = 0;
                    $this->Cart_Model->id_address_invoice = 0;
                    $this->Cart_Model->id_currency = 1;
                    $this->Cart_Model->id_customer = (int)$this->cookie->customer->id_customer;
                    $this->Cart_Model->id_guest = 0;
                    $this->Cart_Model->id_lang = 1;
                    $this->Cart_Model->gift_message = "";
                    $this->Cart_Model->mobile_theme = 0;
                    $this->Cart_Model->secure_key = $this->cookie->customer->secure_key;
                    $this->Cart_Model->delivery_option = "";
                    $this->Cart_Model->date_add = date('Y-m-d H:i:s');
                    $this->Cart_Model->date_upd = date('Y-m-d H:i:s');
                    $id_cart = $this->Cart_Model->addCart($this->Cart_Model);
                    $this->cookie->customer->id_cart = $id_cart;
                    $cookie_encoded = json_encode($this->cookie);
                    $encrypted_cookie = $this->encrypt->encode($cookie_encoded);
                    $cookie = array('name' => 'prestashop_ci', 'value' => $encrypted_cookie, 'path' => '/prestashop/', 'expire' => 3200, '', true);
                    set_cookie($cookie);
                }
            }


            $this->load->model('Cart_Model');
            $id_product = (int)$cart['id_product'];
            $id_product_attribute = (int)$cart['id_product_attribute'];
            $clientQty = (int)$cart['quantity'];

            //load quantity of the cart
            //It may be empty or full
            $cartProductQty = $this->Cart_Model->containsProduct($id_product, $id_product_attribute, $id_cart);

            //Load the number of aviable stock product
            $stock = $this->Cart_Model->getStockById(1, $id_product_attribute, $id_product);

            $cart = $this->input->get('cart');

            if (empty($cart)) {
                $this->response(array('cart' => 'Error', 'message' => 'no parameter'), 404);
                exit;
            }

            if ($cartProductQty == 0 && $this->input->get('cart') === 'up' && !empty($stock)) {

                if ($clientQty > (int)$stock->quantity) {
                    $this->response(array('http_code' => 404, 'error' => true, 'create' => false, 'updated' => false, 'deleted' => false, 'out_of_stock' => true), 404);
                    exit;
                } elseif ($response = $this->Cart_Model->insertProductToCart($id_product, $id_product_attribute, $id_cart, $clientQty)) {
                    $this->response(array('http_code' => 202, 'error' => false, 'create' => true, 'updated' => false, 'deleted' => false, 'message' => 'Product added to the cart'), 202);
                }
            } elseif ($cartProductQty > 0 && ((int)$stock->quantity) >= ($cartProductQty + $clientQty) && $this->input->get('cart') === 'up') {
                $clientQtyUp = '+ ' . (int)$clientQty;
                $this->Cart_Model->updateQty($id_cart, $id_product_attribute, $id_product, $clientQtyUp);
                $this->response(array('http_code' => 202, 'error' => false, 'create' => false, 'updated' => true, 'deleted' => false, 'message' => 'Quantity updated'), 202);
                exit;
            }

            if ($clientQty <= $cartProductQty && $this->input->get('cart') === 'down') {

                if (((int)$cartProductQty) - (int)$clientQty == 0) {
                    $this->Cart_Model->deleteCartProduct($id_cart, $id_product, $id_product_attribute, 0);
                    $this->response(array('http_code' => 202, 'error' => false, 'create' => false, 'updated' => false, 'deleted' => true, 'message' => 'Product deleted'), 200);
                } else {
                    $clientQtyDown = '- ' . (int)$clientQty;
                    $this->Cart_Model->updateQty($id_cart, $id_product_attribute, $id_product, $clientQtyDown);
                    $this->response(array('http_code' => 202, 'error' => false, 'create' => false, 'updated' => true, 'deleted' => false, 'message' => 'Qantity removed'), 200);
                }
            } elseif ($clientQty > $cartProductQty && $this->input->get('cart') === 'down') {
                $this->response(array('http_code' => 404, 'error' => true, 'create' => false, 'updated' => false, 'deleted' => false, 'out_of_stock' => true), 404);
                exit;
            }

            if (($cartProductQty + $clientQty) > (int)$stock->quantity) {
                $this->response(array('http_code' => 404, 'error' => true, 'create' => false, 'updated' => false, 'deleted' => false, 'out_of_stock' => true), 404);
                exit;
            }
        } else {
            $this->response(array('http_code' => 404, 'error' => true, 'create' => false, 'updated' => false, 'deleted' => false, 'message' => 'Empty'), 404);
            exit;
        }
    }

    public function deleteCartProduct_delete($id_cart, $id_product, $id_product_attribute, $id_address_delivery)
    {
        $this->load->model('Cart_Model');
        if ($id_cart = $this->Cart_Model->deleteCartProduct($id_cart, $id_product, $id_product_attribute, $id_address_delivery)) {
            $this->response(array('cart' => $id_cart), 204);
        }
    }

    public function updateProductCartclientQty_post($id_cart, $id_product, $id_product_attribute)
    {

        /*if($cart = $this->put('cart')){
        $this->load->model('Cart_Model');
        $this->Cart_Model->updateclientQty($id_cart, $cart['product']);
        $this->response(array('cart'=>$id_cart), 202);
        }*/
        $this->load->model('Cart_Model');
        if ($cartProductQty = $this->Cart_Model->containsProduct($id_product, $id_product_attribute, $id_cart) > 0) {
            $stock = $this->Cart_Model->getStockById(1, $id_product_attribute, $id_product);
            $affectedRow = $this->Cart_Model->updateQty($id_cart, $id_product_attribute, $id_product);
            $this->response(array('cart' => $affectedRow), 202);
        } else {
            $this->response(array('cart' => 0), 202);
        }
    }

    public function getLastCartProductByCustomer_get($id_customer = null)
    {
        $cart = $this->_getLastNoneOrderedCar($id_customer);
        $this->getProductByCartId_get(current($cart)->id_cart);
    }

    public function getProductByCartId_get($id_cart = null)
    {
        $this->load->library('server');
        $this->server->verifyResourceRequest();
        $this->load->model('Product_Model');
        $products = $this->Product_Model->getProductByCartId($id_cart);
        $cart_array = array();
        $product_array = array();
        foreach ($products as $key => $product) {
            $product_array[] = array('id_product' => (int)$product->id_product, 'id_cart' => (int)$product->id_cart, 'id_address_delivery' => (int)$product->id_address_delivery, 'quantity' => (int)$product->cart_quantity, 'id_shop' => (int)$product->id_shop, 'libelle_produit' => $product->name, 'id_product' => (int)$product->id_product, 'width' => (double)$product->width, 'height' => (double)$product->height, 'depth' => (double)$product->depth, 'id_supplier' => (int)$product->id_supplier, 'id_manufacturer' => (int)$product->id_manufacturer, 'is_virtual' => (int)$product->is_virtual, 'description_short' => $product->description_short, 'available_now' => $product->available_now, 'available_later' => $product->available_later);
        }
        if ($products != null) $this->response($product_array, 200);
    }
}

?>