# Role-Based Content
Heyo! This MediaWiki extension allows you to restrict what a user sees on a central wiki page, all based on their group! The syntax is dead simple, and I even put in the effort to try and remove any exploits of the extension (how nice of me!). To use the extension, first add it to the `LocalSettings.php` config file in the root install folder:
```php
require_once "$IP/Path/To/Hook.php";
// usually something like
// "$IP/extensions/RoleBasedContent/Hook.php";
```

To add role-based content to a page, just enclose the content with a `<RoleContent>` tag. For example:
```html
<RoleContent group="1">
# Group 1
Only group 1 can see this content!
</RoleContent>
```

You can even specify multiple groups by doing this (the space between is optional):
```html
<RoleContent group="1,2, 3">
# Groups 1, 2, and 3
Groups 1, 2, and (you guessed it!) 3 can see this!
</RoleContent>
```

Additionally, 

There ya' go! Use at your own discretion, and be careful, groups **are** case sensitive.

---
Current to do's:
- [x] Add JS module to remove starting `<br>` tags (so the user doesn't see that content was removed, and so it doesn't look ugly)
  - [x] Find jQuery to actually make it work (see notes.txt)
- [x] Add support for multiple groups

Current suggestions:
- [ ] Allow users of higher rank to view lower rank content (MediaWiki doesn't store permissions in a way that's easy to compare...)
