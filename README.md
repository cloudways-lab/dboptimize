

Quick links: [Using](#using) | [Installing](#installing) | [Contributing](#contributing) | [Support](#support)

## Using
To view commands

> wp dboptimize

usage: wp dboptimize <command> [--optimization-id=<optimization-id>] [--site-id=<site-id>] [--param1=value1] [--param2=value2] ...

These are common WP-DBOptimize commands used in various situations:

    version                    Display version of WP-DbOptimize
    sites                          Display list of sites in a WP multisite installation.
    optimizations        Display available optimizations
    do-optimization  Do selected optimization


To view all optimizations


> wp dboptimize optimizations

    actionscheduler          Delete Action schedulers
    optimizetables            Optimize database tables
    revisions                        Clean all post revisions
    autodraft                      Clean all auto-draft posts
    trash                                Clean all trashed posts
    spam                              Remove spam and trashed comments
    unapproved                Remove unapproved comments
    transient                        Remove expired transient options
    pingbacks                    Remove pingbacks
    trackbacks                    Remove trackbacks
    postmeta                      Clean post meta data
    commentmeta          Clean comment meta data
    orphandata                Clean orphaned relationship data

How to use
eg:
wp dboptimize do-optimization --optimization-id=trash

Likewise replace "trash" with any other commands available above

## Installing

Installing this package requires WP-CLI v2.5 or greater. Update to the latest stable release with `wp cli update`.

Once you've done so, you can install the latest stable version of this package with:

To install the latest development version of this package, use the following command instead:

```bash
wp package install /optimisation:dev-master
```

## Contributing

We appreciate you taking the initiative to contribute to this project.

Contributing isn’t limited to just code. We encourage you to contribute in the way that best fits your abilities, by writing tutorials, giving a demo at your local meetup, helping other users with their support questions, or revising our documentation.

For a more thorough introduction, [check out WP-CLI's guide to contributing](https://make.wordpress.org/cli/handbook/contributing/). This package follows those policy and guidelines.

### Reporting a bug

Think you’ve found a bug? We’d love for you to help us get it fixed.

Before you create a new issue, you should to see if there’s an existing resolution to it, or if it’s already been fixed in a newer version.

Once you’ve done a bit of searching and discovered there isn’t an open or fixed issue for your bug, please [create a new issue]. Include as much detail as you can, and clear steps to reproduce if possible. For more guidance, [review our bug report documentation](https://make.wordpress.org/cli/handbook/bug-reports/).

### Creating a pull request

Want to contribute a new feature? Please first [open a new issue](https://github.com/) to discuss whether the feature is a good fit for the project.

Once you've decided to commit the time to seeing your pull request through, [please follow our guidelines for creating a pull request](https://make.wordpress.org/cli/handbook/pull-requests/) to make sure it's a pleasant experience. See "[Setting up](https://make.wordpress.org/cli/handbook/pull-requests/#setting-up)" for details specific to working on this package locally.

## Support

GitHub issues aren't for general support questions, but there are other venues you can try: https://wp-cli.org/#support


*This README.md is generated dynamically from the project's codebase using `wp scaffold package-readme` ([doc](https://github.com/wp-cli/scaffold-package-command#wp-scaffold-package-readme)). To suggest changes, please submit a pull request against the corresponding part of the codebase.*
