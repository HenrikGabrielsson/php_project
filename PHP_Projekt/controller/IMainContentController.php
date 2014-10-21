<?php


namespace controller;

//controller interface för de controllrar som utför ändringar på main_content.
interface IMainContentController
{
	public function getBody();
	public function getTitle();
}