<?php

// Třída poskytuje metody pro správu článků v redakčním systému
class ArticleManager
{

    /**
     * Vrátí článek z databáze podle jeho URL
     */
    public function getArticle(string $url) : bool|array
    {
        return Mysql::oneRow('
            SELECT *
            FROM `articles`
            WHERE `url` = ?
        ', array($url));
    }

    /**
     * Vrátí seznam článků v databázi
     */
    public function getArticles() : bool|array
    {
        return Mysql::moreRows('
            SELECT `article_id`, `title`, `url`, `description`, `author`, `date`
            FROM `articles`
            ORDER BY `article_id` DESC
        ');
    }

    public function saveArticle(int $id, array $article) : void
    {   
        try
        {
            if (!$id)
                Mysql::insert('articles', $article);
            else
                Mysql::update('articles', $article, 'article_id = ?', array($id));
        }catch(Exception $error)
        {
            throw new UserException("Nepodařilo se uložit článek.", 3, $error);
        }
    }

    public function deleteArticle(string $url) : void
    {
        Mysql::edit('
            DELETE FROM articles
            WHERE url = ?
        ', array($url));
    }
}