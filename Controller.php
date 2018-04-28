<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController {

    use AuthorizesRequests,
        DispatchesJobs,
        ValidatesRequests;

    public function checkFields($camposEsperados, $inputs) {
        $retorno = ['error' => 0, 'campos' => array()];
        if (is_array($camposEsperados)) {
            foreach ($camposEsperados as $campo) {
                if (isset($inputs[$campo])) {
                    $retorno['campos'][$campo] = true;
                } else {
                    $retorno['error'] ++;
                    $retorno['campos'][$campo] = false;
                }
            }
        } else {
            if (isset($inputs[$campo])) {
                $retorno['campos'][$campo] = true;
            } else {
                $retorno['error'] ++;
                $retorno['campos'][$campo] = false;
            }
        }
        return $retorno;
    }

    public function removeNullFields($obj) {
        foreach ($obj instanceof Model ? $obj->toarray() : $obj as $chave => $valor) {
            if (!$valor) {
                unset($obj->$chave);
            }
        }
        return $obj;
    }

    public function setFieldInObject($obj, $campos, $request) {
        foreach ($campos as $campo) {
            if ($request->input($campo)) {
                $obj->__set($campo, $request->input($campo));
            }
        }
        return $obj;
    }

    public function prepareInvalidRequest($noFieldError) {
        $reponse['error'] = 'invalid_request';
        $reponse['message'] = 'The request is missing a required parameter, includes an invalid parameter value, includes a parameter more than once, or is otherwise malformed.';
        $fields = array_map(function ($key, $value) {
            if (!$value) {
                return$key;
            }
        }, array_keys($noFieldError['campos']), $noFieldError['campos']);
        $reponse['hint'] = "Check the [" . implode(', ', $fields) . "] parameter(s)";
        return $reponse;
    }
}
