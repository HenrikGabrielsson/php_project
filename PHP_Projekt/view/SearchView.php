<?php

namespace view;

class SearchView
{

	/**
	*@return sidans title.
	*/
	public function getTitle()
	{
		return "SearchResults";
	}

	/**
	* 	visar en lista över polls som matchade en sökning
	* 	@param  	array 	alla polls 
	* 	@return 	string  lista över matchande polls.
	*/
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

	/**
	* @return 	array 	alla sökord hämtas.
	*/
	public function getSearchTerms()
	{
		//separerar sökorden vid mellanslag och returnerar .
		return explode(" ", $_GET[helpers\GetHandler::$SEARCHWORDS]);
	}
}