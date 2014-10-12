<?php

namespace view;

require_once("./view/helpers/PostHandler.php");

class SearchView
{
	public function __construct()
	{

	}

	public function getTitle()
	{
		return "SearchResults";
	}

	public function getResults($polls)
	{
		$resultList = 
		'<h1>Search Results for "'.implode(" ",$this->getSearchTerms()).'".</h1>
		<p>We found '.count($polls).' poll that matched your search.</p>';

		foreach($polls as $poll)
		{
			$resultList .= 
			'<li><a href="?'.helpers\GetHandler::$VIEW.'='.helpers\GetHandler::$VIEWPOLL.'&'.helpers\GetHandler::$ID.'='.$poll->getId().'">'
			.$poll->getQuestion().
			'</a></li>';
		}

		return $resultList . "</ul>";
	}

	public function getSearchTerms()
	{
		return explode(" ", $_GET[helpers\GetHandler::$SEARCHWORDS]);
	}
}