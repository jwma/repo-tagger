<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

    /**
     * @Route("/api/repo")
     * @Method("PATCH")
     */
    public function updateRepoAction(Request $request)
    {
        if (!$this->isCsrfTokenValid('my_xsrftoken', $request->headers->get('xsrftoken'))) {
            return new JsonResponse(null, 403);
        }

        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : array());
        }

        $id = $request->get('id');

        if (is_null($id)) {
            return new JsonResponse(null, 400);
        }

        $em = $this->get('doctrine')->getManager();
        $repo = $em->getRepository('AppBundle:Repo')->find($id);

        if (!$repo) {
            return new JsonResponse(null, 404);
        }

        $repo
            ->setTags($request->get('tags', []))
            ->setRemark($request->get('remark'));

        $em->flush();

        return new JsonResponse();
    }

    /**
     * @Route("/api/repo")
     * @Method("DELETE")
     */
    public function deleteRepoAction(Request $request)
    {
        if (!$this->isCsrfTokenValid('my_xsrftoken', $request->headers->get('xsrftoken'))) {
            return new JsonResponse(null, 403);
        }

        $id = $request->get('id');
        if (is_null($id)) {
            return new JsonResponse(null, 400);
        }

        $em = $this->get('doctrine')->getManager();
        $repo = $em->getRepository('AppBundle:Repo')->find($id);

        if (!$repo) {
            return new JsonResponse(null, 404);
        }

        $repo
            ->setIsDeleted(true)
            ->setDeletedAt(new \DateTime());

        $em->flush();

        return new JsonResponse();
    }

    /**
     * @Route("/api/search")
     */
    public function searchAction(Request $request)
    {
        $search = $request->get('search');

        if (is_null($search)) {
            return new JsonResponse(['list' => []]);
        }

        $searchArr = explode(':', $search);
        $filters = [];

        foreach ($searchArr as $one) {
            $tmp = explode('=', $one);
            if (count($tmp) < 2) {
                continue;
            }

            $filters[] = ['name' => $tmp[0], 'value' => $tmp[1]];
        }

        $em = $this->getDoctrine()->getManager();
        $list = $em->getRepository('AppBundle:Repo')->search($filters);

        return new JsonResponse([
            'list' => $list
        ]);
    }
}
