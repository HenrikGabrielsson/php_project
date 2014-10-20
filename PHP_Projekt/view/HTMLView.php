<?php

namespace view;

class HTMLView 
{
    private $loginBox;
    private $sidebarContent;
    
    //funktion som visar innehållet på sidan. tar emot titeln och bodyn till sidan som ska visas
    public function showHTML($title, $body)
    {
        echo 
        '
        <!doctype html>
        <html>
            <head>
                <title>'.$title.' - PHP Polls</title>
                <meta charset="utf-8" />
                <link href="style/style.css" rel="stylesheet" type="text/css">
            </head>
            
            <body>
                <div id="container">
                    <div id="header">
                        <div id="logo">
                            <a href="."><img src="image/logo.png" alt="Welcome to PHP Polls"></a>
                        </div>
                        <div id="search">
                            <form method="get">
                                <input type="hidden"  name="'.helpers\GetHandler::$VIEW.'" value="'.helpers\GetHandler::$VIEWSEARCH.'" >
                                
                                <input type="text" id="search" placeholder="search" name="'.helpers\GetHandler::$SEARCHWORDS.'">
                                <input type="submit" value="Search">

                            </form>
                        </div>

                        '.$this->loginBox.'
                        
                    </div>

                    <div id="content">

                        <div id="sidebar">
                            '.$this->sidebarContent.'

                        </div>
                        <div id="main_content">
                            '.$body.'
                        </div>
                    </div>
                    
                    
                    <div id="footer">
                    </div>
                </div>
                <script src="script/pollScript.js" type="text/javascript" ></script>
            </body>
        </html>
        ';
    }

    public function setLoginBox($loginBox)
    {
        $this->loginBox = $loginBox;
    }

    public function setSidebarContent($sidebarContent)
    {
        $this->sidebarContent = $sidebarContent;
    }

    public function showErrorPage()
    {
        $title = "We couldn't the page!";
        $body = 
        '<h1>Strange...</h1>
        <p>We couldn\'t find the page you were looking for. Check the URL for errors and try again. It\'s also possible that the page has been deleted. Sorry about that.
        </p>
        ';

        $this->showHTML($title, $body);
    }
}