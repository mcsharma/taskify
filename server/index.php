<?hh

require_once('api/ApiServer.php');

// Note(mahesh): These are saved in conf.d/server-environment-dev.conf file
// inside the apache2's directory.
if (getEnv('ENVIRONMENT') === 'DEV') {
    header("Access-Control-Allow-Origin: *");
}

$path = trim($_SERVER['PATH_INFO'], '/');
$query_string = urldecode($_SERVER['QUERY_STRING']);

$params_map = Map {};
if ($query_string !== '') {
  $params = explode('&', $query_string);
  foreach ($params as $param) {
    $key_and_value = explode('=', $param);
    $key = array_shift($key_and_value);
    $value = count($key_and_value) > 0 ? $key_and_value[0] : null;
    $params_map[$key] = $value;
  }
}
if (substr($path, 0, 4) === 'api/') {
  $api_path = substr($path, 4);
  try {
    $res = \HH\Asio\join(ApiServer::genResponseJson($api_path, new ImmMap($params_map)));
    echo $res;
  } catch (Exception $e) {
    echo $e->getMessage();
  }
} else {
  echo 'Taskify API Server';
}
