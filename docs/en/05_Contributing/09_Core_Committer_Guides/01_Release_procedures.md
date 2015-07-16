title: Release procedures
summary: How do we prepare new releases?

# Release preparation guide

This document is for our [Core committers](core_committers). It provides details about our process for new SilverStripe
releases. For everyone else, we provide this document so that we can be transparent about what our processes are.

<div class="notice" markdown='1'>
This document is a first draft of a guide that was published internally at SilverStripe Ltd. We are opening up the
release process, and as part of this, much of this process is being improved.
</div>

## Access you will need

 * Commit access to the silverstripe-installer, silverstripe-framework, silverstripe-cms repositories on GitHub.
 * SSH access to the box that hosts the release packages (to upload the tar.gz and zip downloads)
 * Admin rights on TeamCity (to set up MSSQL build)
 * Publish rights for doc, api, demo

## Setting up your environment

<div class="notice" markdown='1'>
To do: We should move away from Phing and bundle this stuff more elegantly.
</div>

 * Setup phing and composer - Necessary to run the build tools

        curl -s https://getcomposer.org/installer | php
        sudo mv composer.phar /usr/bin/composer
        composer config --global repositories.pear pear http://pear.php.net
        composer global require phing/phing:2.4.*
        composer global require pear-pear.php.net/Pear:*
        composer global require pear-pear.php.net/Archive_Tar:*
        composer global require pear-pear.php.net/VersionControl_Git:*
        echo -e 'export PATH=$PATH:~/.composer/vendor/bin' >> ~/.bash_profile

## Setup your AWS account (talk to Stig)

<div class="notice" markdown='1'>
To do: We should to replace this with either S3 or GitHub.
</div>

 * Setup AWS CLI - https://sites.google.com/a/silverstripe.com/aws/first-steps/install-the-tools

 * Ensure that your public IP is allowed via the security policy https://sites.google.com/a/silverstripe.com/aws/cli-commands#TOC-Security-Groups.

        aws ec2 authorize-security-group-ingress --group-id sg-27ef2a42 --protocol tcp --port 22 --cidr 103.14.69.131/32

 * Ensure that you have the silverstripe.pem (ssh private key) setup locally. Get this from keepassx. Load this into your ssh agent.

        ssh-add -K ~/.ssh/silverstripe.pem

 * Add the following to your .ssh/config to ensure that you can SCP into the NFS for ss.org

        # For sshing into SS.org over AWS
        Host nfs1.silverstripe.org
        User admin
        HostName 10.130.2.200
        ProxyCommand ssh -A ec2-user@54.206.35.248 nc %h %p 2> /dev/null
  
 * Test you can login

        ssh nfs1.silverstripe.org

## Pre-announcements

 * If this is a security release, post to the security-preannounce list.
 * Send an email to silverstripe-committers and [Nicole](mailto:nwilliams@silverstripe.com) to warn people that a new release is coming.

## Final things to check into the repository

### Merge-forward

