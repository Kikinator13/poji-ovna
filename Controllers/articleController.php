<?php
    class ArticleController extends Controller
    {
        public function work(array $parameters) : void
        {
            //Vytvoření instance modelu, který nám umožní pracovat s články
            $articleManager = new ArticleManager();
            $userManager = new UserManager();
            $user = $userManager->getUser();
            $this->data['admin'] = $user && $user['admin'];
            // Je zadáno URL článku ke smazání
            if (!empty($parameters[1]) && $parameters[1] == 'delete')
            {
                echo $parameters[0];
                $this->userVerify(true);
                $articleManager->deleteArticle($parameters[0]);
                $this->addMessage('Článek byl úspěšně odstraněn');
                $this->redirect('article');
            }
            // Je zadáno URL článku
            else if (!empty($parameters[0]))
            {
                
                // Získání článku podle URL
                $article = $articleManager->getArticle($parameters[0]);
                // Pokud nebyl článek s danou URL nalezen, přesměrujeme na ChybaKontroler
                if (!$article)
                    $this->redirect('error');

                // Hlavička stránky
                $this->head = array(
                    'title' => $article['title'],
                    'keyWords' => $article['key_words'],
                    'description' => $article['description'],
                );

                // Naplnění proměnných pro šablonu
                $this->data['title'] = $article['title'];
                $this->data['content'] = $article['content'];
                $this->data['author'] = $article['author'];
                $this->data['date'] = $article['date'];

                // Nastavení šablony
                $this->view = 'article';
            }else{
                $articles = $articleManager->getArticles();
                $this->data['articles'] = $articles;
                $this->view = 'articles';
            }
        }
    }