<?php

namespace Eventual\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Eventual\Bundle\Entity\EventCollection;
use Eventual\Bundle\Entity\Event;

class ApiController extends CollectionAware
{
    /**
     * Verifies the request signature and throws a 403 error if it's not valid
     *
     * @param Request $request
     * @param EventCollection|Event $item The requested item
     * @throws AccessDeniedHttpException
     */
    private function verifyRequestSignature(Request $request, $item)
    {
        $col = ($item instanceof EventCollection) ? $item : $item->getCollection();
        $timestamp = (int)$request->query->get('timestamp');
        $timediff = time() - $timestamp;
        $signatureHash = hash(
            'sha512',
            sprintf('%s#%d#%s', $col->getPubKey(), $timestamp, $col->getPrivateKey())
        );

        if (!($col->getPubKey() == $request->query->get('api_key')
            && $timediff <= 300 && $timediff >= 0
            && $signatureHash == $request->query->get('signature'))) {
            throw new AccessDeniedHttpException();
        }
    }

    /**
     * @Route("/collection/{id}", requirements={"id"="\d+"})
     * @Method({"GET"})
     */
    public function getCollectionAction($id)
    {
        $collection = $this->getCollection($id);
        $this->verifyRequestSignature($this->get('request'), $collection);

        $response = array(
            'collection'    => array(
                "id"             => $collection->getId(),
                "name"           => $collection->getName(),
                "created"        => $collection->getCreated()->getTimestamp(),
                "events"         => array(),
            ),
        );

        foreach ($collection->getEvents() as $e) {
            $response['collection']['events'][] = array(
                'id'              => $e->getId(),
                'name'            => $e->getName(),
                'place_name'      => $e->getPlaceName(),
                'description'     => $e->getDescription(),
                'date'            => $e->getDate()->getTimestamp(),
                'coordinates'     => array(
                    'lat'            => $e->getCoordsLat(),
                    'long'           => $e->getCoordsLong(),
                ),
            );
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/event/{id}", requirements={"id"="\d+"})
     * @Method({"GET"})
     */
    public function getEventAction($id)
    {
        $e = $this->getEvent($id);
        $this->verifyRequestSignature($this->get('request'), $e);

        return new JsonResponse(array(
            'id'              => $e->getId(),
            'name'            => $e->getName(),
            'place_name'      => $e->getPlaceName(),
            'description'     => $e->getDescription(),
            'date'            => $e->getDate()->getTimestamp(),
            'coordinates'     => array(
                'lat'            => $e->getCoordsLat(),
                'long'           => $e->getCoordsLong(),
            ),
        ));
    }
}
