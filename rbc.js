$('p br:only-child').each(function(i, el) {
	if ($(el).parent().html() === "<br>\n") { $(this).parent().remove(); }
	if ($(this).parent().html().includes("<br>\n<")) { $(this).remove(); } 
});

/* still need to test this js - i wrote this whole plugin at like...2 in the morning
$('p br:only-child').each(function(i, el) {
    if ($(el).parent().html() === "<br>\n") { $(this).parent().remove(); }
    if ($(this).parent().html().includes("<br>\n<")) { $(this).remove(); } 
});
*/
