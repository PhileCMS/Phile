/* This is a nix package manager configuration file. It installs a PHP
 environment for development witgh "nix-shell". Start a server
 in the shell with "php -S localhost:8080". See: https://nixos.org/
 */
{ pkgs ? import <nixpkgs> { } }:
let
  myPhp = pkgs.php80.buildEnv {
    extensions = { all, ... }: with all; [
      dom
      filter
      imagick
      mbstring
      opcache
      openssl
      session
      simplexml
      tokenizer
      xdebug
      xmlwriter
      zip
    ];
    extraConfig = ''
      memory_limit=256M
      xdebug.mode = debug
      xdebug.start_with_request = yes
    '';
  };
in
pkgs.mkShell {
  nativeBuildInputs = [
    myPhp
    pkgs.php80Packages.composer
  ];
}
