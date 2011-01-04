function submitbutton(pressbutton) {
	if(pressbutton == 'unpublish') {
		if(confirm('Unpublishing files may disrupt the feed. Are you sure you wish to continue unpublishing? (Files will not be removed.)')) {
			submitform( pressbutton );
		}
	} else {
		submitform( pressbutton );
	}
}