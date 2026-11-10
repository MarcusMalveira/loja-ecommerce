<?php

// Identifica se estamos no ambiente da Vercel.
$producao = getenv('VERCEL') === '1'
    || getenv('VERCEL_ENV') !== false;

// Registra erros para diagnóstico.
ini_set('log_errors', '1');

// Controla a exibição pública dos erros.
ini_set('display_errors', $producao ? '0' : '1');
ini_set('display_startup_errors', $producao ? '0' : '1');

// Monitora todos os níveis de erro do PHP.
error_reporting(E_ALL);