Merge from other release branches, project branches and trunk (don't forget silverstripe-installer!)

### Translations

<div class="notice" markdown='1'>
To do: We should bundle necessary tools as `require-dev` dependencies of the framework and cms modules.
</div>

Update translations with the transifex client (requires Python). This means pushing new master files to the platform, as well as pulling down new translations. Please skim over the translation diffs to ensure we don't import any XSS attacks or other abusive content like extremely long strings. We're relying on the community to identify wrong translations, since we can't speak all languages.

    # Do this for both framework/ and cms/
    (cd framework && tx push -s)
    (cd framework && tx pull -a -f --minimum-perc=10)

    # Manually review new additions through git diff
    (cd framework && git add lang/)

    # Convert JavaScript source files
    phing -Dmodule=framework translation-generate-javascript-for-module
    phing -Dmodule=framework/admin translation-generate-javascript-for-module
    phing -Dmodule=cms translation-generate-javascript-for-module
    (cd framework && git add javascript/lang/)
    (cd framework && git commit -m "Updated translations")
    (cd framework && git push)

### Changelog

Summarize highlevel changelog (incl. screenshots) and send to marketing for publication on our blog.

Create new upgrading instructions with summarized API changes (and a link to the full changelog). Make sure that the /docs/en/changelogs/index.md has a reference to any new version releases, and that there is a #Changelog section at the bottom containing links to each of the resulting release downloads. See http://doc.silverstripe.org/framework/en/3.1/changelogs/rc/3.1.5-rc1 for an example.

## Manual testing

<div class="notice" markdown='1'>
To do: The TeamCity tests can be replaced entirely with Travis and AppVeyor once [#4406](https://github.com/silverstripe/silverstripe-framework/issues/4406) is done.
</div>

 * Run the manual regression test suite (Ensure the tested site is in "live mode")
 * Check that the unit tests and the builds are passing for this branch: TeamCity Behat and SQL Server, Travis CMS, Travis Framework
 * Check that you can upgrade a basic site from previous minor release and previous major release (Example: Releasing 2.4.1, you'd check upgrading for 2.4.0 and 2.3.6)
 * Check all tickets (framework, cms, installer) assigned to that milestone are either closed or reassigned to another milestone.

## Special things for new minor/major releases

 * Create a new TeamCity instance for MSSQL testing (this can be removed once [#4406](https://github.com/silverstripe/silverstripe-framework/issues/4406) is done)
 * Create a new Relishapp.com version and a corresponding TeamCity build

        # Example
        relish versions:add silverstripe/silverstripe-framework:3.2

 * Check our supported modules (unit tests and regression)
 * Coordinate with module maintainers about simultaneous module releases and dependencies
 * Add version to http://api.silverstripe.org (publish "ss2api" project on test.silverstripe.com, see README)
 * Add version to http://doc.silverstripe.org (and update current docsviewer version in _config.php on github)
 * Add version to http://userhelp.silverstripe.org (and update current docsviewer version in _config.php on github)
 * Create a new branch on userhelp-content on github
 * Update Deprecation::notice() version

## Releasing

<div class="notice" markdown='1'>
To do: The release process needn't rely on a carefully configured local checkout: it can be built straight from Packagist.
</div>

 * (First time) Setup an _ss_environment.php. Ensures that you can run tests without configuration changes on the installation.
 * Check out a new working copy, to ensure you have a clean version. Create it with composer.
DON'T execute install.php, to avoid publishing local changes.

        composer create-project --prefer-source --keep-vcs silverstripe/installer my-release 3.1.x-dev

 * Add buildtools - can't be added through composer since it would show up in the composer.lock file:

         git clone git://github.com/silverstripe/silverstripe-buildtools.git buildtools

 * Run unit tests again on this checkout. 

        phpunit

 * Run behat tests again on this checkout. Needs to run on separate install to avoid "polluting" composer.json.

        composer create-project --prefer-source --dev silverstripe/installer my-behat-release 3.1.x-dev
        cd my-behat-release
        composer require silverstripe/behat-extension:* --prefer-dist
        # (for 3.0 compatibility use composer require silverstripe/behat-extension 0.1.*-dev)

        composer require silverstripe/behat-perceptual-diff:* --prefer-dist
        wget http://selenium.googlecode.com/files/selenium-server-standalone-2.31.0.jar
        java -jar selenium-server-standalone-2.31.0.jar
        vendor/bin/behat @framework
        vendor/bin/behat @cms

 * Run Behat perceptual diffs as sanity check: https://github.com/chillu/silverstripe-behat-perceptual-diff
 * Check out another copy and run the installer.php

        composer create-project --prefer-source --keep-vcs silverstripe/installer my-install-test 3.1.x-dev

 * Make sure that the installer correctly works and can setup a new site from scratch.

 * Create a temporary release branch if starting an RC cycle and you want to continue commits to the release branch without  including those in the upcoming release (for each module and installer):

        git checkout -b 3.1.9
        git push origin 3.1.9

## Actually performing the release

Perform release. Follow the instructions, and check the buildtools README to understand what it's doing.

For 3.x releases:

    phing release

Protip: Skip the prompts by predefining some values.

    phing -DbaseBranchName=3.1 -DtagName=3.1.0-beta3 -Dchangelog.fromCommit=3.1.0-beta2 release

Finish the temporary release branch if you've released a stable version (for each module and installer):

    git checkout 3.1
    git merge tmp-3.1.2
    git push origin 3.1
    git push origin :tmp-3.1.2
    git branch -d tmp-3.1.2

## Publication and announcement

<div class="notice" markdown='1'>
To do: silverstripe.org should update links automatically once releases are tagged and/or uploaded.
</div>

These require appropriate permissions on SilverStripe.org:

 * Update the ss.org release links by logging in and running the [CoreReleaseUpdateTask on silverstripe.org](http://www.silverstripe.org/dev/tasks/CoreReleaseUpdateTask). 
 * Manually update current stable release via http://www.silverstripe.org/admin/pages/edit/show/55
Ensure the new version is downloadable from http://silverstripe.org/download.
 * If the release was security related, update the security release info the http://silverstripe.org/security-releases/
 page  (edit page in CMS) against which milestone.

Once those are done, try installing the archive downloaded from silverstripe.org. Then it's time to announce the new
release to all and sundry:

 * Post a link to the release and changelog on silverstripe-announce
 * Post a readonly thread to silverstripe.org "Releases and Announcements" forum which links to mailing list announcement, and set it as a global announcement on the forums (remove any other old releases from existing global announcements)
 * Update the IRC topic
 * Talk to [Cam](mailto:cam@silverstripe.com) or [Nicole](mailto:nwilliams@silverstripe.com) about getting tweets, faceook posts, and maybe a blog post out.

These are things that should only be done for stable releases:

 * Get the maintainer of the WebPlatformInstaller to submit a new release to Microsoft (stable releases only). He is responsible for updating the ss.org/download page once Microsoft has approved the release ("Content - WebPI" tab on "downloads" page in CMS)
 * Update the demo site (docs tbc)

## Getting ready for the next release

 * Set the github milestones (framework, cms, installer) to completed
 * Create new github milestones for each module with the next minor version number (e.g. "3.0.100" if you've just released "3.0.99"). This is important because for planning purposes, and to have an overview which tickets are fixed.
 * If this is a pre-release, create a reminder event as the tentative date for the next release. Invite interested stakeholders who might be impacted, e.g. other core committers.


## Following the release

For the week after a new release, closely monitor feedback from the community. Assess what needs to be addressed, and
how urgently.

 * If we just produced a pre-release, we will need to decide what must be addressed before the stable
release.
 * If we have just produced a stable release, we will need to decide whether the issues are so critical that a new
patch release must be prepared.