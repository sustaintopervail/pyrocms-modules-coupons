<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * API module
 *
 * @author Muhammad Faisal(sustaintopervail@yahoo.com)
 * @package PyroCMS\Core\Modules\API
 */
class Module_Coupon extends Module
{
	public $version = '1.0.0';

    public function info()
    {
        return array(
            'name' => array(
                'en' => 'Coupons'
            ),
            'description' => array(
                'en' => 'Coupons management module'
            ),
            'frontend' => true,
            'backend' => true,
            'menu' => 'content',
            'sections' => array(
                'coupon' => array(
                    'name' => 'coupon:title',
                    'uri' => 'admin/coupon',
                    'shortcuts' => array(
                        'create' => array(
                            'name' => 'coupon:create_title',
                            'uri' => 'admin/coupon/create',
                            'class' => 'add'
                        )
                    )
                )
            )
        );
    }

    public function install()
    {
        $this->dbforge->drop_table('coupons');
        $table =  array(
                'id' => array('type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'primary' => true),
                'name' => array('type' => 'VARCHAR', 'constraint' => 100, 'null' => false, ),
                'description' => array('type' => 'TEXT', 'null' => false),
                'thumbnail' => array('type' => 'INT', 'constraint' => 10 ),
                'expiry_date' => array('type' => 'DATE',  'null' => false),
                'is_expired' => array('type' => 'INT',  'null' => false),
                'created_on' => array('type' => 'DATETIME','null'=>false),
                'updated_on' => array('type' => 'TIMESTAMP'),

        );

        $this->dbforge->add_field($table);
        $this->dbforge->add_key('id',true);
        if ( !$this->dbforge->create_table('coupons'))
        {
            return FALSE;
        }

        return true;
    }

    public function uninstall()
    {
        $this->dbforge->drop_table('coupons');
        return true;
    }

    public function upgrade($old_version)
    {
        return true;
    }

}