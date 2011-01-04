function submitbutton(pressbutton) {
	if(pressbutton == 'publish' || pressbutton == 'save') {
		if (document.adminForm.filename.value.match(/\s/)) {
			alert('The filename you entered contains spaces. Please publish a file that does not contain spaces in the name.');
		} else if (document.adminForm.filename.value == '') {
			alert('Please enter a filename.');
		} else {
			submitform( pressbutton );
		}
	} else {
		submitform( pressbutton );
	}
}