<?php

namespace model;

require_once("./model/Poll.php");

class Search
{
	private $pollRepo;

	public function __construct()
	{
		$this->pollRepo = new repository\pollRepo();
	}

	public function getSearchResults($searchArr)
	{
		$allPolls = $this->pollRepo->getAllPublicPolls();

		$matchingPolls = array();

		foreach ($allPolls as $poll) 
		{
			$match = false;
			foreach($searchArr as $word)
			{
				//om ordet inte finns i frågan så breakar denna loopen.
				if(stripos($poll->getQuestion(), $word) === false)
				{
					$match = false;
					break;
				}
				else
				{
					$match = true;
				}
			}

			//om ALLA orden fanns i frågan så läggs den till i listan med undersökningar som ska returneras.
			if($match)
			{
				$matchingPolls[] = $poll;
			}
		}

		return $matchingPolls;
	}
}