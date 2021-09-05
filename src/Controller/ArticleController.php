<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Form\CategoryType;
use App\TestServices\Spam;
use App\TestServices\Logger;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ArticleType;
use App\Security\ArticleVoter;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\EventSubscriber\LocaleSubscriber;
/**
 * Class ArticleController
 * @package App\Controller
 *
 * @Route("/blog")
 */
class ArticleController extends AbstractController
{
    // Méthode de gestion des sessions
    public function sessionStart($_locale, $request)
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
    }
    /**
     * @Route("/{_locale}",
     * format="html",
     * name = "blog",
     * requirements={
     *"_locale": "en|fr",
     *"_format": "html|xml"})
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function index(Request $request, TranslatorInterface $translator, $_locale){
        $this->sessionStart($_locale, $request);

        // Traduction avec variable
        $logout = $translator->trans('Logout');
        $logged = $translator->trans('You are logged in as');
        return $this->render('blog.html.twig',['logout' => $logout, 'logged' => $logged]);
    }

    /**
     * @Route("/list/{page}/{_locale}",
     * defaults={"page": "1", "_locale": "en_EN"},
     * requirements={"page":"\d+",
     *"_locale": "en|fr"},
     * methods={"GET"},
     * name="show_list")
     * @param $page
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param $_locale
     * @return Response
     */
    public function listAction($page, Request $request, TranslatorInterface $translator, $_locale): Response
    {
        // Exception pour les pages négatives
        if($page < 1)
        {
            throw $this->createNotFoundException("Erreur 404");
        }

        // Reprise de la méthode pour les sessions
        $this->sessionStart($_locale, $request);

        // Gestion de la pagination
        $em = $this->getDoctrine()->getManager();
        $nbPerPage = 4;
        $articles = $em->getRepository('App:Article')->myFindAllWithPaging($page, $nbPerPage);
        $nbTotalPages = intval(ceil(count($articles) / $nbPerPage));

        if($page > $nbTotalPages)
        {
            throw $this->createNotFoundException("Le numéro de page n'existe pas");
        }

        return $this->render('article/list.html.twig', ['articles' => $articles, 'total' => $nbTotalPages, 'page' => $page]);
    }

    /**
     * @Route("/article/add/{_locale}",
     * defaults={"page": "1", "_locale": "en"},
     * name = "add_article")
     * @param Session $session
     * @param Request $request
     * @param Spam $spam
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function addAction(Session $session, Request $request, Spam $spam, Logger $logger, $_locale, TranslatorInterface $translator)
    {
       // $session->getFlashBag()->add("info", "L'article a bien été ajouté");
        $this->sessionStart($_locale, $request);

        // variable déterminant l'intitulé du bouton du formulaire
        $addArticle = $translator->trans('Add a new article');

        // Instanciation de la classe Article qui va servir à la création du formulaire
        $article = new Article();
        $article->setPublished(1);
        $form = $this->createForm(ArticleType::class, $article);
        $form->add('send', SubmitType::class, ['label' => $addArticle]);
        $form->handleRequest($request);
        $add = $translator->trans('Article Created! Knowledge is power!');
        // Gestion de la soumission du formulaire
        if ($form->isSubmitted() && $form->isValid() && !$spam->isSpam($article->getContent(), $request))
        {
            // envoi du message flash
            $this->addFlash('success', $add);
            $article->setCreatedAt(new \DateTime('now')); // TODO mettre dans l'entité
            $em = $this->getDoctrine()->getManager(); // On récupère l'entity manager
            $em->persist($article); // On confie notre entité à l'entity manager (on persist l'entité)
            $em->flush();

            return $this->redirectToRoute("show_list");
        }
        else if($spam->isSpam($article->getContent(), $request))
        {
            return $this->render('article/add.html.twig', array('form' => $form->createView()));
        }
        return $this->render('article/add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/article/addcate/{_locale}",
     * defaults={"_locale": "en"},
     * name = "add_cate")
     */
    public function addCategory(Request $request, $_locale, TranslatorInterface $translator)
    {
        // Création d'une nouvelle catégorie
        $this->sessionStart($_locale, $request);
        $category = new Category();
        $addcate = $translator->trans('Add Category');
        $form = $this->createForm(CategoryType::class, $category);
        $form->add('send', SubmitType::class, ['label' => $addcate]);
        $form->handleRequest($request);
        //$add = $translator->trans('Article Created! Knowledge is power!');
        // Gestion de la soumission du formulaire
        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager(); // On récupère l'entity manager
            $em->persist($category); // On confie notre entité à l'entity manager (on persist l'entité)
            $em->flush();
            return $this->redirectToRoute("show_list");

        }
        return $this->render('category/addcate.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/article/{id}/{_locale}",
     * requirements={"id":"\d+"},
     * defaults={"_locale": "en_EN"},
     * name = "show_article")
     * @param $id
     * @param Request $request
     * @param Article $article
     * @param SecurityController $security
     * @param $_locale
     * @return Response
     */
    public function viewAction($id, Request $request, Article $article, SecurityController $security, $_locale)
    {
        $this->sessionStart($_locale, $request);
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('App:Article')->find($id);
        if (!$security->isGranted('view', $article))
        {
            return $this->render('article/view.html.twig');
        }

        if (!isset($article))
        {
            throw $this->createNotFoundException("Erreur 404");
        }
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('App:Article')->find($id);
        $comments = $this->getDoctrine()->getRepository(Comment::class)->findBy(['comment' => $id], ['created_at' => 'desc']);
        return $this->render('article/view.html.twig', array('article' => $article, 'comments' => $comments));

    }


    /**
     * @Route("/article/edit/{id}/{_locale}",
     * defaults={"_locale": "en_EN"},
     * name = "edit_article",
     * methods={"GET","POST"})
     * @param Session $session
     * @param $id
     * @param Request $request
     * @param Article $article
     *
     */
    public function editAction(Session $session, $id, Request $request, Article $article, SecurityController $security, $_locale, TranslatorInterface $translator)
    {
        $this->sessionStart($_locale, $request);

        $editArticle = $translator->trans('Edit an article');

        if (!$security->isGranted('edit', $article)) {
            if (!isset($article)) {
                throw $this->createNotFoundException("Erreur 404");
            }
            $em = $this->getDoctrine()->getManager();
            $article = $em->getRepository('App:Article')->find($id);
            $comments = $this->getDoctrine()->getRepository(Comment::class)->findBy(['comment' => $id], ['created_at' => 'desc']);
            return $this->render('article/view.html.twig', array('article' => $article, 'comments' => $comments));

        }
        $form = $this->createForm(ArticleType::class, $article);
        $form->add('send', SubmitType::class, ['label' => $editArticle]);
        $form->handleRequest($request);
        $modify = $translator->trans('Article Modified! Wonderful!');

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', $modify);
            $article->setUpdatedAt(new \DateTime('now'));
            $em = $this->getDoctrine()->getManager(); // On récupère l'entity manager
            $em->persist($article); // On confie notre entité à l'entity manager (on persist l'entité)
            $em->flush();
            return $this->redirectToRoute("show_list");
        }

        return $this->render('article/edit.html.twig', array('form' => $form->createView()));

    }
    /**
     * @Route("/category/{id}/{_locale}",
     * defaults={"page": "1", "_locale": "en_EN"},
     * name = "show_articles_cate",
     * requirements={"id":"\d+"})
     * @param $id
     * @return Response
     */
    public function showCate($id, Request $request, $_locale)
    {
        $this->sessionStart($_locale, $request);
        // Renvoie les articles en lien avec la catégorie
        if(!isset($id))
        {
            throw $this->createNotFoundException("Erreur 404");
        }
        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository('App:Article')->findArticleFromCate($id);


        return $this->render('category/category.html.twig', array('articles' => $articles));
    }
    /**
     * @Route("/article/delete/{id}",
     * name = "delete_article")
     * @param $id
     */
    public function deleteAction($id){
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('App:Article')->find($id);

        if(isset($article))
        {
            $em->remove($article);
            $em->flush();
            return $this->redirectToRoute('show_list');
        }
        throw $this->createNotFoundException("Erreur 404");
    }

    // Méthode qui retourne les 5 derniers articles et les catégories
    public function returnLastArticle($nbArticle, Request $request){
        $categories = $this->getDoctrine()->getRepository(Category::class)->findBy([],['name' => 'asc']);
        $articles = $this->getDoctrine()->getRepository(Article::class)->findLastArticle($nbArticle);
        return $this->render('last_articles.html.twig', array('articles' => $articles, 'categories' => $categories));
    }
}