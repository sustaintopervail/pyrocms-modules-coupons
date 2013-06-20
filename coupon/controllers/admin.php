<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Admin controller for the api module
 * 
 * @author 		PyroCMS Dev Team
 * @package 	Addons\SharedAddons\Modules\Coupon\Controllers
 */
class Admin extends Admin_Controller
{
    protected $section = 'coupon';
    /**
     * @var array   Validation rules for coupon form
     */
    protected $validation_rules = array(
        'name' => array(
            'field' => 'name',
            'label' => 'Name',
            'rules' => 'trim|htmlspecialchars|required|max_length[100]'
        ),
        'email' => array(
            'field' => 'description',
            'label' => 'Description',
            'rules' => 'trim|required'
        ),
        /*array(
            'field' => 'thumbnail',
            'label' => 'Thumbnail',
            'rules' => 'trim|required'
        ),*/
        array(
            'field' => 'expiry_date',
            'label' => 'ExpiryDate',
            'rules' => 'trim|required'
        )
    );
	public function __construct()
	{
		parent::__construct();
        $this->load->model(array('coupon_m','files/file_folders_m'));
        $this->load->library('files/files');
		$this->load->language('coupon');
	}
	
	/**
	 * Index method
	 *
	 * @return void
	 */
	public function index()
	{


        //set the base/default where clause
        $base_where = array();

        //add post values to base_where if f_module is posted
        if ($this->input->post('f_expiry_date')) 	$base_where['expiry_date'] = $this->input->post('f_expiry_date');
        if ($this->input->post('f_status')) 	$base_where['is_expired'] 	= $this->input->post('f_status');
        if ($this->input->post('f_name')) 	$base_where['name'] = $this->input->post('f_name');


        // Create pagination links
        $total_rows = $this->coupon_m->count_by($base_where);
        $pagination = create_pagination('admin/coupon/index', $total_rows);

        // Using this data, get the relevant results
        $coupons = $this->coupon_m->limit($pagination['limit'])->get_many_by($base_where);

        //do we need to unset the layout because the request is ajax?
        $this->input->is_ajax_request() ? $this->template->set_layout(FALSE) : '';

        $this->template
            ->title($this->module_details['name'])
            ->append_js('admin/filter.js')
            ->append_js('module::coupon_form.js')
            ->set_partial('filters', 'admin/partials/filters')
            ->set('pagination', $pagination)
            ->set('coupons', $coupons);

        $this->input->is_ajax_request()
            ? $this->template->build('admin/tables/coupons')
            : $this->template->build('admin/index');
	}

    private function generate_thumbnail(){

        if($this->file_folders_m->exists('coupons')){
            $result = $this->file_folders_m->get_by(array('slug'=>'coupons'));
            //print_r($result);

        }else{
            $result = Files::create_folder(0, 'coupons');
            $result = (object)$result;
        }
        $coupon_thumbnail = Files::upload($result->id, $this->input->post('name').'_'.time(), 'userfile', 100, 100, true, 'jpg|jpeg|png|gif|JPG|JPEG');

       /* print_r($coupon_thumbnail);
        exit;*/
        if(isset($coupon_thumbnail['data']['id'])){
            return $coupon_thumbnail['data']['id'];
        }else{
            return 0;
        }
    }
    /**
     * Create new Coupon
     *
     * @return void
     */
    public function create()
    {
        $this->form_validation->set_rules($this->validation_rules);

        if ($this->form_validation->run())
        {
            if($_FILES['userfile']['error']==0){
                $thumbnail = $this->generate_thumbnail();
            }else{
                $thumbnail = 0;
            }
            $post_arr = array(
                'name'				=> $this->input->post('name'),
                'description'		=> $this->input->post('description'),
                'thumbnail'		    => $thumbnail,
                'expiry_date'	    => date('Y-m-d',strtotime($this->input->post('expiry_date'))),
                'is_expired'	    => 0,
                'created_on'	    => date("Y-m-d H:i:s"),
                'updated_on'		=> date("Y-m-d H:i:s"),

            );

            if ($id = $this->coupon_m->insert($post_arr))
            {
                $this->pyrocache->delete_all('coupon_m');
                $this->session->set_flashdata('success', 'Coupon has been added successfully', $this->input->post('name'));
            }
            else
            {
                $this->session->set_flashdata('error', 'There is some error in adding new coupon. Please try again.');
            }

            // Redirect back to the form or main page
            $this->input->post('btnAction') == 'save_exit' ? redirect('admin/coupon') : redirect('admin/coupon/edit/' . $id);
        }
        else
        {
            $post = new stdClass();
            // Go through all the known fields and get the post values
            foreach ($this->validation_rules as $key => $field)
            {
                $post->$field['field'] = set_value($field['field']);
            }

        }

        $this->template
            ->title($this->module_details['name'], lang('coupon_create_title'))
            ->append_metadata($this->load->view('fragments/wysiwyg', $this->data, TRUE))
            ->append_js('module::coupon_form.js')
            ->append_css('module::coupon.css')
            ->set('coupon', $post)
            ->build('admin/partials/form');
    }

