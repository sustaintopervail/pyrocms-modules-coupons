<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Public controller for the api module
 *
 * @author		Muhammad Faisal
 * @package		PyroCMS\Addons\Modules\Reservation\Controllers
 */
class Reservation extends Public_Controller
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
        )
    );

    /**
     * Constructor
     */
    public function __construct()
	{
		parent::__construct();
        $this->load->language('reservation');
		$this->load->model('reservation_m');
	}

    /**
     * index controller
     * Loads reservation form
     */
    public function index()
	{
        $reservation = new stdClass;
        foreach ($this->validation_rules as $key => $field)
        {
            $reservation->$field['field'] = set_value($field['field']);
        }
        $this->template
            ->title($this->module_details['name'])
            ->append_css('module::jquery-ui-timepicker-addon.css')
            ->append_css('module::jquery-ui.css')
            ->append_css('module::reservation.css')
            ->append_js('module::jquery-ui-timepicker-addon.js')
            ->append_js('module::jquery-ui-sliderAccess.js')
            ->append_js('module::jquery.validate.js')
            ->append_js('module::reservation.js')
            ->set('reservation',$reservation)
            ->build('index');
	}

    /**
     * Create reservation entry in database
     */
    public function create()
	{
        // Validate reservation input
        $this->form_validation->set_rules($this->validation_rules);
        if ($this->form_validation->run())
        {

            // Save reservation into database
            if ($id = $this->reservation_m->insert(array(
                'name'				=> $this->input->post('name'),
                'email'				=> $this->input->post('email'),
                'phone'		        => $this->input->post('phone'),
                'event_date'  			=> date('Y-m-d',strtotime(str_replace("-","/",$this->input->post('date')))),
                'event_time'  			=> date('H:i:s',strtotime(str_replace("-","/",$this->input->post('date')))),
                'location'			=> $this->input->post('location'),
                'no_of_hours'       => $this->input->post('no_of_hours'),
                'additional_notes'	=> $this->input->post('additional_notes'),
                'created_on'		=> date("Y-m-d H:i:s"),

            )))
            {
                // Send email to Admin
                $data['sender_agent']	= $this->agent->browser() . ' ' . $this->agent->version();
                $data['sender_ip']		= $this->input->ip_address();
                $data['sender_os']		= $this->agent->platform();
                $data['slug'] 			= 'new_reservation';
                $data['reply-to']		= Settings::get('server_email');
                $data['to']				= Settings::get('server_email');
                $data['from']			= $this->input->post('email');
                $data['name']			= $this->input->post('name');
                // Try to send the email
                $results = Events::trigger('email', $data, 'array');

                $this->pyrocache->delete_all('reservation_m');
                $this->session->set_flashdata('success', 'Your reservation is saved');
                redirect('reservation/success');
            }
            else
            {
                $this->session->set_flashdata('error', 'Reservation cannot be saved. Please try again.');
            }

            // Redirect back to the form or main page

        }
        else
        {
            // Go through all the known fields and get the post values
            $reservation = new stdClass;
            foreach ($this->validation_rules as $key => $field)
            {
                $reservation->$field['field'] = set_value($field['field']);
            }

        }
        $this->template
            ->title($this->module_details['name'])
            ->set('reservation',$reservation)
            ->build('index');
	}

    /**
     * Success controller
     * shown on successful reservation
     */
    public function success(){
        $this->template
            ->title($this->module_details['name'])
            ->build('success');
    }

}

/* End of file reservation.php */