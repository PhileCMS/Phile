<!--
Title: Setup
Description: Setup PhileCMS
-->

## Welcome to the setup

Congratulations, you have successfully installed [Phile](https://github.com/PhileCMS/Phile).

But you have to set one important setting in your `config.php` file. Please set the config value `$config['encryptionKey']` to a non empty value. This setting is important for encrytion stuff and should be unique for each installation. It is very important that you never change this setting after you set it the first time.

Hint: It is also important that the value of this setting is a secure and complex string. Because it is used to encrypt stuff like passwords.

You can use our <a href="generator.php">generator</a> to create a secure value for your installation.