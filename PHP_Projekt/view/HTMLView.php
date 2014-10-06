<?php

namespace view;

class HTMLView 
{
    public $loginBox;
    
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

                    <div id="sidebar">
                        <div id="categoryList">

                        </div>
                    </div>

                    <div id="content">
                        '.$body.'
                    </div>
                    
                    
                    <div id="footer">
                    </div>
                </div>
            </body>
        </html>
        ';
    }

    public function setLoginBox($loginBox)
    {
        $this->loginBox = $loginBox;
    }
}