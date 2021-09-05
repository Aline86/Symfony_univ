<?php


namespace App\Security;

use App\Entity\User;
use App\Entity\Article;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use App\Controller\SecurityController;

class ArticleVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return $subject instanceof Article && in_array($attribute, ['view', 'edit']);
    }

    protected function voteOnAttribute(string $attribute, $article, TokenInterface $token)
    {
        if('view' === $attribute && $article instanceof Article && $article->getPublished())
        {
            return true;
        }

        $userId = $token->getUser()->getId();
        $owner = $article->getUser();
        if('edit' === $attribute && $owner instanceof User && $userId === $owner->getId())
        {
            return true;
        }


        return false;
    }
}