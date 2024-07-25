<?php

namespace DataDog\AuditBundle\Util\Rest;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class DataTableRestResponse extends JsonResponse {
    public function __construct($data, $status = 200, $message = "", $errors = null, $headers = array())
    {
        // Prepare form errors if provided
        if($errors instanceof Form) {
            $errors = $this->getFormErrors($errors);
        }

        $structure = array(
            "has_error" => $this->isError($status),
            "additional_errors" => $errors,
            "code" => $status,
            "message" => ($message != "") ? $message : null,
        );

        $structure = array_merge($structure, $data);

        parent::__construct($structure, $status, $headers);
    }

    private function isError($status) {
        if($status >= 200 && $status <= 299) { return false; }
        if($status >= 300 && $status <= 399) { return false; }
        if($status >= 400 && $status <= 499) { return true; }
        if($status >= 500 && $status <= 599) { return true; }
        return true;
    }
}