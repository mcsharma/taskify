<?hh
// Nothing must be placed before this call.
set_error_handler(get_error_handler());

require_once('api/ApiServer.php');

if ($_ENV['dev']) {
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


function get_error_handler() {
  return function ($errorNumber, $message, $errfile, $errline) {
    switch ($errorNumber) {
        case E_ERROR:
            $errorLevel = 'Error';
            break;
        case E_WARNING:
            $errorLevel = 'Warning';
            break;
        case E_NOTICE:
            $errorLevel = 'Notice';
            break;
        default:
            $errorLevel = 'Undefined';
    }
    echo '<br/><b>'.$errorLevel.'</b>: '.$message.' in <b>'.
      $errfile.'</b> on line <b>'.$errline.'</b><br/>';
  };
}
