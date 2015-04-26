<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');


require_once APPPATH . '/libraries/REST_Controller.php';
require_once APPPATH . '/helpers/tracy/vendor/autoload.php';
use Tracy\Debugger;




/**
 * Cart
 * Definition de la classe Cart
 *
 * @package    CodeIgniter
 * @subpackage REST_Controller
 * @category   Cart
 * @author     Deeptha WICKREMA
 * @version    1.2
 */
class CartBase extends REST_Controller
{

    protected $cookie;


    protected $cart_model;


    /**
     * Default constructor
     */
    public function __construct()
    {

        Debugger::enable(Debugger::DETECT, 'C:\re\wamp\www\prestashop_test\ci\application\logs');
        Debugger::$strictMode = TRUE;
        Debugger::DEVELOPMENT;

        parent::__construct();
        //check if the id_cart is already in the order table
        $this->load->model('Cart_Model');
        $this->cart_model = $this->Cart_Model;
        $this->load->library('encrypt');
        $this->encrypt->set_cipher(MCRYPT_BLOWFISH);
        $this->load->helper('cookie');
        $this->cookie = json_decode($this->encrypt->decode(get_cookie('my_prestashop_ci')));


    }

    protected function addCart($auto_add = true)
    {

        $this->load->model('Guest_Model');
        //Si auto load vaut true
        if ($auto_add) {
            //On vérifie si il n'y a pas de cookie qui existe
            if ($this->cookie == null) {
                Debugger::log('cookie == null');
                //on créer un nouveau guest
                $id_guest = $this->Guest_Model->setNewGuest();
                $cart = array(
                    'id_cart' => null,
                    'id_shop_group' => 1,
                    'id_shop' => 1,
                    'id_address_delivery' => 0,
                    'id_address_invoice' => 0,
                    'id_currency' => 1,
                    'id_customer' => 0,
                    'id_guest' => $id_guest,
                    'id_lang' => 2,
                    'gift_message' => '',
                    'mobile_theme' => 0,
                    'secure_key' => '',
                    'delivery_option' => '',
                    'date_add' => date('Y-m-d H:i:s'),
                    'date_upd' => date('Y-m-d H:i:s'),
                );
                // Création d'un nouveau panier
                $id_cart = $this->cart_model->addCart($cart);

                $cookie_data = array(
                    'id_guest' => $id_guest,
                    'is_logged' => $this->oauth->getServer()->verifyResourceRequest(OAuth2\Request::createFromGlobals()),
                    "prestashop_config" => array(
                        'id_lang' => 1,
                        'id_currency' => 1,
                        'id_shop' => 1,
                        'id_shop' => 1,
                    ),
                    "customer" => array(
                        'nom' => null,
                        'prenom' => null,
                        'id_customer' => null,
                        "secure_key" => null,
                        'id_cart' => $id_cart
                    )
                );

                $encrypted_cookie = $this->encrypt->encode(json_encode($cookie_data));
                $cookie = array(
                    'name' => 'prestashop_ci',
                    'value' => $encrypted_cookie,
                    'path' => '/prestashop_test/',
                    'expire' => 3200, '', true
                );
                set_cookie($cookie);
                return $id_cart;
            }
        }

    }


}

