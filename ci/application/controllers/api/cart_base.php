<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');


require_once APPPATH . '/libraries/REST_Controller.php';

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
    protected  $cookie;

    /**
     * Default constructor
     * @return
     */
    public function __construct()
    {


        parent::__construct();
        $this->load->library('encrypt');
        $this->encrypt->set_cipher(MCRYPT_BLOWFISH);
        $this->load->helper('cookie');
        $this->cookie = json_decode($this->encrypt->decode(get_cookie('my_prestashop_ci')));
        if ($this->cookie == null) {
            $this->load->model('Guest_Model');
            $id_guest = $this->Guest_Model->setNewGuest();
            $cookie_data = array(
                'id_guest' => $id_guest,
                'is_logged' => true,
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
                    'id_cart' => null,
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
        }


    }

    protected function addCart()
    {

    }


}

