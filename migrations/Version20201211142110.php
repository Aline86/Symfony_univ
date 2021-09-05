<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201211142110 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
        $this->addSql('DROP INDEX IDX_243A1ACA7294869C');
        $this->addSql('DROP INDEX IDX_243A1ACA12469DE2');
        $this->addSql('CREATE TEMPORARY TABLE __temp__asso_article_category AS SELECT article_id, category_id FROM asso_article_category');
        $this->addSql('DROP TABLE asso_article_category');
        $this->addSql('CREATE TABLE asso_article_category (article_id INTEGER NOT NULL, category_id INTEGER NOT NULL, PRIMARY KEY(article_id, category_id), CONSTRAINT FK_243A1ACA7294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_243A1ACA12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO asso_article_category (article_id, category_id) SELECT article_id, category_id FROM __temp__asso_article_category');
        $this->addSql('DROP TABLE __temp__asso_article_category');
        $this->addSql('CREATE INDEX IDX_243A1ACA7294869C ON asso_article_category (article_id)');
        $this->addSql('CREATE INDEX IDX_243A1ACA12469DE2 ON asso_article_category (category_id)');
        $this->addSql('DROP INDEX IDX_9474526CF8697D13');
        $this->addSql('CREATE TEMPORARY TABLE __temp__comment AS SELECT id, comment_id, title, author, created_at, message FROM comment');
        $this->addSql('DROP TABLE comment');
        $this->addSql('CREATE TABLE comment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, comment_id INTEGER NOT NULL, title VARCHAR(255) NOT NULL COLLATE BINARY, author VARCHAR(255) NOT NULL COLLATE BINARY, created_at DATETIME NOT NULL, message CLOB NOT NULL COLLATE BINARY, CONSTRAINT FK_9474526CF8697D13 FOREIGN KEY (comment_id) REFERENCES article (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO comment (id, comment_id, title, author, created_at, message) SELECT id, comment_id, title, author, created_at, message FROM __temp__comment');
        $this->addSql('DROP TABLE __temp__comment');
        $this->addSql('CREATE INDEX IDX_9474526CF8697D13 ON comment (comment_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP INDEX IDX_243A1ACA7294869C');
        $this->addSql('DROP INDEX IDX_243A1ACA12469DE2');
        $this->addSql('CREATE TEMPORARY TABLE __temp__asso_article_category AS SELECT article_id, category_id FROM asso_article_category');
        $this->addSql('DROP TABLE asso_article_category');
        $this->addSql('CREATE TABLE asso_article_category (article_id INTEGER NOT NULL, category_id INTEGER NOT NULL, PRIMARY KEY(article_id, category_id))');
        $this->addSql('INSERT INTO asso_article_category (article_id, category_id) SELECT article_id, category_id FROM __temp__asso_article_category');
        $this->addSql('DROP TABLE __temp__asso_article_category');
        $this->addSql('CREATE INDEX IDX_243A1ACA7294869C ON asso_article_category (article_id)');
        $this->addSql('CREATE INDEX IDX_243A1ACA12469DE2 ON asso_article_category (category_id)');
        $this->addSql('DROP INDEX IDX_9474526CF8697D13');
        $this->addSql('CREATE TEMPORARY TABLE __temp__comment AS SELECT id, comment_id, title, author, created_at, message FROM comment');
        $this->addSql('DROP TABLE comment');
        $this->addSql('CREATE TABLE comment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, comment_id INTEGER NOT NULL, title VARCHAR(255) NOT NULL, author VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, message CLOB NOT NULL)');
        $this->addSql('INSERT INTO comment (id, comment_id, title, author, created_at, message) SELECT id, comment_id, title, author, created_at, message FROM __temp__comment');
        $this->addSql('DROP TABLE __temp__comment');
        $this->addSql('CREATE INDEX IDX_9474526CF8697D13 ON comment (comment_id)');
    }
}
