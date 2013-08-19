<?php
/*
PHP script which fetch Evernote note via Evernote Cloud API with certain tag.
Fetched notes are inserted to task in Wunderlist2 web service.

Version history:
1.0 16.8.2013 Joni Räsänen - Initial version
1.01 18.8.2013 Joni Räsänen - Fetch default list for Wunderlist from configuration file, variable $wlDefaultList. Issue #1
1.02 19.8.2013 Joni Räsänen - Evernote content ENML format is cleaned by strip_tags function
*/

/*
	***General code section starts***
*/

	// Interal type for storing note data from Everenote
	class JrSyncNote {

		public $JrGuid;
		public $JrTitle;
		public $JrContent;
		public $JrTags;

		public function __construct($JrGuid, $JrTitle, $JrContent,$JrTags) {
			$this->JrGuid = $JrGuid;
			$this->JrTitle = $JrTitle;
			$this->JrContent = $JrContent;
			$this->JrTags = $JrTags;
		}

		public function guid() {
			return $this->JrGuid;
		}

		public function title() {
			return $this->JrTitle;
		}
		
		public function content() {
			return $this->JrContent;
		}
		
		public function tags() {
			return $this->JrTags;
		}
	}
	
	// Interal type for storing notes from Everenote
	class JrSyncNoteList {
		private $jrnotes = array();

		public function Add($jrnote) {
			$this->jrnotes[] = $jrnote;
		}

		public function Count() {
			return count($this->jrnotes);
		}

		public function GetLists() {
			$japa = $this->jrnotes;
			return $japa;
		}
	}
/*
	***General code section ends***
*/

/*
	***Evernote code section starts***
*/
	// Connect to Evernote
	include_once('evernote_init.php');
	$noteStore = $client->getNoteStore();

	/* ********* This is only for testing purpose *********
	// List all of the notebooks in the user's account
	$notebooks = $noteStore->listNotebooks();
	//print "Found " . count($notebooks) . " notebooks\n";
	foreach ($notebooks as $notebook) {
		//print "    * " . $notebook->name . "\n";
	}
	
	// Get list of tags
	$tagGuids = $noteStore->listTags();
	foreach ($tagGuids as $tag) {
		$tag_guid = ($tag->guid);
		$tag_name = ($tag->name);
		if ($tag_name == "todo") {
				$todo_tag = $tag_guid;
			}
		if ($tag_name == "Synced") {
				$synced_tag = $tag_guid;
			}
		}
	/* ********* This is only for testing purpose *********/

	// Filter for tags
	$filter = new EDAM\NoteStore\NoteFilter();
	$filter->words ="tag:todo -tag:Synced"; //TODO, User should have possible configure this
	
	// Get notes
	$notelist = $noteStore->findNotes($filter,0,100);
	$notes = ($notelist->notes);

	// Create internal object for storing Evernote data
	$jrsyncnotelist = new JrSyncNoteList;
	
	foreach ($notes as $note) {
		$note_guid = ($note->guid);
		$note_title = ($note->title);
		$note_tagGuids = ($note->tagGuids);
		$content = $noteStore->getNoteContent($note_guid);        
		//Collect notes to internal object
		$jrsyncnotelist->add(new JrSyncNote($note_guid,$note_title,strip_tags($content),$note_tagGuids));
        
	}
/*
	***Evernote code section ends***
*/


/*
	***Wunderlist code section starts***
*/
	// Add note only if there is something new
	if ($jrsyncnotelist->Count() > 0) {
		//Connect to Wunderlist
		include_once('wunderlist_init.php');
		
		// Try & Catch
		try
		{
			// get available lists
			$lists = $wunderlist->getLists();
			foreach ($lists as $v1) {
                // Fecth default list from configuration file
                if($wlDefaultList =$v1['title']){
                   $list_id =  $v1['id'];
                }   
			}
			//$due_date = date("Y-m-d", mktime()+(60*60*24));
			$due_date = false;
			$starred = false;
			
			foreach ($jrsyncnotelist->GetLists() as $tmpnote) {
				$wunder_guid = ($tmpnote->JrGuid);
				$wunder_title = ($tmpnote->JrTitle);
				$wunder_comment = ($tmpnote->JrContent);
				$wunder_tags = ($tmpnote->JrTags);
				
				// Add the new task
				$addTask = $wunderlist->addTask($wunder_title, $list_id, $due_date, $starred);
				// The task details are returned if the request was succesfull
				$wunder_task_id = $addTask['id'];
				$addNoteToTask = $wunderlist->addNoteToTask($wunder_task_id, $wunder_comment);
				
				//TODO, Check $addTask and $addNoteToTask result if adding task was really successfull
				//After successfull adding, Evernote note should tag with $synced_tag
				$update_note = new EDAM\Types\Note();
				$update_note->guid = $wunder_guid;
				$update_note->title = $wunder_title;
				$update_note->tagGuids = $wunder_tags; // Try keep existing tags
				$update_note->tagNames = array('Synced'); // This is hardcoded, but maybe we can use some conf file
				$updatedNote = $noteStore->updateNote($update_note);
			}
		}
		catch(Exception $e)
		{
			echo $e->getMessage();
			// $e->getCode() contains the error code	
		}
	}
	
/*
	***Wunderlist code section ends***
*/