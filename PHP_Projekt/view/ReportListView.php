<?php

namespace view;

require_once("./view/helpers/GetHandler.php");
require_once("./view/helpers/PostHandler.php");

class ReportListView
{
	public function getListRequest()
	{
		return $_GET[helpers\GetHandler::$LIST];
	}

	public function getPollToDelete()
	{
		if(isset($_POST[helpers\PostHandler::$DELETEPOLL_POLLID]))
		{
			return $_POST[helpers\PostHandler::$DELETEPOLL_POLLID];	
		}
		return false;
		
	}

	public function getDeletePollReason()
	{
		return $_POST[helpers\PostHandler::$DELETEPOLL_REASON];
	}

	public function getDeleteCommentReason()
	{
		return $_POST[helpers\PostHandler::$DELETECOMMENT_REASON];
	}

	public function getCommentToDelete()
	{
		if(isset($_POST[helpers\PostHandler::$DELETECOMMENT_COMMENTID]))
		{
			return $_POST[helpers\PostHandler::$DELETECOMMENT_COMMENTID];	
		}
		return false;
		
	}

	public function getIgnorePollReport()
	{
		if(isset($_GET[helpers\GetHandler::$IGNOREPOLL]))
		{
			return $_GET[helpers\GetHandler::$IGNOREPOLL];	
		}
		return false;		
	}

	public function getIgnoreCommentReport()
	{
		if(isset($_GET[helpers\GetHandler::$IGNORECOMMENT]))
		{
			return $_GET[helpers\GetHandler::$IGNORECOMMENT];	
		}
		return false;		
	}

	public function getTitle()
	{
		return "Reported things";
	}

	public function denyPage()
	{
		return 
		"<h1>Access Denied</h1>
		<p>We're Sorry. You're not allowed to visit this page.Go play somewhere else.</p>
		";
	}

	public function getContentHead()
	{
		return 
		'<h1>The Reports Lists</h1>
		<ul>
			<li><a href="?'.helpers\GetHandler::$VIEW.'='.helpers\GetHandler::$VIEWREPORT.'&'.helpers\GetHandler::$LIST.'='.helpers\GetHandler::$USERLIST.'">Reported Users</a></li>
			<li><a href="?'.helpers\GetHandler::$VIEW.'='.helpers\GetHandler::$VIEWREPORT.'&'.helpers\GetHandler::$LIST.'='.helpers\GetHandler::$POLLLIST.'">Reported Polls</a></li>
			<li><a href="?'.helpers\GetHandler::$VIEW.'='.helpers\GetHandler::$VIEWREPORT.'&'.helpers\GetHandler::$LIST.'='.helpers\GetHandler::$COMMENTLIST.'">Reported Comments</a></li>
		</ul>';
	}

	public function getPollList($polls, $users, $reports)
	{

		$table = '<table>
			<tr>
				<th>Remove report</th> <th>Poll</th> <th>Reason</th> <th>Creator</th> <th>Delete</th>
			</tr>';

		foreach ($reports as $report) 
		{
			//först hämtar vi den poll som rapporteringen gäller
			$thisPoll;
			foreach($polls as $poll)
			{
				if($poll->getId() == $report->getPollId())
				{
					$thisPoll = $poll;
					break;
				}
			}

			//sen hämtar vi användaren som skapat den.
			$thisUser;
			foreach($users as $user)
			{
				if($user->getId() == $report->getUserId())
				{
					$thisUser = $user;
					break;
				}
			}

			//här lägger vi till nästa rad i tabellen.
			$table .= 
			'<tr>
				<td><a href="'.$_SERVER['REQUEST_URI'].'&'.helpers\GetHandler::$IGNOREPOLL.'='.$report->getId().'">Ignore this report</a></td> <td>'.$thisPoll->getQuestion().'</td> <td>'.$report->getCommentFromReporter().'</td> <td>'.$thisUser->getUserName().'</td> 
				<td>
					<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
						<input type="hidden" value='.$thisPoll->getId().' name="'.helpers\PostHandler::$DELETEPOLL_POLLID.'">
						<input type="text" placeholder="Why are you deleting this?." name="'.helpers\PostHandler::$DELETEPOLL_REASON.'">
						<input type="submit" value="Delete">
					</form>
				</td>
			</tr>';

		}


		$table .= '</table>';

		$bodyContent = 
		'<h2>All reported polls</h2>
		<p>There are currently '.count($polls).' polls that has been reported as offensive.</p>
		'.$table;

		return $this->getContentHead().$bodyContent;
	}

	public function getCommentList($comments, $users, $reports)
	{

		$table = '<table>
			<tr>
				<th>Remove report</th> <th>Comment</th> <th>Reason</th> <th>CommentWriter</th> <th>Delete</th>
			</tr>';

		foreach ($reports as $report) 
		{
			//först hämtar vi den comment som rapporteringen gäller
			$thisComment;
			foreach($comments as $comment)
			{
				if($comment->getId() == $report->getCommentId())
				{
					$thisComment = $comment;
					break;
				}
			}

			//sen hämtar vi användaren som skapat den.
			$thisUser;
			foreach($users as $user)
			{
				if($user->getId() == $report->getUserId())
				{
					$thisUser = $user;
					break;
				}
			}

			//här lägger vi till nästa rad i tabellen.
			$table .= 
			'<tr>
				<td><a href="'.$_SERVER['REQUEST_URI'].'&'.helpers\GetHandler::$IGNORECOMMENT.'='.$report->getId().'">Ignore this report</a></td><td>'.$thisComment->getComment().'</td> <td>'.$report->getCommentFromReporter().'</td> <td>'.$thisUser->getUserName().'</td> 
				<td>
					<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
						<input type="hidden" value='.$thisComment->getId().' name="'.helpers\PostHandler::$DELETECOMMENT_COMMENTID.'">
						<input type="text" placeholder="Why are you deleting this?." name="'.helpers\PostHandler::$DELETECOMMENT_REASON.'">
						<input type="submit" value="Delete">
					</form>
				</td>
			</tr>';

		}


		$table .= '</table>';

		$bodyContent = 
		'<h2>All reported comments</h2>
		<p>There are currently '.count($comments).' comments that has been reported as offensive.</p>
		'.$table;
		
		return $this->getContentHead().$bodyContent;
	}

	public function getUserList($users, $userReports)
	{
		$reportedTable = 
		'<h2>Users nominated for deletion</h2>
		<tr>
			<th>Remove report</th> <th>User</th> <th>Reason</th> <th>Type</th> <th>Delete</th>
		</tr>
		';


		$nominatedTable = 
		'<h2>Users with 1 or more reports</h2>
		<table>
		<tr>
			<th>Remove report</th> <th>User</th> <th>Reason</th> <th>Type</th> <th>Delete</th>
		</tr>
		';


		foreach($userReports as $report)
		{
			//detta är de som ännu inte har fått en nominering att bli borttagna.
			if(is_null($report->getNomination))
			{
				
			}
			else
			{
			}
		}

		return $this->getContentHead();
	}

	
}