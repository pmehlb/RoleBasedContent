# RoleBasedContent Extension

MediaWiki extension to display content on the page based on a user's role.

Instead of displaying all content, then using CSS to hide certain parts of the page, this extension allows you to specify in the editor which roles can see what content.

This extension makes use of the MediaWiki hooks `onBeforePageDisplay` and `onBeforeParserInit`.

In the editor, specify a role-based access block by opening it with `<RoleContent group="GROUP">` and closing it with `</RoleContent>`.