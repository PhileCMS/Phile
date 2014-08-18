# Changelog

## Release v1.2.0

- [x] [a98bde4]("https://github.com/AUTHOR/REPO/commit/a98bde419326fe686671dfdc633f3658a70c680a") by james2doyle - fix error when settings was null
- [x] [de62392]("https://github.com/AUTHOR/REPO/commit/de62392018fca80c072ae110712dfa9c0acdfd56") by Schlaefer - allows page sorting by multiple criteria
- [x] [790c95e]("https://github.com/AUTHOR/REPO/commit/790c95e2f315de5d8accb463e9bc586b95d50347") by Schlaefer - don't couple \Model\Repository to \Model\Page
- [x] [8a7812a]("https://github.com/AUTHOR/REPO/commit/8a7812a3ad582305cb77f4d32df98ce0c8c89431") by Schlaefer - refactors nextPage/previousPage
- [x] [8f5543b]("https://github.com/AUTHOR/REPO/commit/8f5543b01492aa4b95cae93365b4a2ace0ca204b") by Schlaefer - resets pages keys after sorting to numerical values

## Release v1.1.1

- [x] [f927f41]("https://github.com/AUTHOR/REPO/commit/f927f41d6246b5f1bf72669d63e02ceb6333337b") by james2doyle - **update default michelf/markdown version to 1.4**
- [x] [2d6384d]("https://github.com/AUTHOR/REPO/commit/2d6384d1feecf61a0c7cab57d2620cb6fb16484c") by james2doyle - update composer.json description, keywords, markdown version and plugin installer version.
- [x] [e70e03d]("https://github.com/AUTHOR/REPO/commit/e70e03da11e10961bbf3b1a8317a2b32c214b628") by james2doyle - add notes about first run
- [x] [601fa5c]("https://github.com/AUTHOR/REPO/commit/601fa5cb7a74c8935175d6ba024920b422f70a1c") by james2doyle - lots of updates to the 1.1 readme

## Release v1.1.0

- [x] [67e07b5]("https://github.com/PhileCMS/Phile/commit/67e07b546bca5b274ea413101f04b08545dda94c") by Frank Nägler - Merge pull request #93 from PhileCMS/1.0.0/dir-organize
- [x] [f2a5cdb]("https://github.com/PhileCMS/Phile/commit/f2a5cdb8f23547b2a002b1d5dba9e0f0b400d37e") by james2doyle - update generator for new vendor path
- [x] [73b5670]("https://github.com/PhileCMS/Phile/commit/73b5670cc1602084b2cf61eba8cab1245fb5a130") by james2doyle - move vendor directory to lib/vendor
- [x] [51f9d76]("https://github.com/PhileCMS/Phile/commit/51f9d76f372c77af2767e10d1f8c106686fc7830") by james2doyle - update phpFastCache config to use CACHE_DIR
- [x] [a3f7414]("https://github.com/PhileCMS/Phile/commit/a3f7414376615ace0d780b5c265cd1f1cef668c5") by james2doyle - move datastorage and update plugin
- [x] [67a93b8]("https://github.com/PhileCMS/Phile/commit/67a93b8e4e92640605091f1ddae8f0ed9efd3701") by James Doyle - update error handler references
- [x] [b9f8d68]("https://github.com/PhileCMS/Phile/commit/b9f8d68bec8498c5fd14d3180a8014c5dc5de26b") by James Doyle - update error stylesheet
- [x] [68f63a5]("https://github.com/PhileCMS/Phile/commit/68f63a5dfc2efc6cec426ecd2591d3a6ef8001e9") by Frank Nägler - Merge branch 'release/1.0.0' of github.com:PhileCMS/Phile into release/1.0.0
- [x] [7c178df]("https://github.com/PhileCMS/Phile/commit/7c178df0ca4cea57dc7d612460f84525baf0fbb8") by Frank Nägler - added getPreviousPage and getNextPage to page model
- [x] [5a66571]("https://github.com/PhileCMS/Phile/commit/5a6657125f2721a3556c786c341a848bca89efdc") by Frank Nägler - Merge pull request #87 from PhileCMS/bugfix/special-chars-meta
- [x] [3f09421]("https://github.com/PhileCMS/Phile/commit/3f094215fa8bd994f63046edcac72e7460366d0a") by Frank Nägler - added development error handler
- [x] [f2b5d15]("https://github.com/PhileCMS/Phile/commit/f2b5d157753a7f5babbabed1daff9aa1151de55a") by Frank Nägler - update documentation
- [x] [9a65b32]("https://github.com/PhileCMS/Phile/commit/9a65b32dbca9e4942d8905b7974e7cd680f4e825") by Frank Nägler - collect plugin loading error and throw PluginException after all other plugins (incl. errorHandler plugin) has initialize
- [x] [a7cd7f2]("https://github.com/PhileCMS/Phile/commit/a7cd7f24f30b357a25810b41c99a10a8491d5d00") by Frank Nägler - added output buffering to make it possible to show a custom error page by error handlers
- [x] [ae518e6]("https://github.com/PhileCMS/Phile/commit/ae518e6f12c21c86b374f0c7360916fd5dfd412a") by Frank Nägler - [FEATURE] added some new types of exceptions and added a unique code for each thrown exception
- [x] [851c328]("https://github.com/PhileCMS/Phile/commit/851c328163661d8977fd98926e4ce5d659c4e6f5") by Frank Nägler - [FEATURE] ErrorHandling
- [x] [7b3a862]("https://github.com/PhileCMS/Phile/commit/7b3a862e0ce371b47173b7c559bc30bb45faddf8") by Frank Nägler - added utility method to check if a plugin is loaded
- [x] [e5478be]("https://github.com/PhileCMS/Phile/commit/e5478bec46ba71a05154e11b564362dc10aedc97") by james2doyle - replace special chars in meta with underscores
- [x] [beb8b90]("https://github.com/PhileCMS/Phile/commit/beb8b90500f113362d396c4e30d247569b4919b1") by Frank Nägler - [BUGFIX] fix configuration overwrite.

## Release v1.0.0

**important notice: new plugin structure**

- folder: plugins/*VENDOR*/*PLUGINNAME*
- class dir: plugins/*VENDOR*/*PLUGINNAME*/Classes/ (all classes autoloaded)
- plugin class: plugins/*VENDOR*/*PLUGINNAME*/Classes/Plugin.php (see demo plugin)
  - *VENDOR* = lowercase vendor name, e.g. mycompany (phile is reserved for the core plugins)
  - *PLUGINNAME* = first character lowercase, e.g. myPlugin
- namespace: \Phile\Plugin\\*VENDOR*\\*PLUGINNAME*
  - *VENDOR* = first character uppercase vendor name, e.g. Mycompany (Phile is reserved for the core plugins)
  - *PLUGINNAME* = first character uppercase, e.g. MyPlugin

- [x] #79 [TASK] preparations for version 1.0.0

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
