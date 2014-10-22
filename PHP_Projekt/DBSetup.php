<?php


require_once("./model/repo/Repository.php");

class DBSetup extends model\repository\Repository
{
  public function getInstallDBSQLString()
  {
    return 
    '
    -- --------------------------------------------------------

    --
    -- Table structure for table `answer`
    --

    CREATE TABLE IF NOT EXISTS `'.$this->answerTable.'` (
      `answerId` int(11) NOT NULL AUTO_INCREMENT,
      `pollId` int(11) NOT NULL COMMENT \'foreign\',
      `answer` varchar(50) NOT NULL,
      PRIMARY KEY (`answerId`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=262 ;


    -- --------------------------------------------------------

    --
    -- Table structure for table `category`
    --

    CREATE TABLE IF NOT EXISTS `'.$this->categoryTable.'` (
      `categoryId` int(11) NOT NULL AUTO_INCREMENT,
      `categoryName` varchar(50) NOT NULL,
      PRIMARY KEY (`categoryId`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

    --
    -- Dumping data for table `category`
    --

    INSERT INTO `'.$this->categoryTable.'` (`categoryId`, `categoryName`) VALUES
    (1, \'Others\'),
    (2, \'Politics\'),
    (3, \'Movies\'),
    (4, \'Music\'),
    (5, \'Video Games\'),
    (6, \'Food\'),
    (7, \'Books\'),
    (8, \'Places\');

    -- --------------------------------------------------------

    --
    -- Table structure for table `comment`
    --

    CREATE TABLE IF NOT EXISTS `'.$this->commentTable.'` (
      `commentId` int(11) NOT NULL AUTO_INCREMENT,
      `pollId` int(11) NOT NULL,
      `userId` int(11) NOT NULL COMMENT \'foreign\',
      `comment` text NOT NULL,
      `commentTime` datetime NOT NULL,
      PRIMARY KEY (`commentId`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=93 ;


    -- --------------------------------------------------------

    --
    -- Table structure for table `poll`
    --

    CREATE TABLE IF NOT EXISTS `'.$this->pollTable.'` (
      `pollId` int(11) NOT NULL AUTO_INCREMENT,
      `creatorId` int(11) NOT NULL,
      `question` varchar(100) NOT NULL,
      `creationDate` datetime NOT NULL,
      `public` tinyint(1) NOT NULL,
      `categoryId` int(11) NOT NULL,
      PRIMARY KEY (`pollId`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=123 ;


    -- --------------------------------------------------------

    --
    -- Table structure for table `reportedComment`
    --

    CREATE TABLE IF NOT EXISTS `'.$this->reportedCommentTable.'` (
      `reportedCommentId` int(11) NOT NULL AUTO_INCREMENT,
      `userId` int(11) NOT NULL,
      `commentId` int(11) NOT NULL,
      `commentFromReporter` varchar(255) NOT NULL,
      PRIMARY KEY (`reportedCommentId`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=83 ;

    -- --------------------------------------------------------

    --
    -- Table structure for table `reportedPoll`
    --

    CREATE TABLE IF NOT EXISTS `'.$this->reportedPollTable.'` (
      `reportedPollId` int(11) NOT NULL AUTO_INCREMENT,
      `userId` int(11) NOT NULL,
      `pollId` int(11) NOT NULL,
      `commentFromReporter` varchar(200) NOT NULL,
      PRIMARY KEY (`reportedPollId`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=60 ;

    -- --------------------------------------------------------

    --
    -- Table structure for table `reportedUser`
    --

    CREATE TABLE IF NOT EXISTS `'.$this->reportedUserTable.'` (
      `reportedUserId` int(11) NOT NULL AUTO_INCREMENT COMMENT \'primary key\',
      `userId` int(11) NOT NULL COMMENT \'user with reports\',
      `type` varchar(7) NOT NULL,
      `nominatedForDeletionBy` int(11) DEFAULT NULL COMMENT \'userId for admin that nominated this user to be deleted\',
      `commentFromAdmin` varchar(200) DEFAULT NULL COMMENT \'reason by admin. Then reason for being nominated for deletion.\',
      PRIMARY KEY (`reportedUserId`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=62 ;

    -- --------------------------------------------------------

    --
    -- Table structure for table `user`
    --

    CREATE TABLE IF NOT EXISTS `'.$this->userTable.'` (
      `userId` int(11) NOT NULL AUTO_INCREMENT,
      `userName` varchar(40) NOT NULL,
      `email` varchar(255) NOT NULL,
      `dateAdded` date NOT NULL,
      `status` tinyint(1) NOT NULL,
      `password` varchar(255) NOT NULL,
      `salt` varchar(255) NOT NULL,
      PRIMARY KEY (`userId`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=42 ;

    --
    -- Dumping data for table `user`
    --


    -- --------------------------------------------------------

    --
    -- Table structure for table `vote`
    --

    CREATE TABLE IF NOT EXISTS `'.$this->voteTable.'` (
      `voteId` int(11) NOT NULL AUTO_INCREMENT,
      `ip` varchar(100) NOT NULL,
      `answerId` int(11) NOT NULL,
      PRIMARY KEY (`voteId`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=87 ;
    ';
  }


  public function installDB()
  {    
    $this->connect(); 
    
    $query = $this->dbConnection->prepare($this->getInstallDBSQLString());  
    $result = $query->execute();
    

    /*
    if($result === false)
    {
      print_r($this->dbConnection->errorInfo());     
    }
    else
    {
      die("works");
    }
    */

  }

}