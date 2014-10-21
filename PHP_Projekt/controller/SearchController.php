<?php

namespace controller;

require_once("./view/SearchView.php");
require_once("./model/Search.php");

class SearchController
{
	private $htmlView;
	private $searchView;
	private $search;

	public function __construct($htmlView)
	{
		$this->htmlView = $htmlView;
		$this->searchView = new \view\SearchView();
		$this->search = new \model\Search();
	}

	/**
	*	Hämtar innehållet som ska visas och fyller htmlViewn med det.
	*/
	public function getContent()
	{
		//hämtar in sökord från viewn och ber modellen hämta alla matchande undersökningar
		$searchWords = $this->searchView->getSearchTerms();
		$polls = $this->search->getSearchResults($searchWords);

		$title = $this->searchView->getTitle();
		$body = $this->searchView->getResults($polls);

		$this->htmlView->showHTML($title, $body);
	}
}