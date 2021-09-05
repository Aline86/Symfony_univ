<?php


namespace App\TestServices;

use App\Controller\SecurityController;
use App\Entity\Article;
use Doctrine\Common\EventSubscriber;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;



class SaveUser implements EventSubscriber
{
    private $token;

    public function __construct(TokenStorageInterface $token)
    {
        $this->token = $token;
    }

    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
        );
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if(!$entity instanceof Article)
        {
            return;
        }
        $entity->setUser($this->token->getToken()->getUser());

    }
}