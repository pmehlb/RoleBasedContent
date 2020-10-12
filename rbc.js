$('p br:only-child').each(function(i, el) {
    if ($(el).parent().html() === "<br>\n") { $(this).parent().remove(); }
});
$('p br:only-child').each(function(i, el) {
    if ($(this).parent().html().includes("<br>\n<")) { $(this).remove(); } 
});
