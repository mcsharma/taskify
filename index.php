<?hh

require('TaskifyDB.php');
require('models/User.php');
require('models/Task.php');

require('api/ApiServer.php');
require_once('models/UserToTasksEdge.php');

$path = trim($_SERVER['PATH_INFO'], '/');
$query_string = $_SERVER['QUERY_STRING'];
$params_map = Map {};
if ($query_string !== '') {
  $params = explode('&', $query_string);
  foreach ($params as $param) {
    list($key, $value) = explode('=', $param);
    $params_map[$key] = $value;
  }
}

if (substr($path, 0, 4) === 'api/') {
  $api_path = substr($path, 4);
  try {
    // $nodes =  \HH\Asio\join((new UserToTasksEdge(1))->genNodes());
    // echo '<pre>'.json_encode($nodes, JSON_PRETTY_PRINT).'</pre>';
    $res = \HH\Asio\join(ApiServer::genResponseJson($api_path, $params_map));
    echo '<pre>'.$res.'</pre>';
  } catch (Exception $e) {
    var_dump($e->getMessage());
  }
}
// echo
// '<html>
// <head>
//   <script type="text/javascript" src="public/bundle.js"></script>
// </head>
// <body>
//   <div id="root"></div>
// </body>
// </html>';
