<?php 

namespace view\helpers;

//namn på Post-parametrar
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

	public static $COMMENT = "comment";

	public static $COMMENTREPORT_REASON = "commentReportReason";
	public static $COMMENTREPORT_ID = "commentReportId";
	public static $POLLREPORT_REASON = "pollReportReason";

	public static $DELETEOBJECT_REPORTID = "deleteObjectReportId";
	public static $DELETE_REASON = "deleteReason";
	public static $DELETEUSER_USERID = "deleteUserUserId";
	public static $NOMINATEUSER_USERID = "nominateUserUserId";

}