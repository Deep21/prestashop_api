<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

/**
 * Customer
 * Definition de la classe Customer
 *
 * @package    CodeIgniter
 * @subpackage REST_Controller
 * @category   Customer
 * @author     Deeptha WICKREMA
 * @version    1.2
 */
class Customer extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('oauth');
        $this->load->library('session');
        $this->load->library('form_validation');
    }

    function addCustomer_post()
    {
        die("ef")
        if ($customer = $this->post()) {
            $_POST['email'] = $customer['email'];
            $_POST['firstname'] = $customer['firstname'];
            $_POST['lastname'] = $customer['lastname'];
            $_POST['pwd'] = $customer['pwd'];
            $_POST['pwdconfirmed'] = $customer['pwdconfirmed'];
            $_POST['gender'] = $customer['gender'];
            $_POST['birthday'] = $customer['birthday'];
        } else {
            $this->response(array('message' => "empty"), 404);
        }
        $this->_addCustomer();
        /*if($this->form_validation->run('customer_add')){ 

          $this->addCustomer();
        }
        else{
              $this->response(array('message'=>strip_tags(validation_errors())), 404);
        }*/

    }

    private function _addCustomer()
    {
        $this->load->model('Customer_Model');
        $now = date('Y-m-d H:i:s');
        $customer = $this->Customer_Model;
        $customer->email = $this->input->post('email');
        $customer->firstname = $this->input->post('firstname');
        $customer->lastname = $this->input->post('lastname');
        $customer->passwd = $this->input->post('pwd');
        $customer->id_gender = $this->input->post('gender');
        $customer->birthday = $this->input->post('birthday');
        $customer->secure_key = md5(uniqid(rand(), true));
        $customer->id_lang = 1;
        $customer->active = 1;
        $customer->date_add = $now;
        $customer->date_upd = $now;
        if ($customer->addCustomer($customer)) {
            $this->response(array('status' => 'Client ajouté avec succès', 'message' => 'OK'), 200);
        } else {
            $this->response(array('status' => 'error', 'message' => 'BAD'), 404);
        }
    }


    function getCustomer_get($id)
    {
        $this->load->library('auth');
        $this->load->model('Customer_Model');
        return $this->response($this->Customer_Model->getById($id), 404);

    }


    function editCustomer_put($id)
    {
        $this->load->library('server');

        $this->load->library('form_validation');
        $this->form_validation->set_rules('email', 'email', 'required|trim|xss_clean|valid_email');
        $customer = $this->put('customer_put');
        if ($customer) {
            foreach ($customer as $key => $value) {
                $_POST['email'] = $value['email'];
                $_POST['firstname'] = $value['firstname'];
                $_POST['lastname'] = $value['lastname'];
                $_POST['pwd'] = $value['pwd'];
                $_POST['pwdconfirmed'] = $value['pwdconfirmed'];
                $_POST['gender'] = $value['gender'];
                $_POST['birthday'] = $value['birthday'];
            }
        } else {
            $this->response(array('message' => "empty"), 404);
        }

        if ($this->form_validation->run('customer_put')) {
            $this->load->model('Customer_Model');
            $customer = $this->Customer_Model;
            $customer->email = $value['email'];
            $customer->firstname = $value['firstname'];
            $customer->lastname = $value['lastname'];
            $customer->passwd = $this->cryptedpwd;
            $customer->id_gender = $value['gender'];
            $customer->birthday = $value['birthday'];
            $customer->id_lang = 1;
            if ($customer->updateCustomerById($customer, $id)) {
                $this->response(array('status' => 'Client modifié avec succès'), 200);
            } else {
                $this->response(array('status' => 'erreur impossible de modifier'), 404);
            }
        } else {
            $this->response(array('message' => strip_tags(validation_errors())), 404);
        }


    }


}