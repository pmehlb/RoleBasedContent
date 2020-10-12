<?php

// ensure this extension isn't being accessed directly
if (!defined( 'MEDIAWIKI')) {
	echo("This is an extension to the MediaWiki package and cannot be run standalone.\n");
	die(-1);
}

// set MediaWiki settings and hooks
$wgExtensionCredits['parserhook'][] = array(
	"path"			=> __FILE__,
	"name"			=> "Role-Based Content",
	"version"		=> "1.0.1",
	"author"		=> "Patrick Mehlbaum", 
	"url"			=> "https://patrickm.xyz",
	"description"	=> "This extension allows you to restrict page content based on a user's role."
);
$wgHooks["ParserFirstCallInit"][] = "onParserFirstCallInit";
$wgHooks["OutputPageBeforeHTML"][] = "onOutputPageBeforeHTML";
// disable caching so that users only see what they're supposed to and not what the last person saw
$wgParserCacheType = CACHE_NONE;
$wgCachePages = false;

function onParserFirstCallInit($parser) {
	// set the parser to watch for <RoleContent> blocks, and call renderRoleContent() as a callback
	$parser->setHook("RoleContent", "renderRoleContent");
}

function onOutputPageBeforeHTML(OutputPage $out, &$text) {
	// add our custom JS script
	// https://doc.wikimedia.org/mediawiki-core/master/php/classOutputPage.html#a17767a6aa7eb32cc60b29cf081b9adb2
	$out->addScriptFile('./extensions/RoleBasedContent/rbc.js');
}

function renderRoleContent($block, array $args, Parser $parser, PPFrame $frame) {
	/* Just a quick run-down of what this all does!
	 * (in case you need to edit something!)
	 *
	 * @param string $block what is inside of the <RoleContent> block
	 * @param array $args the XML-style parameters passed with the block (assoc. array)
	 */
	
	// get the <RoleContent> group parameter, escape html, and check validity
	$group = strtolower(htmlspecialchars($args["group"] ?? null, ENT_QUOTES));
	if (is_null($group) || empty($group)) {
		return '<strong><span style="color: red">Group parameter not specified.</span></strong>';
	}
	
	/* API parameters sourced from
	 * https://www.mediawiki.org/wiki/API:Userinfo#Example
	 *
	 * execute an API query to find the current user's groups
	 */
	$api_params = array(
		"action" => "query",
		"meta" => "userinfo",
		"uiprop" => "groups",
		"format" => "json"
	);
	$api_response = handleAPIRequest($api_params);
	
	// check if viewer is logged in, return nothing if they're not
	if (empty($api_response["query"]["userinfo"]["id"])) {
		return true;
	}
	
	// array with a list of user's groups
	$groups = $api_response["query"]["userinfo"]["groups"];
	
	// should we show the content?
	$show = false;
	
	// check if user is SysOp
	if (in_array("sysop", $groups)) { $show = true; }
	// check if user is in specified group
	else if (in_array($group, $groups)) { $show = true; }
	// check if the group specified contains an array of allowed groups
	else if (strpos($group, ",") !== false) {
		// get the allowed groups as an array
		$allowed_groups = explode($group, ",");
		// go through each allowed group
		foreach ($allowed_group as $allowed_group) {
			if (in_array(trim($allowed_group), $groups)) { $show = true; }
		}
	}
	
	// if $show is true, show them the content along with the <br> removal tag
	return ($show) ? '<!--RBC:REMOVE-->'. $block : true;
}

function handleAPIRequest(array $param) : array {
	global $wgRequest;
	
	/* PHP internal API request sourced from
	 * https://www.mediawiki.org/wiki/API:Calling_internally#From_application_code
	 * returns an array
	 */
	$api = new ApiMain(
		new DerivativeRequest($wgRequest, $param)
	);
	$api->execute();
	return $api->getResult()->getResultData();
}
