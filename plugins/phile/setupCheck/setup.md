<!--
Title: Setup
Description: Setup PhileCMS
-->

## Welcome to the PhileCMS Setup

Congratulations, you have successfully installed [Phile](https://github.com/PhileCMS/Phile)!


### Set an Encryption Key ###

To proceed you have to set an encryption key. You configure it as `$config['encryptionKey']` in your `config.php` file.

This key is important for Phile's encryption and should be unique to each installation. It is very important that you never change the key after you set it the first time.

**WARNING: DO NOT CHANGE YOUR ENCRYPTION KEY AFTER SETTING IT FOR THE FIRST TIME!**

The encryption key should be a long and complex string. Here's a random key ready to use:

```
{{ encryption_key }}
```