<?php

namespace Eventual\Bundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Eventual\Bundle\Entity\EventCollection;

class CollectionsController extends CollectionAware
{
    /**
     * @Route("/{page}", name="collections_index", requirements={"page" = "\d+"}, defaults={"page" = 1})
     * @Method({"GET"})
     * @Template()
     */
    public function indexAction($page)
    {
        $page = ($page > 0) ? $page : 1;

        $collectionsRepository = $this->getDoctrine()
            ->getRepository('Eventual:EventCollection');

        $count = $collectionsRepository->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $page = ($page-1*25 > $count) ? 1 : $page;

        $collections = $collectionsRepository->findBy(
            array('user' => $this->getUser()),
            array('created' => 'DESC'),
            25,
            ($page-1)*25
        );

        return array(
            'total_pages'              => ceil($count/25),
            'collections'              => $collections,
            'count'                    => count($collections),
            'page'                     => $page,
        );
    }

    /**
     * @Route("/create", name="collections_create")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function createAction(Request $request)
    {
        $collection = new EventCollection();

        $form = $this->createFormBuilder($collection)
                    ->add('name', 'text')
                    ->add('createCollection', 'submit')
                    ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $collection->setUser($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($collection);
            $em->flush();
            return $this->redirect($this->generateUrl('show_collection', array(
                'id' => $collection->getId(),
            )));
        }

        return array(
            'form'    => $form->createView(),
        );
    }

    /**
     * @Route("/show/{id}", name="show_collection", requirements={"id"="\d+"}, defaults={"page"=1})
     * @Method({"GET"})
     * @Template()
     */
    public function showAction($id, $page)
    {
        $em = $this->getDoctrine()
            ->getManager();

        $em->getConfiguration()
            ->addCustomDatetimeFunction('DATE', '\\Eventual\\Bundle\\DQL\\AST\\DateFunction');

        $events = $em->createQuery(
            'SELECT COUNT(e.id) as num_events, DATE(e.date) AS event_date
            FROM Eventual:Event e
            WHERE e.collection = :col
            GROUP BY event_date
            ORDER BY event_date ASC'
        )->setParameter('col', $id)
        ->getResult();

        return array(
            'collection'               => $this->getCollection($id),
            'events'                   => $events,
        );
    }

    /**
     * @Route("/update/{id}", name="collections_update", requirements={"id"="\d+"})
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function updateAction($id)
    {
        $request = $this->get('request');
        $collection = $this->getCollection($id);

        $form = $this->createFormBuilder($collection)
                    ->add('name', 'text')
                    ->add('updateCollection', 'submit')
                    ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($collection);
            $em->flush();
            return $this->redirect($this->generateUrl('show_collection', array(
                'id' => $collection->getId(),
            )));
        }

        return array(
            'form'          => $form->createView(),
            'collection'    => $collection,
        );
    }

    /**
     * @Route("/delete/{id}", name="collections_delete", requirements={"id"="\d+"})
     * @Method({"DELETE"})
     */
    public function deleteAction($id)
    {
        $request = $this->get('request');
        $collection = $this->getCollection($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($collection);
        $em->flush();

        return $this->redirect($this->generateUrl('collections_index'));
    }
}
