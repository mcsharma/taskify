<?hh // strict

final class ApiLogin {

  public static async function genLogin(
    ?int $fbUserID,
    ?string $fbAccessToken,
  ): Awaitable<Map<string, mixed>> {
    if ($fbUserID === null || $fbAccessToken === null) {
      throw new Exception('Invalid credential');
    }
    $curl = curl_init();
    // Set some options - we are passing in a useragent too here
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        // TODO check for sanity in access_token, like it should only consist
        // of letters and numbers
        CURLOPT_URL => 'https://graph.facebook.com/me?fields=first_name,last_name&access_token='.$fbAccessToken,
    ));
    // Send the request & save response to $resp
    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    // Close request to clear up some resources
    curl_close($curl);

    if ($response === false) {
      throw new Exception('Could not connect to facebook');
    }
    if ($http_code !== 200) {
      throw new Exception('Invalid credentials');
    }
    $res_json = json_decode($response, true);
    if (idx($res_json, 'id') != $fbUserID) {
      throw new Exception('Invalid credentials');
    }
    // Credentials are valid at this point, generate our own token, store it
    // DB and send it in the response.

    $data_for_fbid = await TaskifyDB::genHashValue('fbid_to_user', (string)$fbUserID);
    $user_id = null;
    if ($data_for_fbid !== null && $data_for_fbid->containsKey('id')) {
      $user_id = (int)$data_for_fbid['id'];
      invariant(IDUtil::isValidID($user_id), 'must be a valid ID');
    }

    $is_new_user = $user_id === null;
    if ($is_new_user) {
      // Create a new user and map its fbid to the newly generated user ID.
      $user_id = await TaskifyDB::genCreateNode(NodeType::USER, Map {
        'fbid' => $res_json['id'],
        'first_name' => $res_json['first_name'],
        'last_name' => $res_json['last_name'],
      });
      await TaskifyDB::genSetHash('fbid_to_user', (string)$fbUserID, Map {
        'id' => $user_id,
      });
    }
    invariant(is_int($user_id), 'must be a valid user ID at this point');

    $access_token = null;
    if (!$is_new_user) {
      // If it is not a new user, checking if we already have a valid token
      // if so, we will simply return that.
      $token_data = await TaskifyDB::genHashValue('user_id_to_token', (string)$user_id);
      if ($token_data !== null && $token_data->containsKey('token')) {
        $access_token = $token_data['token'];
      }
    }
    if ($access_token === null) {
      // finally we generate access token if it is a valid user and don't have
      // a valid token currently.
      $access_token = base64_encode(
        $user_id.':'.bin2hex(openssl_random_pseudo_bytes(16)),
      );
      await TaskifyDB::genSetHash('user_id_to_token', (string)$user_id, Map {
        'token' => $access_token,
      });
    }
    invariant(is_string($access_token), 'token must be a non-null string at this point');
    return Map {
      'fbid' => $fbUserID,
      'viewerID' => $user_id,
      'authToken' => $access_token,
    };
  }
}
