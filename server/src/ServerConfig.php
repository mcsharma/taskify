<?hh 

final class ServerConfig {
  public static function getDBHost(): string {
    return $_SERVER['db_host'];
  }

  public static function getDBPassword(): string {
    return $_SERVER['db_password'];
  }
}
