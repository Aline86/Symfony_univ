<?php


namespace App\Controller;
use App\TestServices\Antispam;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MailController extends AbstractController
{
    /*** @Route("/", name="home")
     * @param Antispam $mailer
     * @return Response
     **/
    public function indexAction(Antispam $mailer)
    {
        $message = (new \Swift_Message('Test de mail'))
            ->setFrom('ca.haestie@gmail.com')
            ->setTo('ca.haestie@gmail.com')
            ->setBody($this->renderView('emails/test.html.twig'),
                'text/html');
        $mailer->send($message);
        return $this->render('hello_world.html.twig');
    }
}