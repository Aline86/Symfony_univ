<?php

// src/DataFixtures/AppFixtures.php
namespace App\DataFixtures;


use App\Entity\Article;
use App\Entity\Category;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $categories = [];
        for($i = 0; $i < 5; $i++){
            $category = new Category();
            $category->setName("test ".$i);
            $categories[] = $category;
            $manager->persist($category);
        }

        for( $i = 0; $i < 20; $i++ ) {
            $article = new Article();
            $article->setAuthor("Claire-Aline Haestie");
            $article->setContent("Bonjour Mesdames et Messieurs ".$i);
            $article->setCreatedAt(new \DateTime('now'));
            $article->setNbViews(5);
            $article->setTitle("Premier site Symfony 5 ".$i);
            $article->setPublished(true);
            $article->addCategory($categories[$i%5]);
            $article->addCategory($categories[($i+1)%5]);

            for( $j = 0; $j < 5; $j++ ) {
                $comment = new Comment();
                $comment->setTitle("Mon premier commentaire ".$i." ".$j);
                $comment->setAuthor("Claire-Aline Haestie");
                $comment->setCreatedAt(new \DateTime('now'));
                $comment->setMessage("C'est un message sympa");
                $comment->setComment($article);
                $manager->persist($comment);
            }

            $manager->persist($article);

        }
        $manager->flush();
    }
}
