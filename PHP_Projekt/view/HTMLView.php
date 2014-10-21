<?php

namespace view;

require_once("./view/helpers/PostHandler.php");
require_once("./view/helpers/GetHandler.php");

class HTMLView 
{
    private $loginBox;
    private $sidebarContent;
    
    /**
    *   Funktion som skriver ut HTML på sidan efter att andra controllers har lagt till innehåll i den.
    *   
    *   @param  title   Det som ska stå innanför title-taggarna
    *   @param  body    Det som ska stå på sidans maincontent      
    */
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

    /**
    *   Funktion som bestämmer hur inloggningsområdet ska se ut
    *   @param loginBox     html-sträng. Login-rutan
    */
    public function setLoginBox($loginBox)
    {
        $this->loginBox = $loginBox;
    }

    /**
    *   Funktion som bestämmer hur sidofältet ska se ut
    *   @param sidebarContent     HTML-sträng. Sidofältet
    */
    public function setSidebarContent($sidebarContent)
    {
        $this->sidebarContent = $sidebarContent;
    }

    /**
    *   Skapar en generisk Error-page till html-sidan
    *   Kallar på ShowHTML för att lägga till innnehåll och titel på den färdiga mallen.
    */
    public function showErrorPage()
    {
        $title = "We couldn't find the page!";
        $body = 
        '<h1>Strange...</h1>
        <p>We couldn\'t find the page you were looking for. Check the URL for errors and try again. It\'s also possible that the page has been deleted. Sorry about that.
        </p>
        ';

        $this->showHTML($title, $body);
    }
}