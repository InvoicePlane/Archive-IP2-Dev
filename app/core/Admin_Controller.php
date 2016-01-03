<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * InvoicePlane
 * 
 * A free and open source web based invoicing system
 *
 * @package		InvoicePlane
 * @author		Kovah (www.kovah.de)
 * @copyright	Copyright (c) 2012 - 2016 InvoicePlane.com
 * @license		https://invoiceplane.com/license-copyright
 * @link		https://invoiceplane.com
 * 
 */

class Admin_Controller extends User_Controller
{

    public function __construct()
    {
        parent::__construct('user_type', 1);
    }

}

?>