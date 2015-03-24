<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once('OAuth2/Autoloader.php');
require_once(dirname(__FILE__) . '/../../../config/settings.inc.php');

OAuth2\Autoloader::register();
use Tracy\Debugger;
Debugger::enable(Debugger::DETECT, 'C:\re\wamp\www\prestashop_test\ci\application\logs');
class Oauth
{
    private $server;
    private $ci_instance;
    private $storage;

    public function __construct()
    {

        $this->ci_instance = &get_instance();
        $session_expiration = $this->ci_instance->config->item('sess_expiration');

        //Custom PDO Class
        //Created to add custom table prefix
        $this->storage = new OAuth2\Storage\MyPdo(array('dsn' => 'mysql:dbname=' . $this->ci_instance->db->database . ';host=' . $this->ci_instance->db->hostname, 'username' => $this->ci_instance->db->username, 'password' => $this->ci_instance->db->password), array(), _DB_PREFIX_);

        // Prestashop DB contants

        $this->server = new OAuth2\Server($this->storage);

        $this->server->setConfig('access_lifetime', $session_expiration);
        $this->server->addGrantType(new OAuth2\GrantType\UserCredentials($this->storage));
        $this->server->addGrantType(new OAuth2\GrantType\RefreshToken($this->storage));
    }

    public function verifyResourceRequest()
    {
        if (!$this->server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $this->server->getResponse()->send();
            die;
        }
    }

    public function getAccessToken()
    {
        $this->ci_instance->load->model('Token_model');
        $tokenModel = $this->ci_instance->Token_model;
        $response = $this->server->handleTokenRequest(OAuth2\Request::createFromGlobals());
        $access_token = $response->getParameter('access_token');
        $customer = $tokenModel->getUserIdByToken($access_token);
        $this->_setCookie($customer);
        $response->send();
    }

    private function _setCookie($customer)
    {
        $id_cart = null;
        $this->ci_instance->load->model('Cart_Model');
        $cart = $this->ci_instance->Cart_Model;
        $this->ci_instance->load->library('encrypt');
        $this->ci_instance->load->helper('cookie');
        $this->ci_instance->encrypt->set_cipher(MCRYPT_BLOWFISH);
        try {
            //si un client existe on rentre dans la condition
            if (!empty($customer)) {
                //on vérifie si le client possède un id_cart
                //getLastNoneOrderedCart retourne un tableau contenant les informations du panier
                $cart_array = $cart->getLastNoneOrderedCart((int)$customer->id_customer);
                //Si aucun panier existe pour ce client on lui créer un id_cart vide
                if (empty($cart_array)) {
                    $cart->id_cart = null;
                    $cart->id_shop_group = 1;
                    $cart->id_shop = 1;
                    $cart->id_address_delivery = 0;
                    $cart->id_address_invoice = 0;
                    $cart->id_currency = 1;
                    $cart->id_customer = (int)$customer->id_customer;
                    $cart->id_guest = 0;
                    $cart->id_lang = 1;
                    $cart->gift_message = "";
                    $cart->mobile_theme = 0;
                    $cart->secure_key = $customer->secure_key;
                    $cart->delivery_option = "";
                    $cart->date_add = date('Y-m-d H:i:s');
                    $cart->date_upd = date('Y-m-d H:i:s');
                    $id_cart = $cart->addCart($cart);
                //Si un id_cart existe on le renvoie
                } else
                    $id_cart = (int)$cart_array['id_cart'];

                $cookie_data = array("prestashop_config" => array('id_lang' => 1, 'id_currency' => 1, 'id_shop' => 1,), "customer" => array('nom' => 'wick', 'prenom' => 'deep', 'id_customer' => (int)$customer->id_customer, "secure_key" => $customer->secure_key, 'id_cart' => $id_cart,));
                $cookie_encoded = json_encode($cookie_data);
                $encrypted_cookie = $this->ci_instance->encrypt->encode($cookie_encoded);
                $cookie = array('name' => 'prestashop_ci', 'value' => $encrypted_cookie, 'path' => '/prestashop/', 'expire' => 3200, '', true);
                set_cookie($cookie);
            } else {
                throw new Exception('Identification échoué / Client non trouvé');
            }
        } catch (Exception $e) {
            //Si une exception est attrapé on log l'erreur dans un fichier log
            Debugger::log($e->getMessage());
        }


    }
}

