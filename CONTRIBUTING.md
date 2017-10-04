# Contributing to RoboHome-Web

Thank you for your generosity and time to lend your skills to this project.  The contributions you are willing to make to this project are invaluable, big or small.  This document exists to explain how to best be a member of this project.

#### Table Of Contents

[Contributor Covenant Code of Conduct](#contributor-covenant-code-of-conduct )

[Git Usage Guidelines](#git-usage-guidelines)
  * [Making Changes](#making-changes)
  * [Submitting Issues or Feature Requests](#submitting-issues-or-feature-requests)

[Code Style Guidelines](#code-style-guidelines)

[Hacktoberfest](#hacktoberfest)

# Contributor Covenant Code of Conduct

## Our Pledge

In the interest of fostering an open and welcoming environment, we as
contributors and maintainers pledge to making participation in our project and
our community a harassment-free experience for everyone, regardless of age, body
size, disability, ethnicity, gender identity and expression, level of experience,
nationality, personal appearance, race, religion, or sexual identity and
orientation.

## Our Standards

Examples of behavior that contributes to creating a positive environment
include:

* Using welcoming and inclusive language
* Being respectful of differing viewpoints and experiences
* Gracefully accepting constructive criticism
* Focusing on what is best for the community
* Showing empathy towards other community members

Examples of unacceptable behavior by participants include:

* The use of sexualized language or imagery and unwelcome sexual attention or
  advances
* Trolling, insulting/derogatory comments, and personal or political attacks
* Public or private harassment
* Publishing others' private information, such as a physical or electronic
  address, without explicit permission
* Other conduct which could reasonably be considered inappropriate in a
  professional setting

## Our Responsibilities

Project maintainers are responsible for clarifying the standards of acceptable
behavior and are expected to take appropriate and fair corrective action in
response to any instances of unacceptable behavior.

Project maintainers have the right and responsibility to remove, edit, or
reject comments, commits, code, wiki edits, issues, and other contributions
that are not aligned to this Code of Conduct, or to ban temporarily or
permanently any contributor for other behaviors that they deem inappropriate,
threatening, offensive, or harmful.

## Scope

This Code of Conduct applies both within project spaces and in public spaces
when an individual is representing the project or its community. Examples of
representing a project or community include using an official project e-mail
address, posting via an official social media account, or acting as an appointed
representative at an online or offline event. Representation of a project may be
further defined and clarified by project maintainers.

## Enforcement

Instances of abusive, harassing, or otherwise unacceptable behavior may be
reported by contacting the project team. All
complaints will be reviewed and investigated and will result in a response that
is deemed necessary and appropriate to the circumstances. The project team is
obligated to maintain confidentiality with regard to the reporter of an incident.
Further details of specific enforcement policies may be posted separately.

Project maintainers who do not follow or enforce the Code of Conduct in good
faith may face temporary or permanent repercussions as determined by other
members of the project's leadership.

## Git Usage Guidelines

### Making Changes

1. DO make many small, logical commits in a single pull request for large changes.  Large changes should rarely ever be in a single commit.
2. DO make adding, removing, changing, upgrading, or downgrading packages or dependencies in their own separate commit per package or dependency.
3. DO open pull requests that are a complete solution.
    1. All pull requests with testable changes need to have tests submitted with them to be considered complete.  Tests should be in the same commit as the code they are testing.  If you believe it is not possible to test a change, make sure you clearly document in the commit message why this is the case.
    2. Sometimes it can take multiple pull requests to properly address an issue.  Make sure each pull request is a deliverable body of work that won't leave the project in an intermediary state.
4. DO submit [fixup! commits](https://robots.thoughtbot.com/autosquashing-git-commits) to the commit the fixup commit is addressing and not an arbitrary commit in the pull request when addressing feedback given on a pull request.  Adding new commits to address feedback is not ideal, unless you're adding new functionality while addressing feedback.  Also, do not amend commits as it makes inspecting the change between reviews challenging.  When your review is approved, you'll be asked to squash all of your fixup commits and push your changes one last time before your pull request gets merged.
5. DO provide descriptive commit messages and pull request messages.
    1. When making visual changes, provide screenshots or animated GIFs that reflect the you made in the pull request description.
    2. Descriptions should use the present tense ("Add feature" instead of "Added feature").
    3. Reference other issues or pull requests in the body of the description.
6. DO make sure you open a pull request on an appropriately named branch.
7. DO make sure the maintainers comment on your pull request with a picture of a squirrel before or just after merging your pull request.
8. DO be sure to fill out the template provided to you automatically when creating a pull request.

### Submitting Issues or Feature Requests

1. DO provided a detailed, descriptive report of the issue or desired feature.  If applicable, include a screenshot or animated GIF to demonstrate the issue or feature request. 
2. DO include numbered steps to reproduce the issue.
3. DO be sure to fill out the template provided to you automatically when creating an issue.

## Code Style Guidelines

There is a linter that runs whenever changes are made, but there are some things it will never be able to catch.  This section serves to highlight some of these items that maintainers will expect.

1. DO use descriptive variable, function, and class names.  These names should not contain abbreviations or slang.  [Certain buzz words like "manager" are not acceptable for names](https://blog.codinghorror.com/i-shall-call-it-somethingmanager/).
2. DO make sure functions only ever return a single type.  All functions must have a return type.
3. DO make sure you follow [the SOLID principles](https://en.wikipedia.org/wiki/SOLID_(object-oriented_design)).
4. DO NOT comment code.  Comments are not a source of truth for the code and it's impossible to enforce that comments are kept up-to-date and are accurate.  The best way to avoid comments is by writing clean, well-tested code with useful naming.

## Hacktoberfest

Hacktoberfest is an annual promotion put on by DigitalOcean and GitHub each October.  It's a wonderful opportunity for open source projects like this one to gain exposure.  However, due to the incentives of the Hacktoberfest promotion, there is an increase in low quality and low effort pull requests duing the month of October.

This project reserves the right to close and report to GitHub pull requests and issues that do not contribute to this project in good faith and abide by this code of conduct.

## Attribution

This Code of Conduct is adapted from the [Contributor Covenant][homepage], version 1.4,
available at https://www.contributor-covenant.org/version/1/4/code-of-conduct.html

[homepage]: https://www.contributor-covenant.org
