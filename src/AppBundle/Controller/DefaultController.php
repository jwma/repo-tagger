<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction(Request $request)
    {
    }

    /**
     * @Route("/api/repo")
     * @Method("GET")
     */
    public function listRepoAction(Request $request)
    {
        if (!$this->isCsrfTokenValid('my_xsrftoken', $request->headers->get('xsrftoken'))) {
            return new JsonResponse(null, 403);
        }

        $em = $this->getDoctrine()->getManager();

        $list = $em->getRepository('AppBundle:Repo')->all();

        return new JsonResponse([
            'list' => $list
        ]);
    }
}
