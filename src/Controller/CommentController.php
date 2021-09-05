<?php


namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\CommentaireType;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CommentController
 * @package App\Controller
 */
class CommentController extends AbstractController
{
    /**
     * @Route("/comment/add/{id}/{_locale}",
     * name = "add_comment")
     * defaults={"_locale": "en_EN"},
     * requirements={"page":"\d+",
     *"_locale": "en|fr"},
     * @param Session $session
     * @param $id
     * @param Request $request
     * @param $_locale
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function addComment(Session $session, $id, Request $request, $_locale, TranslatorInterface $translator)
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
        $article = $this->getDoctrine()->getRepository(Article::class)->findOneBy(['id' => $id]);

        // Nous créons l'instance de "Commentaires"
        $commentaire = new Comment();

        // Nous créons le formulaire en utilisant "CommentairesType" et on lui passe l'instance
        $form = $this->createForm(CommentType::class, $commentaire);

        $send = $translator->trans('Send');
        $form->add('send', SubmitType::class, ['label' => $send]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hydrate notre commentaire avec l'article
            $commentaire->setComment($article);

            // Hydrate notre commentaire avec la date et l'heure courants
            $commentaire->setCreatedAt(new \DateTime('now'));

            $doctrine = $this->getDoctrine()->getManager();

            // On hydrate notre instance $commentaire
            $doctrine->persist($commentaire);

            // On écrit en base de données
            $doctrine->flush();

            // On redirige l'utilisateur
            return $this->redirectToRoute('show_article', ['id' => $id]);
        }

        return $this->render('article/comment.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}