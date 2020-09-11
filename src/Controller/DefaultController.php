<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{
    public function index(): Response
    {
        return $this->render(
            'lesson2/syntax.html.twig',
            array(
                'variables' => array('intro' => '<b>test string</b>', 'template' => 'base.html.twig')
            )
        );
    }

    public function subrequest()
    {
        return new Response('test sub request');
    }
}
