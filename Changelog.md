# Changelog

## Release v0.9.4

**Ok so there is a problem here. Turns out we messed up version numbers. 0.9.2 was tagged as a release. But when the changelog was bumped to 0.9.3 the phile version was kept at 0.9.2. Also there was no tag for 0.9.3. So we are going to jump to 0.9.4 now.**

- [x] [4053bb2]("https://github.com/PhileCMS/Phile/commit/4053bb26c3403914ee8436711ee5aef1fc0f28b5") by James Doyle - index.php version bump
- [x] [c7c519b]("https://github.com/PhileCMS/Phile/commit/c7c519ba12cdf5e7e92762de4721a93ec62ccc63") by James Doyle - version bump
- [x] [a1cb008]("https://github.com/PhileCMS/Phile/commit/a1cb008246864295310a501bbd3253dbc5db315c") by James Doyle - Merge pull request #98 from Schlaefer/patch-1
- [x] [83dd413]("https://github.com/PhileCMS/Phile/commit/83dd4139ad201daf56b7beaa952f17fc525f67f3") by Schlaefer - refactors Utility::resolveFilePath
- [x] [6956fe0]("https://github.com/PhileCMS/Phile/commit/6956fe00c46035eff4c6a66cf80420f0c159ed9c") by Frank Nägler - Merge pull request #95 from quasipickle/patch-1
- [x] [72844cc]("https://github.com/PhileCMS/Phile/commit/72844cc1d50818e9bc7d5fc26a81f982dc197c33") by quasipickle - Update Page.php
- [x] [0010569]("https://github.com/PhileCMS/Phile/commit/0010569d3ae2df89c9edfb203a51044c22f64a95") by James Doyle - Merge pull request #86 from PhileCMS/bugfix/spaces-in-meta
- [x] [4df1164]("https://github.com/PhileCMS/Phile/commit/4df11649aaa604719d112580c62ec613b2e428cd") by james2doyle - fix issue with using spaces in meta. Now they are converted to underscores. So "Area Title:" would become "{{ current_page.meta.area_title }}"
- [x] [2894cfb]("https://github.com/PhileCMS/Phile/commit/2894cfb11d4e95fca20019efdf5fc0154e517506") by James Doyle - Merge pull request #83 from pschmitt/master
- [x] [556e18e]("https://github.com/PhileCMS/Phile/commit/556e18e90712d7626e94ac9be811181de013d903") by Philipp Schmitt - Grammar
- [x] [480bd83]("https://github.com/PhileCMS/Phile/commit/480bd8325bf346845d0307c981fa934ad0c59107") by James Doyle - added philecms google plus community
- [x] [6393d64]("https://github.com/PhileCMS/Phile/commit/6393d64d7efa94657aef0a17b49cbdfac391370b") by james2doyle - Merge branch 'feature/RefactoringInterfaces'
- [x] [04a2418]("https://github.com/PhileCMS/Phile/commit/04a2418ded02273cb5e2c8070f354709a90a07e6") by James Doyle - Merge pull request #77 from PhileCMS/feature/MetaParserService
- [x] [e91d602]("https://github.com/PhileCMS/Phile/commit/e91d6020518eed2a08ad9e63abbd4e5cc864fbbf") by Frank Nägler - reformat the readme file
- [x] [363b099]("https://github.com/PhileCMS/Phile/commit/363b0991dad2592ec6a44856550559099b1c3c7f") by Frank Nägler - [TASK] added Changelog.md to the project
- [x] [220681f]("https://github.com/PhileCMS/Phile/commit/220681f022bd9193c79522045edd35a9b855ddcf") by Frank Nägler - [TASK] code cleanup: restructure interfaces
- [x] [a223521]("https://github.com/PhileCMS/Phile/commit/a22352189f3d84933db4bf8e036945403d2f13ad") by Frank Nägler - Merge branch 'refs/heads/feature/MetaParserService' into feature/RefactoringInterfaces
- [x] [fea19e2]("https://github.com/PhileCMS/Phile/commit/fea19e241154606953fea3f3a084a0abdfa8eb4d") by Frank Nägler - [BUGFIX] change requirement of plugin-installer-plugin to "dev-master", because we have currently no final version
- [x] [475b7b0]("https://github.com/PhileCMS/Phile/commit/475b7b0cf1a0ccf826678a0c807d3d548a5985eb") by Frank Nägler - [TASK] added git commit message template
- [x] [0a8f7c2]("https://github.com/PhileCMS/Phile/commit/0a8f7c239674da66ada714f3b57df7c1f5639999") by Frank Nägler - [FEATURE] change code of meta parser to use a service

## Release v0.9.3

**important notice: the changes in issue #81 need some change in plugins, please take a look at the notes in the issue**

- [x] #77 [FEATURE] change code of meta parser to use a service - many thanks to @NeoBlack for the implementation
- [x] #54 [FEATURE] Added $folder parameter functions to pages - many thanks to @okadesign for the implementation
- [x] #50 [BUGFIX] fix handling of URI - many thanks to @NeoBlack for the implementation
- [x] #48 [BUG] Using a querystring on the uri - many thanks to @jacmgr how discover this bug
- [x] #29 [FEATURE] Use multiple meta fields for sorting pages - many thanks to @NeoBlack for the implementation
- [x] #81 [TASK] code cleanup: restructure interfaces (issue: #78)

## Release v0.9.2

- [x] #47 [BUGFIX] fix sorting by meta data, if pages has the same meta value.
- [x] #37 [FEATURE] WSOD on installing Phile in localhost
- [x] #35 [FEATURE] Encryption utilities

## Release v0.9.1

- [x] #27 [FEATURE] Meta blocks as HTML comments
- [x] #21 [FEATURE] Menu sorting by Meta value
- [x] #19 [BUGFIX] Update .htaccess
- [x] #16 [BUGFIX] Sub pages not working

## Release v0.9.0
we are proud to announce the first beta release of Phile with the version number 0.9.
this version is a pre-release and we use it in production on several projects, but be careful, it is still in beta state.
