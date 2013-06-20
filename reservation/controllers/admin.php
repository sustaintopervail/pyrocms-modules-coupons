<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Admin controller for the api module
 * 
 * @author 		PyroCMS Dev Team
 * @package 	Addons\SharedAddons\Modules\Reservation\Controllers
 */
class Admin extends Admin_Controller
{
    /**
     * @var array   Validation rules for reservation form
     */
    protected $validation_rules = array(
        'name' => array(
            'field' => 'name',
            'label' => 'Name',
            'rules' => 'trim|htmlspecialchars|required|max_length[100]'
        ),
        'email' => array(
            'field' => 'email',
            'label' => 'Email',
            'rules' => 'trim|required|email|max_length[100]'
        ),
        array(
            'field' => 'phone',
            'label' => 'Phone',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'date',
            'label' => 'Date',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'location',
            'label' => 'Location',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'no_of_hours',
            'label' => 'No. of Hours',
            'rules' => 'trim|required|numeric'
        ),
        array(
            'field' => 'additional_notes',
            'rules' => 'trim|htmlspecialchars'
        ),
        array(
            'field' => 'amount',
            'rules' => 'trim|numeric|required'
        )
    );
	public function __construct()
	{
		parent::__construct();
        $this->load->model('reservation_m');
		$this->load->language('reservation');
	}
	
	/**
	 * Index method
	 *
	 * @return void
	 */
	public function index()
	{


        // Create pagination links
        $total_rows = $this->reservation_m->count_by();
        $pagination = create_pagination('admin/reservation/index', $total_rows);

        // Using this data, get the relevant results
        $reservations = $this->reservation_m->limit($pagination['limit'])->get_many_by();

        //do we need to unset the layout because the request is ajax?
        $this->input->is_ajax_request() ? $this->template->set_layout(FALSE) : '';

        $this->template
            ->title($this->module_details['name'])
            //->append_js('admin/filter.js')
            ->set('pagination', $pagination)
            ->set('reservations', $reservations)
            ->build('admin/index');

	}

    /**
     * Edit Reservation
     *
     * @access public
     * @param int $id the ID of the reservation to edit
     * @return void
     */
    public function edit($id = 0)
    {
        $id OR redirect('admin/reservation');

        $reservation = $this->reservation_m->get($id);

        $this->form_validation->set_rules($this->validation_rules);

        if ($this->form_validation->run())
        {

            $inv_res = $this->generate_invoice($reservation);

            $result = $this->reservation_m->update($id, array(
                'name'				=> $this->input->post('name'),
                'email'				=> $this->input->post('email'),
                'phone'		        => $this->input->post('phone'),
                'event_date'	    => date('Y-m-d',strtotime($this->input->post('date'))),
                'event_time'	    => date('H:i:s',strtotime($this->input->post('date'))),
                'location'			=> $this->input->post('location'),
                'no_of_hours'       => $this->input->post('no_of_hours'),
                'additional_notes'	=> $this->input->post('additional_notes'),
                'amount'	        => $this->input->post('amount'),
                'is_invoiced'	    => true,
                'invoice_id'	    => $inv_res['invoice_id'],
                'invoice_url'	    => $inv_res['invoice_url'],
                'invoiced_on'	    => date("Y-m-d H:i:s"),
                'updated_on'		=> date("Y-m-d H:i:s"),

            ));

            if ($result)
            {
                $this->session->set_flashdata(array('success' => sprintf('Reservation Edited Successfully', $this->input->post('name'))));
            }
            else
            {
                $this->session->set_flashdata('error','There is an error while editing '.$this->input->post('name'));
            }

            // Redirect back to the form or main page
            redirect('admin/reservation');
        }

        // Go through all the known fields and get the post values
        foreach ($this->validation_rules as $key => $field)
        {
            if (isset($_POST[$field['field']]))
            {
                $reservation->$field['field'] = set_value($field['field']);
            }
        }

        $this->template
            ->title($this->module_details['name'], sprintf(lang('edit_title'), $reservation->name))
            ->append_css('module::jquery-ui-timepicker-addon.css')
            ->append_css('module::jquery-ui.css')
            ->append_css('module::reservation.css')
            ->append_js('module::jquery-ui-timepicker-addon.js')
            ->append_js('module::jquery-ui-sliderAccess.js')
            ->append_js('module::jquery.validate.js')
            ->append_js('module::reservation.js')
            ->set('reservation', $reservation)
            ->build('admin/edit');
    }

