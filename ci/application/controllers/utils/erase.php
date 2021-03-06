<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Erase extends CI_Controller
{
    
    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     *	- or -
     * 		http://example.com/index.php/welcome/index
     *	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    public function order() {
        $this->load->model('/util/Order_Erase_Model');
        $order_erase_model = $this->Order_Erase_Model;
        $order_erase_model->delete_all_linked_orders();
    }
    
    public function cart() {
        $this->load->model('/util/Cart_Erase_Model');
        $order_erase_model = $this->Cart_Erase_Model;
        $order_erase_model->delete_all_linked_cart();
    }
}

/* End of file welcome.php */

/* Location: ./application/controllers/welcome.php */
