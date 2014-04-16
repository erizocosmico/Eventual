<?php

namespace Eventual\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
            // TODO not auth error
            die('Not authorised');
        }

        return $collection;
    }
}
