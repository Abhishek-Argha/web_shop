<?php
// src/Controller/IndexController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Service\DataService;



class IndexController extends AbstractController
{
    public function index(Request $request, DataService $dataService): Response
    {   
        $data = $dataService->getPaginatedData($request);

        return $this->render('homepage.html.twig', ['data' => $data]);
    }
}