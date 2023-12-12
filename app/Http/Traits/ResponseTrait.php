<?php

namespace App\Http\Traits;

trait ResponseTrait
{
    private $success_code;
    private $error_code;
    private $success_message;
    private $error_message;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->success_code = 200;
        $this->error_code = 201;
        $this->success_message = "Successful";
        $this->error_message = "Something went wrong!";
    }

    /**
     * Get success code
     */
    public function get_success_code($code = null)
    {
        if ($code) {
            $this->success_code = $code;
        }
        return $this->success_code;
    }

    /**
     * Get error code
     */

    public function get_error_code($code = null)
    {
        if ($code) {
            $this->error_code = $code;
        }
        return $this->error_code;
    }


    /**
     * Get error code
     */

    public function set_err_code($code = 201)
    {
        $this->error_code =  $code ?? $this->error_code;
    }

    /**
     * Get error code
     */

    public function get_err_code()
    {
        return $this->error_code;
    }


    /**
     * Get success message
     */
    public function get_success_message($message = null)
    {
        return $this->success_message;
    }

    /**
     * Get error message
     */
    public function get_error_message($message = null)
    {
        if ($message) {
            $this->error_message = $message;
        }
        return $this->error_message;
    }
}
