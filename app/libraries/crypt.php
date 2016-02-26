<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Class Crypt
 * @package Libraries
 */
class Crypt
{
    /**
     * @return string
     */
    public function salt()
    {
        return substr(sha1(mt_rand()), 0, 22);
    }

    /**
     * @param $password
     * @param $salt
     * @return string
     */
    public function generate_password($password, $salt)
    {
        return crypt($password, '$2a$10$' . $salt);
    }

    /**
     * @param $hash
     * @param $password
     * @return bool
     */
    public function check_password($hash, $password)
    {
        $new_hash = crypt($password, $hash);

        return ($hash == $new_hash);
    }

}