    /**
     * Edit Coupon
     *
     * @access public
     * @param int $id the ID of the coupon to edit
     * @return void
     */
    public function edit($id = 0)
    {
        $id OR redirect('admin/coupon');

        $coupon = $this->coupon_m->get($id);

        $this->form_validation->set_rules($this->validation_rules);

        if ($this->form_validation->run())
        {

            if($_FILES['userfile']['error']==0){
                $thumbnail = $this->generate_thumbnail();
            }else{
                $thumbnail = $coupon->thumbnail;
            }
            $result = $this->coupon_m->update($id, array(
                'name'				=> $this->input->post('name'),
                'description'		=> $this->input->post('description'),
                'thumbnail'		    => $thumbnail,
                'expiry_date'	    => date('Y-m-d',strtotime($this->input->post('expiry_date'))),
                'is_expired'	    => 0,
                'updated_on'		=> date("Y-m-d H:i:s"),

            ));

            if ($result)
            {
                $this->session->set_flashdata(array('success' => sprintf('Coupon Edited Successfully', $this->input->post('name'))));
            }
            else
            {
                $this->session->set_flashdata('error','There is an error while editing '.$this->input->post('name'));
            }

            // Redirect back to the form or main page
            redirect('admin/coupon');
        }

        // Go through all the known fields and get the post values
        foreach ($this->validation_rules as $key => $field)
        {
            if (isset($_POST[$field['field']]))
            {
                $coupon->$field['field'] = set_value($field['field']);
            }
        }

        $this->template
            ->title($this->module_details['name'], sprintf(lang('edit_title'), $coupon->name))
            ->append_metadata($this->load->view('fragments/wysiwyg', $this->data, TRUE))
            ->append_js('module::coupon_form.js')
            ->append_css('module::coupon.css')
            ->set('coupon', $coupon)
            ->build('admin/edit');
    }

    /**
     * Delete Coupon
     * @access public
     * @param int $id the ID of the coupon to delete
     * @return void
     */
    public function delete($id = 0)
    {

        // Delete one
        $ids = ($id) ? array($id) : $this->input->post('action_to');

        // Go through the array of slugs to delete
        if ( ! empty($ids))
        {
            $coupon_names = array();
            $deleted_ids = array();
            foreach ($ids as $id)
            {
                // Get the current coupon so we can grab the id too
                if ($coupon = $this->coupon_m->get($id))
                {

                    if ($this->coupon_m->delete($id))
                    {
                        // Wipe cache for this model, the content has changed

                        $this->pyrocache->delete('coupon_m');
                        $coupon_names[] = $coupon->name;
                        $deleted_ids[] = $id;
                    }
                }
            }
        }

        // Some coupons have been deleted
        if ( ! empty($coupon))
        {
            // Only deleting one coupon
            if (count($coupon) == 1)
            {
                $this->session->set_flashdata('success', sprintf($this->lang->line('coupon_delete_success'), $coupon_names[0]));
            }
            // Deleting multiple coupons
            else
            {
                $this->session->set_flashdata('success', sprintf($this->lang->line('coupon_mass_delete_success'), implode('", "', $coupon_names)));
            }
        }
        // For some reason, none of them were deleted
        else
        {
            $this->session->set_flashdata('notice', lang('coupon_delete_error'));
        }

        redirect('admin/coupon');
    }

    /**
     * Helper method to determine what to do with selected items from form coupon
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
                redirect('admin/coupon');
                break;
        }
    }
}

/* End of file admin.php */