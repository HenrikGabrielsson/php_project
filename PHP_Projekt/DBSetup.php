<?php


require_once("./model/repo/Repository.php");

class DBSetup extends model\repository\Repository
{
  public function getInstallDBSQLString()
  {
    return 
    '
    CREATE PROCEDURE '.\Settings::$dbName.'.deleteUser(IN id INT)
    BEGIN
      #ta bort användarens polls och allt som hör till dem
            DELETE vote.* FROM vote INNER JOIN answer ON vote.answerId = answer.answerId INNER JOIN poll ON answer.pollId = poll.pollId
            WHERE poll.creatorId = id;
            DELETE answer.* FROM answer INNER JOIN poll ON answer.pollId = poll.pollId 
            WHERE poll.creatorId = id;
            DELETE report.* FROM report INNER JOIN comment ON report.objectId = comment.commentId INNER JOIN poll ON comment.pollId = poll.pollId
            WHERE poll.creatorId = id AND report.type = "comment";
            DELETE comment.* FROM comment INNER JOIN poll on comment.pollId = poll.pollId 
            WHERE poll.creatorId = id;
      DELETE report.* FROM report INNER JOIN poll ON report.objectId = poll.pollId
            WHERE poll.creatorId = id AND report.type = "poll";
            DELETE FROM poll WHERE poll.creatorId = id;
            
            #alla användarens commentarer
            DELETE FROM comment WHERE comment.userId = id;
            
            #användaren själv och reports
            DELETE FROM report WHERE report.userId = id;
            DELETE FROM user WHERE user.userId = id;
            
            
    END;



    CREATE PROCEDURE '.\Settings::$dbName.'.didUserVote (IN p_answerid INT, IN p_ip VARCHAR(255))
    BEGIN
      DECLARE votePoll int DEFAULT 0;
      SELECT pollId FROM answer WHERE answerId = p_answerId INTO votePoll;
            
            SELECT vote.voteId FROM vote INNER JOIN answer ON vote.answerId = answer.answerId
      WHERE vote.ip = p_ip AND answer.pollId = votePoll; 
            
    END;



    CREATE PROCEDURE '.\Settings::$dbName.'.deletePoll(IN id INT)
    BEGIN

    #ta bort alla reports på comments som tas bort.
    DELETE report.* FROM report INNER JOIN comment ON report.objectId = comment.commentId
    WHERE comment.pollId = id AND report.type = "comment";

    #ta bort alla comments i poll
    DELETE FROM comment WHERE comment.pollId = id;

    #ta bort alla votes i poll
    DELETE vote.* FROM vote INNER JOIN answer ON vote.answerId = answer.answerId 
    WHERE answer.pollId = id;

    #ta bort alla svar i poll
    DELETE FROM answer WHERE answer.pollId = id;

    #ta bort alla reports på poll 
    DELETE FROM report WHERE objectId = id AND type = "poll"; 

    DELETE FROM poll WHERE pollId = id;
    END;



    CREATE PROCEDURE '.\Settings::$dbName.'.deleteComment(IN id INT)
    BEGIN
      #ta bort reports på denna comment och kommentaren själv
      DELETE FROM report WHERE objectId = id;
            DELETE FROM comment WHERE commentId = id;
    END;



    --
    -- Table structure for table `answer`
    --

    CREATE TABLE IF NOT EXISTS `answer` (
      `answerId` int(11) NOT NULL AUTO_INCREMENT,
      `pollId` int(11) NOT NULL COMMENT \'foreign\',
      `answer` varchar(50) NOT NULL,
      PRIMARY KEY (`answerId`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=274 ;

    -- --------------------------------------------------------

    --
    -- Table structure for table `category`
    --

    CREATE TABLE IF NOT EXISTS `category` (
      `categoryId` int(11) NOT NULL AUTO_INCREMENT,
      `categoryName` varchar(50) NOT NULL,
      PRIMARY KEY (`categoryId`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

    --
    -- Dumping data for table `category`
    --

    INSERT INTO `category` (`categoryId`, `categoryName`) VALUES
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

    CREATE TABLE IF NOT EXISTS `comment` (
      `commentId` int(11) NOT NULL AUTO_INCREMENT,
      `pollId` int(11) NOT NULL,
      `userId` int(11) NOT NULL COMMENT \'foreign\',
      `comment` text NOT NULL,
      `commentTime` datetime NOT NULL,
      PRIMARY KEY (`commentId`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=99 ;

    -- --------------------------------------------------------

    --
    -- Table structure for table `poll`
    --

    CREATE TABLE IF NOT EXISTS `poll` (
      `pollId` int(11) NOT NULL AUTO_INCREMENT,
      `creatorId` int(11) NOT NULL,
      `question` varchar(100) NOT NULL,
      `creationDate` datetime NOT NULL,
      `public` tinyint(1) NOT NULL,
      `categoryId` int(11) NOT NULL,
      PRIMARY KEY (`pollId`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=128 ;

    -- --------------------------------------------------------

    --
    -- Table structure for table `report`
    --

    CREATE TABLE IF NOT EXISTS `report` (
      `reportId` int(11) NOT NULL AUTO_INCREMENT,
      `userId` int(11) NOT NULL,
      `objectId` int(11) NOT NULL,
      `comment` varchar(255) DEFAULT NULL,
      `type` varchar(7) NOT NULL,
      `nominatedBy` int(11) DEFAULT NULL COMMENT \'only for users. if they have been nominated for deletion\',
      PRIMARY KEY (`reportId`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

    -- --------------------------------------------------------

    --
    -- Table structure for table `user`
    --

    CREATE TABLE IF NOT EXISTS `user` (
      `userId` int(11) NOT NULL AUTO_INCREMENT,
      `userName` varchar(40) NOT NULL,
      `email` varchar(255) NOT NULL,
      `dateAdded` date NOT NULL,
      `status` tinyint(1) NOT NULL,
      `password` varchar(255) NOT NULL,
      `salt` varchar(255) NOT NULL,
      PRIMARY KEY (`userId`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=46 ;



    -- --------------------------------------------------------

    --
    -- Table structure for table `vote`
    --

    CREATE TABLE IF NOT EXISTS `vote` (
      `voteId` int(11) NOT NULL AUTO_INCREMENT,
      `ip` varchar(100) NOT NULL,
      `answerId` int(11) NOT NULL,
      PRIMARY KEY (`voteId`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=88 ;
    ';


  }


  public function installDB()
  {    
    $this->connect(); 
    
    $query = $this->dbConnection->prepare($this->getInstallDBSQLString());  
    $result = $query->execute();
    
    return $result;

  }

}