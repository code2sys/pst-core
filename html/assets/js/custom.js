function openModal(id, height, width)
{ 
	$('#' + id).modal({persist: true}); 
	$('#simplemodal-container').height(height).width(width);
	$(window).resize();
	return false;
}

function confirmDelete(delUrl)
{
	if(confirm("Are you sure you want to delete this record?"))
	{
		document.location = delUrl;
	}
}

function resetForm(id) {
	$('#'+id).each(function(){
	   this.reset();
	});
}

function changePageWithLink(newUrl, returnUrl)
{
	$('<form action="'+newUrl+'" method="POST">' + 
    '<input type="hidden" name="return" value="'+returnUrl+'">' +
    '</form>').appendTo('body').submit();
}


$(document).ready(function(){
  $('#search').keyup(function(e){
    if(e.keyCode == 13)
    {
      $('.search_button a').click();
    }
  });
});


