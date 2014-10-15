<?php 

namespace view\helpers;

class PostHandler
{
	public static $LOGINNAME = "LoginName";
	public static $LOGINPASSWORD = "LoginPassword";

	public static $REGUSERNAME = "regUsername";
	public static $REGEMAIL = "regEmail";
	public static $REGPASSWORD1 = "regPassword1";
	public static $REGPASSWORD2 = "regPassword2";

	public static $CREATEQUESTION = "createQuestion";
	public static $CREATEANSWER = "createAnswer";
	public static $CREATECATEGORY = "createCategory";
	public static $CREATEPUBLIC = "createPublic";

	public static $VOTE = "vote";
	public static $COMMENT = "comment";

	public static $COMMENTREPORT_REASON = "commentReportReason";
	public static $COMMENTREPORT_ID = "commentReportId";
	public static $POLLREPORT_REASON = "pollReportReason";

	public static $DELETEPOLL_POLLID = "deletePollPollId";
	public static $DELETEPOLL_REPORTID = "deletePollReportId";
	public static $DELETEPOLL_REASON = "deletePollReason";
	public static $DELETECOMMENT_COMMENTID = "deleteCommentCommentId";
	public static $DELETECOMMENT_REPORTID = "deleteCommentReportId";
	public static $DELETECOMMENT_REASON = "deleteCommentReason";
}