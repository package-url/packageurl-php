# Contributing 

Feel free to open pull requests.


## Pullrequests

When opening a pull request, use the repository’s pull request template and complete all required fields.  
Keep each pull request focused on a single topic or problem.

Every pull request must reference an existing issue that it aims to address.  
If no issue exists for your topic, please create one first using the appropriate issue template, then link your pull request to it.


## Setup 

Even though the code base itself is php 7.3 compatible,
the setup of this project for development purposes requires php >= 7.4.

To start developing simply run `composer run-script dev-setup` to install dev-dependencies and tools.

## Tests

Make sure

* to run `composer run-script cs-fix` to have the coding standards applied.
* to run `composer run-script test` and pass all tests.
