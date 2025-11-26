{ pkgs, lib, config, inputs, ... }:

{
  # https://devenv.sh/basics/
  env.GREET = "Webshop EDV Graz Development Environment";

  # Enable dotenv integration
  dotenv.enable = true;

  # https://devenv.sh/packages/
  packages = [
    pkgs.git
    pkgs.curl
  ];

  # https://devenv.sh/languages/
  languages.php = {
    enable = true;
    package = pkgs.php83;
    extensions = [ "pdo_mysql" "openssl" "mbstring" "curl" ];
    ini = ''
      memory_limit = 256M
      display_errors = On
      error_reporting = E_ALL
      session.cookie_httponly = 1
      session.use_only_cookies = 1
    '';
    fpm.pools.web = {
      settings = {
        "clear_env" = "no";
        "pm" = "dynamic";
        "pm.max_children" = 10;
        "pm.start_servers" = 2;
        "pm.min_spare_servers" = 1;
        "pm.max_spare_servers" = 5;
      };
    };
  };

  # https://devenv.sh/processes/
  processes = {
    webserver.exec = "php -S localhost:8000 -t View";
  };

    services.mysql = {
      enable = true;
      package = pkgs.mysql80;
      initialDatabases = [{ name = "webshop_edv"; }];
      ensureUsers = [
        {
          name = "webshop";
          password = "webshop";
          ensurePermissions = {
            "webshop_edv.*" = "ALL PRIVILEGES";
          };
        }
      ];
    };

  # https://devenv.sh/scripts/
  scripts.setup.exec = ''
    echo "Setting up Webshop EDV Graz with MySQL..."
    echo ""
    echo "Make sure MySQL is running (devenv up in another terminal)"
    echo ""
    echo "Running MySQL setup script..."
    php Model/setup.php

    echo ""
    echo "✓ Setup complete!"
    echo ""
    echo "Visit: http://localhost:8000"
    echo "Login: admin / Pass1234word"
  '';

  # https://devenv.sh/pre-commit-hooks/
  git-hooks.hooks = {
    # PHP linting
    phpcs = {
      enable = false; # Enable if you want PHP CodeSniffer
    };
  };

  # Environment variables
  env.DB_TYPE = "mysql";
  env.DB_HOST = "127.0.0.1";
  env.DB_NAME = "webshop_edv";
  env.DB_USER = "webshop";
  env.DB_PASS = "webshop";
  env.APP_ENV = "development";
  env.APP_DEBUG = "true";
}
