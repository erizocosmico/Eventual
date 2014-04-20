<?php

namespace Eventual\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class HomeController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction()
    {
        // If the user is logged in redirect to collections index
        if (!is_null($this->getUser())) {
            return $this->redirect($this->generateUrl('collections_index'));
        }

        return $this->render('Eventual::landing.html.twig');
    }
}
