<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        return new Response('halo');
    }

    /**
     * @Route("/api/repo")
     * @Method("GET")
     */
    public function listRepoAction()
    {
        $em = $this->getDoctrine()->getManager();

        $list = $em->getRepository('AppBundle:Repo')->all();

        return new JsonResponse($list);
    }
}
