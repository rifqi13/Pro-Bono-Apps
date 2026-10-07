<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected $request;
    protected $helpers = ['form', 'url', 'auth', 'format'];
    protected $session;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
{
    parent::initController($request, $response, $logger);
    
    // Set timezone & locale
    date_default_timezone_set('Asia/Jakarta');
    setlocale(LC_TIME, 'id_ID', 'Indonesian', 'id');
    
    $this->db = \Config\Database::connect();
}

    
}

