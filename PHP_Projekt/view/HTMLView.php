<?php

namespace view;

class HTMLView 
{
    
    
    //funktion som visar innehållet på sidan. tar emot titeln och bodyn till sidan som ska visas
    public function showHTML()
    {
        echo 
        '
        <!doctype html>
        <html>
            <head>
                <title>'.$title.'</title>
                <meta charset="utf-8" />
            </head>
            
            <body>
                '.$body.'
            </body>
        </html>
        ';
    }
    
}