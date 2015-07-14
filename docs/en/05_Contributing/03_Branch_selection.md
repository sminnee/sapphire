summary: Determining the right branch to submit your pull request against.

# Choosing the branch for your change

When issues or pull requests are raised, you need to decide which release a change will go in. This affects
two things:

 * Which milestone an issue or pull request is marked with.
 * Which branch a pull request is merged into.

In short, the answer is this:

 * Most changes should be applied to the default branch in GitHub.
 * Low-risk bugfixes should be applied to the stable minor branch (e.g. the `3.1` branch).
 * API changes and high-risk changes should always go against the `master` branch.
 * Security fixes should be emailed to [security@silverstripe.com](mailto:security@silverstripe.com) instead.

This guide is written for by anyone contributing a change. It is also the official guide for our core committers
when reviewing pull requests and triaging issues.

## Semantic versioning

The starting point for our decision-making is [Semantic Versioning](http://semver.org/) (a.k.a. SemVer). We try
and follow SemVer as closely as possible. Unfortunately, we are not strictly compliant for a few reasons.

The main issue is that we don't declare a *precise and comprehensive* public API. We have documentation, but many users of SilverStripe rely on functionality that isn't clearly documented. We treat only our documented features as our "public API", but we don't believe that to be in our users' interests.

Similarly, we have found it unhelpful to treat that anything that can *possibly* be done with the code as the public API, as every bugfix is a change to behaviour that [someone might be relying on](https://xkcd.com/1172/).

This is particularly problematic when addressing security issues. Whenever possible, we provide fixes to critical support issues as patch releases to the currently supported minor releases lines. Sometimes, this is only possible with minor changes to edge-case behaviours of the API—generally those parts of the API that aren't clearly documented.

In these cases, we have to make a judgement on how obscure a use-case is. If the security issue is critical, and the API breakage obscure, we may choose to release the critical security release.

### Public API

For the purposes of SemVer assessment, we define our public API as follows:

 * Where functionality is described by our documentation ([docs](http://docs.silverstripe.org/) and [API](http://api.silverstripe.org/)), then it is part of our public API.
 * For use-cases not covered by the documentation, the core team makes a judgement on how likely it is that the undocumented behaviour is relied upon. Where it seems likely that the behaviour will be relied on by more than a handful of users, then it is part of our public API. If in doubt, it's best to assume that it's part of the public API.

We acknowledge that this is not ideal. In future releases, we aim to make our documentation more precise and comprehensive so that it can be used as a viable definition of our public API. In the meantime, if you are relying on an undocumented feature, we strongly recommend that you [provide an update to the documentation](documentation) that describes it!

### Regression Risk

As well as API breakage, we assess *Regression Risk* when deciding where to apply a change. Regression Risk isn't defined by SemVer, but we use it to keep patch releases upgrade as safe as possible.

Regression Risk is the risk that either of these happens:

 * The change introduces a new bug, or *inadvertently* breaks an API
 * Someone is relying on the undocumented behavour that we have changed

Generally, the more code you're changing, the bigger the Regression Risk. In addition, for undocumented features, Regression Risk is used as a measure of the likelihood that people are relying on the undocumented behaviour on their sites.

Regression risk is not a numerical measurement. We rely on developers' judgement, and it can be "Low", "Medium", or "High". Some examples are:

 * Low: Isolated changes to well-understood code with minimal refactoring; changes to documentation, whitespace, tools used by release maintainers.
 * Medium: Refactors to well-understood code with a well-understood impact.
 * High: Changes to poorly-understood code; replacing major components with new implementations (such as 3rd party libraries)


We limit contributions to patch & minor releases in the following way:

 * Patch releases: low Regression Risk changes only, except for security issues
 * Minor releases: medium Regression Risk, except for security issues

## Branch designations

Some of the branch designations change over time (as our branches are named after version numbers), but by way of example we have the following branch designations as of 13 July 2015.

 * **Stable Minor Branches** will produce patch releases. *(Currently 3.0 and 3.1)*
 * The **Upcoming Minor Branch** will produce a minor release, but we've locked down on the API & features. *(Currently 3.2)*
 * The **Major Branch** will have future Minor Branches forked from it, at the time the first beta released is produced. *(Currently 3)*
 * The **Master Branch**, will have future Major Branches forked from it, at the time the first beta released is produced. *(Always master)*

## Choosing the right branch

With all those principles in mind, here is how you decide which release a change should go in, depending on the type of issue it is.

In the following, we use *API breakages* as shorthand for "backwards incompatible changes are introduced to the public API", as per the SemVer definition.

### Critical or Important security issues

[What do we mean by "Critical" and "Important"?](http://docs.silverstripe.org/en/3.1/contributing/release_process/#severity-rating)

 * If there is an *API breakage*, try as much as possible to limit the scope of the API breakage to obscure use-cases. If necessary, it is acceptable to have a critical security issue downgraded to a minor security issue, with a more complete fix available in a future minor/major release, and/or with optional configuration supplied.
 * In any case, the fix should be applied to each *Stable Minor Branch* in which the issue is present.
 * Changes should be forward-merged or otherwise applied into the *Upcoming Minor Branch* (if it exists), the *Major Branch*, and the *Master Branch*.
 * Sometimes a more elegant but backwards-incompatible fix may be written separately for the master branch.


**IMPORTANT:** If you are looking to submit a pull request for such as issue, please stop and email [security@silverstripe.com](mailto:security@silverstripe.com) instead. We don't like disclosing issues publicly until after a patch release is available.

### Other security issues

 * If there is a low *Regression Risk* and no *API breakage*, then apply to each *Stable Minor Branch*
 * If there is an *API breakage*, but you can refactor the change so that it only occurs when an optional config setting is activated, then do this and apply it to the *Major Branch*. Note the config setting in the upgrade guide.
 * If there is a high *Regression Risk* or the *API breakage* can't be refactored in this way, then apply to the *Master Branch*.

### Bugfixes

 * If there is a low *Regression Risk* and no *API breakage*, then apply to the latest *Stable Minor Branch*
 * If there is a medium *Regression Risk* and no *API breakage*, then apply to the *Major Branch* or the *Upcoming Minor Branch* (if it exists).
 * Otherwise, apply to the *Master Branch*.

### New features / APIs

 * If there is a low/medium *Regression Risk* and no *API breakage*, then apply to the *Major Branch*
 * Otherwise, apply to the *Master Branch*.

### Major new APIs or refactorings

 * Apply to the *Master Branch*.