<?php

namespace model;

class Search
{
	private $pollRepo;

	public function __construct()
	{
		$this->pollRepo = new repository\pollRepo();
	}

	/**
	* hämta sökresultat efter att ha fått sökord
	* @param 	array 		array med sökord
	* @return 	array 		array med matchande polls. 	
	*/
	public function getSearchResults($searchArr)
	{

		$allPolls = $this->pollRepo->getAllPublicPolls();
		$matchingPolls = array();

		//kolla igenom alla polls och hämta ut de som matchar
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