<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Coupon_m extends MY_Model
{
    protected $_table = 'coupons';

	public function get_all()
	{
		return $this->db
			->select('*')
			->order_by('created_on', 'desc')
			->get('coupons')
			->result();
	}

    function get($id)
    {
        return $this->db->select('coupons.*')
            ->where(array('coupons.id' => $id))
            ->get('coupons')
            ->row();
    }

    public function get_by($key, $value = '')
    {
        $this->db->select('coupons.*');
        if (is_array($key))
        {
            $this->db->where($key);
        }
        elseif ($key == 'name')
        {
            $this->db->like('coupons.name', $value);
        }
        else
        {
            $this->db->where($key, $value);
        }

        $result = $this->db->get($this->_table)->row();
        //echo '<pre>SQl Query: '.$this->db->last_query().'</pre>';
        return $result;
    }

    function get_many_by($params = array())
    {
        $this->load->helper('date');

        if (!empty($params['expiry_date'])&& !empty($params['expiry_date']))
        {
            $this->db->where('coupons.expiry_date', $params['expiry_date']);
        }

        // Is a is_expired set?
        if (!empty($params['is_expired'])&& !empty($params['is_expired']))
        {
            $this->db->where('coupons.is_expired', $params['is_expired']);
        }

        if (array_key_exists('name', $params) && !empty($params['name']))
        {
            $this->db->like('coupons.name', $params['name']);
        }

        if (array_key_exists('search', $params))
        {
            $this->db->like('coupons.name', $params['search']);
            $this->db->or_like('coupons.description', "%".$params['search']."%");
        }

        // Limit the results based on 1 number or 2 (2nd is offset)
        if (isset($params['limit']) && is_array($params['limit']))
            $this->db->limit($params['limit'][0], $params['limit'][1]);
        elseif (isset($params['limit']))
            $this->db->limit($params['limit']);

        $result = $this->get_all();
        //echo 'Last Query: '.$this->db->last_query();
        //exit;
        return $result;
    }

    function get_one_by($params = array())
    {
        $this->db->select('coupons.*');
        // Is a is_expired set?
        if (!empty($params['is_expired'])&& !empty($params['is_expired']))
        {
            $this->db->where('is_expired', $params['is_expired']);
        }

        if (array_key_exists('name', $params) && !empty($params['name']))
        {
            $this->db->like('coupons.name', $params['name']);
        }

        if (array_key_exists('description', $params) && !empty($params['description']))
        {
            $this->db->like('coupons.description', "%".$params['description']."%");
        }

        if (array_key_exists('search', $params))
        {
            $this->db->like('coupons.name', $params['search']);
            $this->db->or_like('coupons.description', "%".$params['description']."%");
        }

        $result = $this->db->get($this->_table)->row();
        //echo 'Last Query: '.$this->db->last_query();
        //exit;
        return $result;
    }

    function count_by($params = array())
    {
        $this->db->select('coupons.*');

        // Is a is_expired set?
        if (!empty($params['is_expired'])&& !empty($params['is_expired']))
        {
            $this->db->where('is_expired', $params['is_expired']);
        }

        if (array_key_exists('name', $params) && !empty($params['name']))
        {
            $this->db->like('coupons.name', $params['name']);
        }

        if (array_key_exists('description', $params) && !empty($params['description']))
        {
            $this->db->like('coupons.description', "%".$params['description']."%");
        }

        if (array_key_exists('search', $params))
        {
            $this->db->like('coupons.name', $params['search']);
            $this->db->or_like('coupons.description', "%".$params['description']."%");
        }

        $result = $this->db->count_all_results($this->_table);
        //echo $this->db->last_query();
        //exit;
        return $result;
    }
}