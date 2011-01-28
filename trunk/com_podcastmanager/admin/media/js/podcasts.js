Joomla.submitbutton = function(pressbutton) {
	if (pressbutton == 'podcasts.unpublish') {
		if (confirm('Unpublishing files may disrupt the feed. Are you sure you wish to continue unpublishing? (Files will not be removed.)')) {
			Joomla.submitform(pressbutton);
		}
	} else {
		Joomla.submitform(pressbutton);
	}
}
