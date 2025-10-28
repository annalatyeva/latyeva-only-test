<?php
  function normalizeTel($tel) {
    $tel = trim($tel);
    $tel = preg_replace('/[^0-9]/', '', $tel);
    if (strlen($tel) == 11 && $tel[0] == '8') {
        $tel = '7' . substr($tel, 1);
    }
    if (strlen($tel) == 10) {
        $tel = '7' . $tel;
    }
    return $tel;
  }


  function checkEmailOrTel($enterlogin) {
    if (strpos($enterlogin, '@') !== false) {
      return 'email';
    } else {
      return 'tel';
    }
  }
