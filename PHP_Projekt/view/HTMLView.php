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
                <title>'.$title.' - Polls n\' Shit</title>
                <meta charset="utf-8" />
                <link href="style/style.css" rel="stylesheet" type="text/css">
            </head>
            
            <body>
                <div id="container">
                    <div id="header">
                        <div id="logo">
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
}