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

	public function getCommentToDelete()
	{
		if(isset($_POST[helpers\PostHandler::$DELETECOMMENT_COMMENTID]))
		{
			return $_POST[helpers\PostHandler::$DELETECOMMENT_COMMENTID];	
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

	public function getPollList($polls, $users, $reports)
	{

		$table = '<table>
			<tr>
				<th>Poll</th> <th>Reason</th> <th>Creator</th> <th>Delete</th>
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
				<td>'.$thisPoll->getQuestion().'</td> <td>'.$report->getCommentFromReporter().'</td> <td>'.$thisUser->getUserName().'</td> 
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
		'<h1>All reported polls</h1>
		<p>There are currently '.count($polls).' polls that has been reported as offensive.</p>
		'.$table;

		return $bodyContent;
	}

	public function getCommentList($comments, $users, $reports)
	{

		$table = '<table>
			<tr>
				<th>Comment</th> <th>Reason</th> <th>CommentWriter</th> <th>Delete</th>
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
				<td>'.$thisComment->getComment().'</td> <td>'.$report->getCommentFromReporter().'</td> <td>'.$thisUser->getUserName().'</td> 
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
		'<h1>All reported comments</h1>
		<p>There are currently '.count($comments).' comments that has been reported as offensive.</p>
		'.$table;
		
		return $bodyContent;
	}

	public function getUserList()
	{
		return "userList";
	}

	
}