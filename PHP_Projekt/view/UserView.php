<?php

namespace view;

class UserView
{
	private $user;
	private $ownPolls;

	private $pollsCommentedIn;
	private $comments;

	public function __construct($user)
	{
		$this->user = $user;
	}

	/**
	*@return string 	sidans title.
	*/
	public function getTitle()
	{
		return $this->user->getUserName();
	}


	/**
	*@param  	array 		egna skapade undersökningar
	*@param  	array 		undersökningar som användaren har kommenterat i 
	*@param 	array 		egna kommentarer
	*@return 	string 		sidans content.
	*/
	public function getBody($ownPolls, $pollsCommentedIn, $comments)
	{

		$this->ownPolls = $ownPolls;
		$this->comments = $comments;
		$this->pollsCommentedIn = $pollsCommentedIn;

		$content = 
		'<h1>'.$this->user->getUserName().'</h2>'
		.$this->getPollsList()
		.$this->getCommentsList();
		return $content;
	}

	/**
	*	@return string 	Hämtar listan med undersökningar som användaren skapat. 
	*/
	public function getPollsList()
	{
		$pollList = 
		'<h2>Created polls</h2>
		<ul>';

		if($this->ownPolls)
		{
			foreach ($this->ownPolls as $poll) 
			{
				$pollList .= 
				'<li>
					<a href="?'.helpers\GetHandler::$VIEW.'='.helpers\GetHandler::$VIEWPOLL.'&'.helpers\GetHandler::$ID.'='.$poll->getId().'">
					'.$poll->getQuestion().'</a>
				</li>';
			}
		}

		return $pollList . '</ul>';
	}

	/**
	*	@return string 	Hämtar listan med comments som användaren skrivit. 
	*/
	public function getCommentsList()
	{
		$commentList = 
		'<h2>Written comments</h2>
		<ul>';

		if($this->comments)
		{
			foreach ($this->comments as $comment) 
			{
				//hämta den poll som kommentaren ligger i.
				foreach($this->pollsCommentedIn as $poll)
				{
					if($poll && $poll->getId() === $comment->getPollId())
					{
						$thisPoll = $poll;
						break;	
					}
				}
				$commentList .=
				'<li>In <a href="?'.helpers\GetHandler::$VIEW.'='.helpers\GetHandler::$VIEWPOLL.'&'.helpers\GetHandler::$ID.'='.$thisPoll->getId().'">'.$thisPoll->getQuestion().'</a>
					<p>At '.$comment->getCommentTime(). $this->user->getUserName().' wrote:</p>
					<p>'.$comment->getComment().'</p>
				</li>'; 

			}
		}

		return $commentList.'</ul>';
	}
}