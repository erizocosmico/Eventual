<?php

namespace Eventual\Bundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Eventual\Bundle\Entity\Event;

class EventsController extends CollectionAware
{
    /**
     * @Route("/create/for_collection/{id}", name="events_create", requirements={"id"="\d+"})
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function createAction($id)
    {
        $request = $this->get('request');
        $event = new Event();
        $collection = $this->getCollection($id);
        $now = new \DateTime();

        $form = $this->createFormBuilder($event)
                    ->add('name', 'text')
                    ->add('placeName', 'text')
                    ->add('coordsLat', 'number')
                    ->add('coordsLong', 'number')
                    ->add('description', 'textarea')
                    ->add('date', 'datetime', array(
                        'data'     => $now,
                        'years'    => range((int)$now->format('Y'), (int)$now->format('Y')+10),
                    ))
                    ->add('createEvent', 'submit')
                    ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $event->setCollection($collection);
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();
            return $this->redirect($this->generateUrl('show_event', array(
                'id' => $event->getId(),
            )));
        }

        return array(
            'form'              => $form->createView(),
            'collection'        => $collection,
        );
    }

    public function getEvent($id)
    {
        $event = $this->getDoctrine()
            ->getRepository('Eventual:Event')
            ->find($id);

        if (!$event) {
            throw $this->createNotFoundException();
        }

        if ($this->getUser()->getId() != $event->getCollection()->getUser()->getId()) {
            // TODO not auth error
            die('Not authorised');
        }

        return $event;
    }

    /**
     * @Route("/show/{id}", name="show_event", requirements={"id"="\d+"})
     * @Method({"GET"})
     * @Template()
     */
    public function showAction($id)
    {
        $form = $this->createFormBuilder(array())
                    ->add("remove_event", "submit")
                    ->getForm();

        return array(
            'event'      => $this->getEvent($id),
            'form'       => $form->createView(),
        );
    }

    /**
     * @Route("/update/{id}", name="events_update", requirements={"id"="\d+"})
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function updateAction($id)
    {
        $request = $this->get('request');
        $event = $this->getEvent($id);

        $form = $this->createFormBuilder($collection)
                    ->add('name', 'text')
                    ->add('update_event', 'submit')
                    ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();
            return $this->redirect($this->generateUrl('show_event', array(
                'id' => $event->getId(),
            )));
        }

        return array(
            'form'          => $form->createView(),
            'event'         => $event,
        );
    }

    /**
     * @Route("/delete/{id}", name="events_delete", requirements={"id"="\d+"})
     * @Method({"DELETE"})
     */
    public function deleteAction($id)
    {
        $request = $this->get('request');
        $event = $this->getEvent($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($event);
        $em->flush();

        return $this->redirect($this->generateUrl('collections_index'));
    }
}
