<!--
Title: Setup
Description: Setup PhileCMS
-->

## Welcome to the setup

Congratulations, you have successfully installed [Phile](https://github.com/PhileCMS/Phile)!

Before proceeding, please make sure you have an encryption key set in your `config.php` file. Change the `$config['encryptionKey']` variable to a non empty value, this setting is important for Phile's encryption and should be unique to each installation. It is very important that you never change this setting after you set it the first time.

In case you missed that, here it is again:

**WARNING: DO NOT CHANGE YOUR ENCRYPTION KEY AFTER SETTING IT FOR THE FIRST TIME**

Note: It is important that the value of this setting is a secure and complex string, as it is used to encrypt passwords.

You can use Phile's built in <a href="generator.php">generator</a> to create a secure value for your installation.
