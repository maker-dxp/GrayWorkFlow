<?php

const WIDGET_DEBUG                      =       true;

const CLASS_MAP                         =       array(
  'JWT'                                 =>      'Firebase/JWT/JWT.php',
  'JWK'                                 =>      'Firebase/JWT/JWK.php',
  'ExpiredException'                    =>      'Firebase/JWT/ExpiredException.php',
  'BeforeValidException'                =>      'Firebase/JWT/BeforeValidException.php',
  'SignatureInvalidException'           =>      'Firebase/JWT/SignatureInvalidException.php'
);

const PHP_IGNORE                        =       array(
    'widget.config.php'
);

const DEFAULT_AVATAR                    =       '';

/* 用户权限 */
const SU                                =       'su';
const ADMIN                             =       'admin';