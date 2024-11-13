<?php

namespace App\Traits;

trait IdentifyContact {
  function isEmail($value) {
    return filter_var($value, FILTER_VALIDATE_EMAIL);
  }

  function isPhone($value) {
    // Verificar se é um telefone válido (usando regex para telefones brasileiros)
    $phonePattern = '/^\(\d{2}\)\s\d{4,5}-\d{4}$/';

    return preg_match($phonePattern, $value);
  }
}