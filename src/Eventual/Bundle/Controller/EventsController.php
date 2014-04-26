<?php

namespace Eventual\Bundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Eventual\Bundle\Entity\Event;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Form\FormError;

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
                    ->add('coordinates', 'text', array(
                        'mapped'         => false,
                    ))
                    ->add('description', 'textarea')
                    ->add('date', 'datetime', array(
                        'data'     => $now,
                        'years'    => range((int)$now->format('Y'), (int)$now->format('Y')+10),
                    ))
                    ->add('createEvent', 'submit')
                    ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->validateCoordinates($form);
        }

        if ($form->isValid()) {
            list($lat, $long) = array_map(function ($coord) {
                return (float)trim($coord);
            }, split(',', $form['coordinates']->getData()));
            $event->setCoordsLat($lat);
            $event->setCoordsLong($long);
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
                    ->add('coordinates', 'text', array(
                        'mapped'         => false,
                    ))
                    ->add('description', 'textarea')
                    ->add('date', 'datetime', array(
                        'data'     => $event->getDate(),
                        'years'    => range((int)$now->format('Y'), (int)$now->format('Y')+10),
                    ))
                    ->add('updateEvent', 'submit')
                    ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->validateCoordinates($form);
        } else {
            $form->get('coordinates')->setData($event->getCoordsLat() . ', ' . $event->getCoordsLong());
        }

        if ($form->isValid()) {
            list($lat, $long) = array_map(function ($coord) {
                return (float)trim($coord);
            }, split(',', $form['coordinates']->getData()));
            $event->setCoordsLat($lat);
            $event->setCoordsLong($long);
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

    private function validateCoordinates($form)
    {
        $valid = true;
        if ($form->get('coordinates')->getData()) {
            $data = $form->get('coordinates')->getData();
            $dataParts = split(',', $data);

            if (count($dataParts) == 2) {
                foreach ($dataParts as $i => $coord) {
                    $coord = (float) trim($coord);
                    $i++;
                    $valid &= $coord >= (-$i*90) && $coord <= ($i*90);
                }
            } else {
                $valid = false;
            }
        } else {
            $valid = false;
        }

        if (!$valid) {
            $form->get('coordinates')->addError(new FormError('Coordinates are not valid.'));
        }
    }

    /**
     * @Route("/{id}/for_day/{day}/{page}", name="show_day", requirements={"id"="\d+", "day"="\d{4}-\d{2}-\d{2}", "page"="\d+"}, defaults={"page"=1})
     * @Method({"GET"})
     * @Template()
     */
    public function eventsForDayAction($id, $day, $page)
    {
        $eventsRepository = $this->getDoctrine()
            ->getRepository('Eventual:Event');

        $count = $eventsRepository->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->where('abs(date_diff(e.date, :evdate)) = 0')
            ->setParameter('evdate', $day)
            ->getQuery()
            ->getSingleScalarResult();

        $page = ($page-1*25 > $count) ? 1 : $page;

        $events = $eventsRepository->createQueryBuilder('e')
            ->where('abs(date_diff(e.date, :evdate)) = 0 AND e.collection = :col')
            ->setParameter('evdate', $day)
            ->setParameter('col', $id)
            ->orderBy('e.date', 'ASC')
            ->setMaxResults(25)
            ->setFirstResult(($page-1)*25)
            ->getQuery()
            ->getResult();

        return array(
            'collection'               => $this->getCollection($id),
            'total_pages'              => ceil($count/25),
            'events'                   => $events,
            'count'                    => count($events),
            'page'                     => $page,
            'day'                      => $day,
        );
    }
}
