<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Reservation_m extends MY_Model
{
    protected $_table = 'reservations';

	public function get_all()
	{
		return $this->db
			->select('*')
			->order_by('created_on', 'desc')
			->get('reservations')
			->result();
	}

    function get($id)
    {
        return $this->db->select('reservations.*')
            ->where(array('reservations.id' => $id))
            ->get('reservations')
            ->row();
    }

    public function get_by($key, $value = '')
    {
        $this->db->select('reservations.*');
        if (is_array($key))
        {
            $this->db->where($key);
        }
        elseif ($key == 'name')
        {
            $this->db->like('reservations.name', $value);
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

        if (!empty($params['date'])&& !empty($params['date']))
        {
            $this->db->where('reservations.date', $params['date']);
        }

        // Is a is_invoiced set?
        if (!empty($params['is_invoiced'])&& !empty($params['is_invoiced']))
        {
            $this->db->where('reservations.is_invoiced', $params['is_invoiced']);
        }

        if (array_key_exists('name', $params) && !empty($params['name']))
        {
            $this->db->like('reservations.name', $params['name']);
        }

        if (array_key_exists('email', $params) && !empty($params['email']))
        {
            $this->db->like('reservations.email', $params['email']);
        }

        if (array_key_exists('location', $params) && !empty($params['location']))
        {
            $this->db->like('reservations.location', $params['location']);
        }

        if (array_key_exists('search', $params))
        {
            $this->db->like('reservations.name', $params['search']);
            $this->db->or_like('reservations.email', $params['search']);
            $this->db->or_like('reservations.location', $params['search']);
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
        $this->db->select('reservations.*');
        // Is a is_invoiced set?
        if (!empty($params['is_invoiced'])&& !empty($params['is_invoiced']))
        {
            $this->db->where('is_invoiced', $params['is_invoiced']);
        }

        if (array_key_exists('name', $params) && !empty($params['name']))
        {
            $this->db->like('reservations.name', $params['name']);
        }

        if (array_key_exists('email', $params) && !empty($params['email']))
        {
            $this->db->like('reservations.email', $params['email']);
        }

        if (array_key_exists('location', $params) && !empty($params['location']))
        {
            $this->db->like('reservations.location', $params['location']);
        }

        if (array_key_exists('search', $params))
        {
            $this->db->like('reservations.name', $params['search']);
            $this->db->or_like('reservations.email', $params['search']);
            $this->db->or_like('reservations.location', $params['search']);
        }

        $result = $this->db->get($this->_table)->row();
        //echo 'Last Query: '.$this->db->last_query();
        //exit;
        return $result;
    }

    function count_by($params = array())
    {
        $this->db->select('reservations.*');

        // Is a is_invoiced set?
        if (!empty($params['is_invoiced'])&& !empty($params['is_invoiced']))
        {
            $this->db->where('is_invoiced', $params['is_invoiced']);
        }

        if (array_key_exists('name', $params) && !empty($params['name']))
        {
            $this->db->like('reservations.name', $params['name']);
        }

        if (array_key_exists('email', $params) && !empty($params['email']))
        {
            $this->db->like('reservations.email', $params['email']);
        }

        if (array_key_exists('location', $params) && !empty($params['location']))
        {
            $this->db->like('reservations.location', $params['location']);
        }

        if (array_key_exists('search', $params))
        {
            $this->db->like('reservations.name', $params['search']);
            $this->db->or_like('reservations.email', $params['search']);
            $this->db->or_like('reservations.location', $params['search']);
        }

        $result = $this->db->count_all_results($this->_table);
        //echo $this->db->last_query();
        //exit;
        return $result;
    }
}