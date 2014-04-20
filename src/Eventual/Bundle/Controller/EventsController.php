<?php

namespace Eventual\Bundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Eventual\Bundle\Entity\Event;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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
                    ->add('coordsLat', 'number', array('precision' => 14, 'grouping' => true))
                    ->add('coordsLong', 'number', array('precision' => 14, 'grouping' => true))
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
        $now = new \DateTime();

        $form = $this->createFormBuilder($event)
                    ->add('name', 'text')
                    ->add('placeName', 'text')
                    ->add('coordsLat', 'number', array('precision' => 14, 'grouping' => true))
                    ->add('coordsLong', 'number', array('precision' => 14, 'grouping' => true))
                    ->add('description', 'textarea')
                    ->add('date', 'datetime', array(
                        'data'     => $event->getDate(),
                        'years'    => range((int)$now->format('Y'), (int)$now->format('Y')+10),
                    ))
                    ->add('updateEvent', 'submit')
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
        $collection = $event->getCollection();

        $em = $this->getDoctrine()->getManager();
        $em->remove($event);
        $em->flush();

        return $this->redirect($this->generateUrl('show_collection', array('id' => $collection->getId())));
    }
}
