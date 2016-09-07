<?hh

require_once('hack/api/ApiServer.php');

$path = trim($_SERVER['PATH_INFO'], '/');
$query_string = $_SERVER['QUERY_STRING'];
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
    echo '<pre>'.$res.'</pre>';
  } catch (Exception $e) {
    echo $e->getMessage();
  }
}
echo
'<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Taskify</title>
    </head>
    <body>
        <div id="root-container"></div>

        <!-- Dependencies -->

        <!-- Main -->
        <link rel="stylesheet" type="text/css" href="./public/dist/bundle.css"></link>
        <script src="./public/node_modules/react/dist/react.js"></script>
        <script src="./public/node_modules/react-dom/dist/react-dom.js"></script>
        <script src="./public/dist/bundle.js"></script>
    </body>
</html>';
