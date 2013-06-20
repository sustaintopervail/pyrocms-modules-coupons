<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * API module
 *
 * @author PyroCMS Dev Team
 * @package PyroCMS\Core\Modules\API
 */
class Module_Reservation extends Module
{
	public $version = '1.0.0';

	public function info()
	{
		return array(
			'name' => array(
				'en' => 'Reservation Module',
				'el' => 'Reservation Module',
				'fr' => 'Reservation Module',
				'hu' => 'Reservation Module'
			),
			'description' => array(
				'en' => 'Reservation management module',
				'el' => 'Reservation management module',
				'fr' => 'Reservation management module',
                'hu' => 'Reservation management module',
			),
			'frontend' => TRUE,
			'backend' => TRUE,
			'menu' => 'content',
            'sections' => array(
                'reservations' => array(
                    'name' => 'reservation:title',
                    'uri' => 'admin/reservation',
                ),
            ),
		);
	}

    public function install()
    {
        $this->dbforge->drop_table('reservations');
        $table =  array(
                'id' => array('type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'primary' => true),
                'name' => array('type' => 'VARCHAR', 'constraint' => 100, 'null' => false, ),
                'email' => array('type' => 'VARCHAR', 'constraint' => 100, 'null' => false),
                'phone' => array('type' => 'VARCHAR', 'constraint' => 20 ),
                'event_date' => array('type' => 'DATE',  'null' => false),
                'event_time' => array('type' => 'TIME',  'null' => false),
                'location' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => false),
                'no_of_hours' => array('type' => 'INT', 'constraint' => 11, ),
                'additional_notes' => array('type' => 'TEXT'),
                'amount' => array('type' => 'DOUBLE', 'constraint' => '18,2', 'default' => 0),
                'is_invoiced' => array('type' => 'INT', 'constraint' => 4, 'default' => 0),
                'invoice_id' => array('type' => 'VARCHAR', 'constraint' => 30, 'null' => true),
                'invoice_url' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => true),
                'invoiced_on' => array('type' => 'DATETIME', 'null' => true),
                'created_on' => array('type' => 'DATETIME','null'=>false),
                'updated_on' => array('type' => 'TIMESTAMP'),

        );

        $this->dbforge->add_field($table);
        $this->dbforge->add_key('id',true);
        if ( !$this->dbforge->create_table('reservations'))
        {
            return FALSE;
        }
        $email_template ="INSERT INTO `".$this->db->dbprefix('email_templates')."` (`id` ,`slug` ,`name` ,`description` ,`subject` ,`body` ,`lang` ,`is_default` ,`module`)
                          VALUES (NULL , 'new_reservation', 'New Reservation Email', 'After a reservation is made this email is sent to admin in order to notify him to take appropriate action', '{{ settings:site_name }} - New Reservation', '<p>Hello Admin,</p> <p>A new reservation is made by {{name}}</p> <p>Please check reservations at <a href=\"{{ url:site }}admin/reservation\">Reservations</a></p>', 'en', '1', 'reservation')";
        $this->db->query($email_template);
        return true;
    }

    public function uninstall()
    {

        $this->dbforge->drop_table('reservations');
        $remove_email_template_sql = "DELETE FROM `".$this->db->dbprefix('email_templates')."` WHERE `slug`='new_reservation'";
        $this->db->query($remove_email_template_sql);
        return true;
    }

    public function upgrade($old_version)
    {
        return true;
    }

}