    /**
     * Delete Reservation
     * @access public
     * @param int $id the ID of the reservation to delete
     * @return void
     */
    public function delete($id = 0)
    {

        // Delete one
        $ids = ($id) ? array($id) : $this->input->post('action_to');

        // Go through the array of slugs to delete
        if ( ! empty($ids))
        {
            $reservation_names = array();
            $deleted_ids = array();
            foreach ($ids as $id)
            {
                // Get the current reservation so we can grab the id too
                if ($reservation = $this->reservation_m->get($id))
                {

                    if ($this->reservation_m->delete($id))
                    {
                        // Wipe cache for this model, the content has changed

                        $this->pyrocache->delete('reservation_m');
                        $reservation_names[] = $reservation->name;
                        $deleted_ids[] = $id;
                    }
                }
            }
        }

        // Some reservations have been deleted
        if ( ! empty($reservation))
        {
            // Only deleting one reservation
            if (count($reservation) == 1)
            {
                $this->session->set_flashdata('success', sprintf($this->lang->line('reservation_delete_success'), $reservation_names[0]));
            }
            // Deleting multiple reservations
            else
            {
                $this->session->set_flashdata('success', sprintf($this->lang->line('reservation_mass_delete_success'), implode('", "', $reservation_names)));
            }
        }
        // For some reason, none of them were deleted
        else
        {
            $this->session->set_flashdata('notice', lang('reservation_delete_error'));
        }

        redirect('admin/reservation');
    }

    /**
     * Helper method to determine what to do with selected items from form reservation
     * @access public
     * @return void
     */
    public function action()
    {
        switch ($this->input->post('btnAction'))
        {

            case 'delete':
                $this->delete();
                break;

            default:
                redirect('admin/reservation');
                break;
        }
    }

    private function generate_invoice($reservation){

        $this->config->load('paypal');
        $config = array(
            'mode' => $this->config->item('Sandbox'), 			// Sandbox / testing mode option.
            'API_Username' => $this->config->item('APIUsername'), 	// PayPal API username of the API caller
            'API_Password' => $this->config->item('APIPassword'), 	// PayPal API password of the API caller
            'Signature' => $this->config->item('Signature') 	// PayPal API signature of the API caller
        );


        if($config['mode'])
        {
            // Show Errors
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        }

        $this->load->library('PayPal_Invoice_API', $config);

        /**
         * Populate Data
         */
        $aryData['language'] = $this->config->item('Language');
        $aryData['merchantEmail'] =$this->config->item('Business');

        $aryData['payerEmail'] = $reservation->email;
        $aryData['currencyCode'] = $this->config->item('Currency');

        $aryData['orderId'] = strtotime('now');
        $aryData['paymentTerms'] = $this->config->item('PaymentTerms');

        $description = "Details\n\n";
        $description .= "Name: ".$reservation->name."\n";
        $description .= "Email: ".$reservation->email."\n";
        //$description .= "Phone: ".$reservation->phone."\n";
        $description .= "Location: ".$reservation->location."\n";
        $description .= "Event Date: ".$reservation->event_date."\n";
        $description .= "Event Time: ".$reservation->event_time."\n";
        $description .= "No. of Hours: ".$reservation->no_of_hours."\n";
        
        $aryData['invoiceNote'] = $reservation->additional_notes;
        // Removed, let's put description into item.


        $aryItems[0]['item_name'] =  $aryItems[0]['name'] = "Photo Booth Reservation";
        $aryItems[0]['item_description'] = $description;
        $aryItems[0]['date'] = date('Y-m-d H:i:s');//"2013-2-24T05:38:48Z";
        $aryItems[0]['item_quantity'] = $aryItems[0]['quantity'] = "1";
        $aryItems[0]['item_unitprice'] = $aryItems[0]['unitprice'] = $reservation->amount;;

        $res = $this->paypal_invoice_api->doCreateAndSendInvoice($aryData, $aryItems);
        if($res['responseEnvelope.ack']== "Success")
        {
            return array('status' => true,'invoice_id' => $res['invoiceID'],'invoice_url'=>$res['invoiceURL']);
        }
        else
        {
            // Return to Reservation Edit Page in case of error
            $this->session->set_flashdata('error',$this->paypal_invoice_api->formatErrorMessages($res));
            redirect('admin/reservation/edit/' . $reservation->id);

        }
    }

}

/* End of file admin.php */