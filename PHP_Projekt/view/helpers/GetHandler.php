<?php

namespace view\helpers;

//konstanter för Get-parametrar
class GetHandler
{
	public static $LOGIN = "login";
	public static $LOGOUT = "logout";
	public static $VIEW  = "view";
	public static $ID = "id";
	public static $VOTE = "vote";
	public static $SHOWRESULT = "showResults";
	public static $REGISTER = "register";
	public static $CREATE = "create";
	public static $SEARCHWORDS = "searchwords";
	public static $LIST = "list";
	
	//reports lists
	public static $POLLLIST = "poll";
	public static $COMMENTLIST = "comment";
	public static $USERLIST = "user";

	//ta bort reports
	public static $IGNORE = "ignore";

	//olika "views"
	public static $VIEWREGISTER = "register";
	public static $VIEWCATEGORY = "category";
	public static $VIEWPOLL = "poll";
	public static $VIEWUSER = "user";
	public static $VIEWCREATEPOLL = "create";
	public static $VIEWSEARCH = "search";
	public static $VIEWREPORT = "report";

	
}