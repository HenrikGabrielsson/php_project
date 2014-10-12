<?php

namespace view;

class UserView
{
	private $user;
	private $polls;
	private $comments;

	public function __construct($user, $polls, $comments)
	{
		$this->user = $user;
		$this->polls = $polls;
		$this->comments = $comments;
	}

	public function getTitle()
	{
		return $this->user->getUserName();
	}

	public function getBody()
	{
		$content = 
		'<h1>'.$this->user->getUserName().'</h2>'
		.$this->getPollsList()
		.$this->getCommentsList();

		return $content;
	}

	public function getPollsList()
	{
		$pollList = 
		'<h2>Created polls</h2>
		<ul>';

		foreach ($this->polls as $poll) {
			$pollList .= 
			'<li>
				<a href="?'.helpers\GetHandler::$VIEW.'='.helpers\GetHandler::$VIEWPOLL.'&'.helpers\GetHandler::$ID.'='.$poll->getId().'">
				'.$poll->getQuestion().'</a>
			</li>';
		}

		return $pollList . '</ul>';
	}

	public function getCommentsList()
	{
		$commentList = 
		'<h2>Written comments</h2>
		<ul>';

		foreach ($this->comments as $comment) {
			$commentList .=
			'<li>In <a href="?'.helpers\GetHandler::$VIEW.'='.helpers\GetHandler::$VIEWPOLL.'&'.helpers\GetHandler::$ID.'='.$comment->getPollId().'">this poll</a>
			<p>At '.$comment->getCommentTime(). $this->user->getUserName().' wrote:</p>

			<p>'.$comment->getComment().'</p>

			</li>'; 

		}

		return $commentList.'</ul>';
	}
}