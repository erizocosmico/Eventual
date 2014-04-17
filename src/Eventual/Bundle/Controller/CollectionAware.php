<?php

namespace Eventual\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CollectionAware extends Controller
{
    public function getCollection($id)
    {
        $collection = $this->getDoctrine()
            ->getRepository('Eventual:EventCollection')
            ->find($id);

        if (!$collection) {
            throw $this->createNotFoundException();
        }

        if ($this->getUser()->getId() != $collection->getUser()->getId()) {
            throw new AccessDeniedHttpException();
        }

        return $collection;
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
            throw new AccessDeniedHttpException();
        }

        return $event;
    }
}
