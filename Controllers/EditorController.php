<?php
    class EditorController extends Controller
    {
        public function work(array $parameters) : void
        {
            //Ověření zda je uživatel admin.
            $this->userVerify(true);
            // Hlavička stránky
            $this->head['title'] = 'Editor článků';
            // Vytvoření instance modelu
            $articleManager = new ArticleManager();
            // Příprava prázdného článku
            $article = array(
                'article_id' => '',
                'title' => '',
                'content' => '',
                'url' => '',
                'description' => '',
                'key_words' => '',
                'author' => ''
            );
            // Je odeslán formulář
            if ($_POST)
                {
                // Získání článku z $_POST
                $keys = array('title', 'content', 'url', 'description', 'key_words', 'author');
                $article = array_intersect_key($_POST, array_flip($keys));
                
                // Uložení článku do DB
                try{
                    $articleManager->saveArticle((int)$_POST['article_id'], $article);
                    $this->addMessage('Článek byl úspěšně uložen.');
                    $this->redirect('article/' . $article['url']);
                }catch(UserException $error){
                    $this->addMessage("Nepodařilo se uložit článek.");
                    $article["article_id"]="";
                }
                
            }
            // Je zadané URL článku k editaci
            else if (!empty($parameters[0]))
            {
                $loadedArticle = $articleManager->getArticle($parameters[0]);
                if ($loadedArticle)
                    $article = $loadedArticle;
                else
                    $this->addMessage('Článek nebyl nalezen');
            }
            $this->data['article'] = $article;
            $this->view = 'editor';
        }
    }