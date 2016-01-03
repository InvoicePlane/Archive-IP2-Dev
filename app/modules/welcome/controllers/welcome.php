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

class Welcome extends CI_Controller
{
    public function index()
    {
        $this->load->view('welcome');
    }
}