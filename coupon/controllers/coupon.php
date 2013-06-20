<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Public controller for the api module
 *
 * @author		Muhammad Faisal
 * @package		PyroCMS\Addons\Modules\Reservation\Controllers
 */
class Coupon extends Public_Controller
{
    /**
     * Constructor
     */
    public function __construct()
	{
		parent::__construct();
        $this->load->language('coupon');
        $this->load->model(array('coupon_m','files/file_folders_m'));
        $this->load->library('files/files');
	}

    /**
     * index controller
     * Loads coupons list
     */
    public function index()
	{
        $coupons = $this->coupon_m->get_many_by(array('is_expired' => 0));
        $this->template
            ->title($this->module_details['name'])
            ->append_css('module::coupon.css')
            ->append_js('module::coupon.js')
            //->append_js('module::jquery-print.js')
            ->set('coupons',$coupons)
            ->build('list');
	}

    /**
     * print_coupon controller
     * Loads coupon for print
     */
    public function print_coupon($id)
	{
        $this->template->set_layout(FALSE);
        $coupon = $this->coupon_m->get($id);
        $this->template
            ->title($this->module_details['name'])
            ->set('coupon',$coupon)
            ->build('print_coupon');
	}

    /**
     * coupon controller
     * Loads coupon
     */
    public function view($id)
	{

        $coupon = $this->coupon_m->get($id);
        $this->template
            ->title($this->module_details['name'])
            ->append_css('module::coupon.css')
            ->append_js('module::coupon.js')
            ->set('coupon',$coupon)
            ->build('coupon');
	}

}

/* End of file reservation.php */