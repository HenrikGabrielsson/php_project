<?php

namespace controller;

require_once("./view/SearchView.php");
require_once("./model/Search.php");

require_once("./controller/IMainContentController.php");

class SearchController implements IMainContentController
{
	private $searchView;
	private $search;

	public function __construct()
	{
		$this->searchView = new \view\SearchView();
		$this->search = new \model\Search();
	}

	/**
	*	Hämtar innehållet som ska visas och fyller htmlViewn med det.
	*/
	public function getBody()
	{
		//hämtar in sökord från viewn och ber modellen hämta alla matchande undersökningar
		$searchWords = $this->searchView->getSearchTerms();
		$polls = $this->search->getSearchResults($searchWords);

		return $this->searchView->getResults($polls);
	}

	public function getTitle()
	{
		return $this->searchView->getTitle();
	}
}