<?php
/*Version history:
1.00 16.08.2013 Joni Räsänen - Initial version
1.01 18.08.2013 Joni Räsänen - Added $wlDefaultList for default list for Wunderlist, Issue #1
1.02 01.09.2013 Joni Räsänen - All hardcoded tags etc are moved to config.php and they are configure by user, issues #5 and #6
*/
/* Evernote configurations starts

To get a developer token, visit
// https://sandbox.evernote.com/api/DeveloperToken.action
*/
$evernote_authToken = "your developer token";
$evernote_TodoTag = "your Evernote todo tag";
$evernote_SyncedTag = "your Evernote synced tag";
$evernote_DoneTag ="your Evernote done tag";
/* Evernote configurations ends */

/* Wunderlist configurations starts*/

// Wunderlist email address is username
$wlUser = "something@somewhere";
$wlPass = "password";
$wlDefaultList = "List where tasks are added";
/* Wunderlist configurations ends */