JrSync
=============
JrSync
- Current version: 1.02

PHP script which fetch Evernote note via Evernote Cloud API with certain tag.
Fetched notes are inserted to task in Wunderlist2 web service.

I have used two external projects:
- Wunderlist PHP API: Wunderlist2-PHP-Wrapper from https://github.com/PENDOnl/Wunderlist2-PHP-Wrapper

- Official Evernote PHP Could API from https://github.com/evernote/evernote-sdk-php

Current version of used API:
- Wunderlist2-PHP-Wrapper 1.02
- Evernote PHP Could API 1.25

Usage
-------
* 1. Download repository
* 2. Edit config.php with your Evernote and Wunderlist credentials
* 2.1. Note, You need Evernote developer token. You will get your own from https://sandbox.evernote.com/api/DeveloperToken.action
* 3. Edit default Wunderlist list name what is suitable for you
* 4. Run script `php jrsync.php`

Notes
-------
This current version is able to fetch note from Evernote which are tagged with tag "todo" and after exportin note are tagged with tag "Synced".
All notes with tag "note" and without "Synced" are fetched even they are already sent to Wunderlist.

Please see current issues section from below

Current issues
-------
### Major issues ###
- Evernote authenticating with OAuth
- Everenote note is tagged with Synced-tag without checking was it imported to Wunderlist succesfully

### Minor issues ###
- User should able to configure what tag is fetched from Evernote instead of hardcoded todo-tag
- User should able to configure what tag is updated to Evernote after Wunderlist import instead of hardcoded Sync-tag
- Much much more

Version history
-------
- 1.00, Initial version
- 1.01, Fetch default list for Wunderlist from configuration file
- 1.02, Evernote content ENML format is cleaned by strip_tags function