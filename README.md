Updating
========

* Clone this repo and switch to this branch.
* Run `git submodule update --remote`. This will put the latest wiki version.
* Run `./converter.sh`. This will convert the `md` pages to `HTML`.

If there are errors running `converter.sh`, then it is possible that you may have to run [marked](https://github.com/chjj/marked) manually for the files that are not converting properly.
