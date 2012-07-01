Podcast Manager
===============
*Podcast Manager* is a suite of extensions for a Joomla! powered website which allows users to create, manage, and host a podcast feed directly from their website.

Compatibility
===============
*Podcast Manager* is currently compatible with Joomla! 2.5.6 or newer.  2.5.6 is the minimum supported version of the 2.5 series due to supporting both Joomla! 2.5 and 3.x with a single package and the necessary support code not being present in earlier versions of 2.5.  Also, high level security issues exist in older versions of 2.5, and as such, they will NOT be supported.  Older versions of Podcast Manager compatible with older versions of Joomla! (the first supported version was Joomla! 1.6) are available from the downloads section of this repository.

Requirements
===============
*Podcast Manager* has no external requirements to operate; all necessary code is installed with the package.

Support
===============
* Documentation for *Podcast Manager* is available on my website at http://www.babdev.com/extensions/podcast-manager.
* If you've found a bug, please report it to the Issue Tracker at https://github.com/mbabker/podcast-manager/issues.

Installation Package
===============
* Installation packages for *Podcast Manager* are available from the downloads section of this repository.
* If you have made a checkout of the repository, you can build installation packages using Phing by running 'phing dev_head' from your interface.

Stable Master Policy
===============
The master branch will at all times remain stable.  Development for new features will occur in branches and when ready, will be pulled into the master branch.

In the event features have already been merged for the next release series and an issue arises that warrants a fix on the current release series, the developer will create a branch based off the tag created from the previous release, make the necessary changes, package a new release, and tag the new release.  If necessary, the commits made in the temporary branch will be merged into master.
