<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
class SecurityController extends AbstractController
{

    /**
     * @Route("login/{_locale}", name="app_login",
     * defaults={"_locale": "en_EN"},
     * requirements={"_locale": "en|fr"})
     * @return Response
     */
    public function loginAction(Request $request, AuthenticationUtils $authUtils, $_locale)
    {
        $session=$this->get('session')->set('locale', $_locale);

        if(isset($session))
        {
            $request->setLocale($session);
        }
        else
        {
            $request->setLocale($_locale);
        }

        $key = $this->get('session')->set('connected', 'yes');

        // Récupération des erreurs s'il y en a eu lors de la précédente authentification
        $error = $authUtils->getLastAuthenticationError();
        // Login précédemment saisi par l'utilisateur
        $lastUsername = $authUtils->getLastUsername();
        return $this->render('form/form_login.html.twig', array(
            'last_username' => $lastUsername,
            'error' => $error,
        ));
    }

    /**
     * @Route("login_check", name="app_login_check")
     * On déclare une route mais l'action ne sera jamais exécutée car Symfony catche l'évènement
     * @return void
     * @throws \Exception
     */
    public function loginCheckAction()
    {
        // Non censé entrer ici car la route doit être catchée par Symfony ==> L'exception joue un rôle de garde fou
        throw new \Exception('Unexpexted loginCheck action');
    }

    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout(Request $request)
    {
        $request->getSession()->invalidate();
        // controller can be blank: it will never be executed!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}