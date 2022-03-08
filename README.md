## If you are developing this code on Windows
Run `git config --local core.autocrlf true`

This will make it so that when you add files to the index, it will convert the CRLF(carrage return line feed) characters used by Windows to LF(line feed) characters used by Unix, and vice versa for checkouts. For more info see the [core.autocrlf documentation](https://git-scm.com/book/en/v2/Customizing-Git-Git-Configuration#_core_autocrlf).