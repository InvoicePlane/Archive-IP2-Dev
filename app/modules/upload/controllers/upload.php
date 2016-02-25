<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Upload
 * @package Modules\Upload\Controllers
 * @property CI_Loader $load
 * @property Mdl_Uploads $mdl_uploads
 */
class Upload extends Admin_Controller
{
    public $targetPath;

    /**
     * Upload constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('upload/mdl_uploads');
        $this->targetPath = getcwd() . '/uploads/customer_files';
    }

    /**
     * Uploads a file 
     * @param $customerId
     * @param $url_key
     * @return bool
     */
    public function upload_file($customerId, $url_key)
    {
        Upload::create_dir($this->targetPath . '/');

        if (!empty($_FILES)) {
            $tempFile = $_FILES['file']['tmp_name'];
            $fileName = preg_replace('/\s+/', '_', $_FILES['file']['name']);
            $targetFile = $this->targetPath . '/' . $url_key . '_' . $fileName;
            $file_exists = file_exists($targetFile);

            if (!$file_exists) //If file does not exists then upload
            {
                $data = array(
                    'client_id' => $customerId,
                    'url_key' => $url_key,
                    'file_name_original' => $fileName,
                    'file_name_new' => $url_key . '_' . $fileName
                );
                $this->mdl_uploads->create($data);

                move_uploaded_file($tempFile, $targetFile);
            } else //If file exists then echo the error and set a http error response
            {
                echo lang('error_dublicate_file');;
                http_response_code(404);
            }

        } else {
            return Upload::show_files($url_key, $customerId);
        }
        
        return false;
    }

    /**
     * Deletes a file by the given URL key
     * @param $url_key
     */
    public function delete_file($url_key)
    {
        $path = $this->targetPath;
        $fileName = $_POST['name'];

        $this->mdl_uploads->delete($url_key, $fileName);
        unlink($path . '/' . $url_key . '_' . $fileName);

    }

    /**
     * Creates a directory
     * @param $path
     * @param string $chmod
     * @return bool
     */
    public function create_dir($path, $chmod = '0777')
    {
        if (!(is_dir($path) OR is_link($path))) {
            return mkdir($path, $chmod);
        } else {
            return false;
        }
    }

    /**
     * Shows the files for a given URL key
     * @param $url_key
     * @param null $customerId
     * @return bool
     */
    public function show_files($url_key, $customerId = null)
    {
        $result = array();
        $path = $this->targetPath;

        $files = scandir($path);

        if ($files !== false) {
            foreach ($files as $file) {
                if ('.' != $file && '..' != $file && strpos($file, $url_key) !== false) {
                    $obj['name'] = substr($file, strpos($file, '_', 1) + 1);
                    $obj['fullname'] = $file;
                    $obj['size'] = filesize($path . '/' . $file);
                    $obj['fullpath'] = $path . '/' . $file;
                    $result[] = $obj;
                }
            }
        } else {

            echo false;
        }
        
        echo json_encode($result);
    }
}
