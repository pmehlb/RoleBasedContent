<?php

/* Role-Based Content Removal, API referenced from:
 * https://gerrit.wikimedia.org/g/mediawiki/core/+/master/includes/OutputPage.php
 * https://www.mediawiki.org/wiki/Manual:Architectural_modules/OutputPage
 */

/*
this might be a better idea https://www.mediawiki.org/wiki/Manual:Parser_functions
https://www.mediawiki.org/wiki/Manual:Tag_extensions
*/

namespace MediaWiki\Extension\RoleBasedContent;

class PageHook implements \MediaWiki\Hook\BeforePageDisplayHook {
	/**
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/BeforePageDisplay
	 * @param \OutputPage $out
	 * @param \Skin $skin
	 */
	public function onBeforePageDisplay($out, $skin) : void {
		// get body content text
		$page = $out->getHTML();
		$groups = User::getGroupMemberships();
		
		foreach ($groups as $group) {
			file_put_contents("dump.txt", $group->getGroup, FILE_APPEND);
		}
		
		if (!strpos($out->getPageTitle(), "Editing")) {
			/* get all page RoleContent tags
			* RegEx string (\(\!(|\/)RoleContent.*\))
			* matches (RoleContent group="...") and (/RoleContent)
			* -----------------------------------------------------------
			* once we get all of these blocks, check the users group against it, and
			* if they don't have permission, remove it from the $page html
			*
			* note: i purposely don't use a DOMDocument due to the hassle...not sure if it's faster though
			*/
			
			$tempblocks = array();
			$blocks = array();
			
			// get all tags
			if (preg_match_all('/(\(\!(|\/)RoleContent.*\))/', $page, $tempblocks)) {
				$blocks = $tempblocks[1];
			}
			
			// if there are blocks, get opening and closing tags, and check against user role
			if (!is_null($blocks)) {
				for ($i = 0; $i < count($blocks); $i += 2) {
					$open_block = $blocks[$i];
					$close_block = $blocks[$i + 1];
					
					//if () {
						
						// get first occurence of both, remove everything inside (and <p></p> tags)
						$start_pos = strpos($page, $open_block);
						$end_pos = strpos($page, $close_block);
						
						// for testing and debuggin
						// file_put_contents("dump.txt", "Pass: " . $open_block . " (pos. " . $start_pos . ")\t" . $close_block . "(pos. " . $end_pos . ")\r\n\r\n", FILE_APPEND);
						
						$page = substr_replace($page, "", $start_pos, ($end_pos - $start_pos + strlen($close_block)));
					//}
				}
			}
			
			$out->clearHTML();
			$out->addHTML($page);
		}
	}
}