<?php
require_once get_stylesheet_directory() . '/custom/api/class-api-manager.php';
class apiAuthentication {
    public function curlrequest($username, $password) {
        try {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => AUTHENTICATION_API,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_HEADER => 1,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_POSTFIELDS => "emailId=" . urlencode($username) . "&password=" . urlencode($password),
                CURLOPT_HTTPHEADER => array(
                    "appnName: ZING",
                    "authKey: 1lxVJZ/89KCNhoQzfds+URrveGLhwPcGap2w5F6WeIMPVNU8i6a/tldiWHQOfYnNn8xs8Y",
                    "Content-Type: application/x-www-form-urlencoded",
                    "Origin: ".API_ORIGIN_NAME
                ),
            ));
            $response = curl_exec($curl);
            if (curl_errno($curl)) {
                error_log("Authentication API Curl error: " . curl_error($curl));
                curl_close($curl);
                return null;
            }
            $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            $header = substr($response, 0, $header_size);
            $body = substr($response, $header_size);

            curl_close($curl);

            // Extract cookies/values from headers
            preg_match('/JSESSIONID=([^;]+)/', $header, $jsessionid_match);
            preg_match('/c_in=([^;]+)/', $header, $c_in_match);
            preg_match('/Expires=([^;]+)/', $header, $expiry_match);
            preg_match('/Authorizationkey:\s*(.*)/i', $header, $authkey_match);

            $jsessionid_val = $jsessionid_match[1] ?? '';
            $c_in = $c_in_match[1] ?? '';
            $expiry = isset($expiry_match[1]) ? strtotime(trim($expiry_match[1])) : null;
            $i_s_val = trim($authkey_match[1] ?? '');

            $result = json_decode($body, true);

            if (empty($result)) {
                error_log("Authentication API Response: No data for username = $username");
                return null;
            }

            $user_info = [];

            foreach ($result as $val) {
                $data = $val['data'] ?? [];

                $user_info['jsessionid_val'] = $jsessionid_val;
                $user_info['message'] = $val['message'] ?? '';

                $user_info['first_name'] = $data['firstName'] ?? '';
                if (empty($user_info['first_name'])) error_log("Missing firstName for user = $username");

                $user_info['last_name'] = $data['surname'] ?? '';
                if (empty($user_info['last_name'])) error_log("Missing surname for user = $username");

                $user_info['email'] = $data['emailId'] ?? '';
                if (empty($user_info['email'])) error_log("Missing emailId for user = $username");

                $user_info['branch'] = $data['branchName'] ?? '';
                if (empty($user_info['branch'])) error_log("Missing branchName for user = $username");

                $user_info['location'] = $data['locationName'] ?? '';
                if (empty($user_info['location'])) error_log("Missing locationName for user = $username");

                $user_info['designation'] = $data['designation'] ?? '';
                if (empty($user_info['designation'])) error_log("Missing designation for user = $username");

                $user_info['department'] = $data['deptGroupName'] ?? '';
                if (empty($user_info['department'])) error_log("Missing deptGroupName for user = $username");

                $user_info['sap'] = $data['sapNumber'] ?? '';
                if (empty($user_info['sap'])) error_log("Missing sapNumber for user = $username");

                $user_info['i_s'] = $i_s_val;
                $user_info['i_s_expiry'] = $expiry ?? null;
                if (empty($i_s_val)) error_log("Missing i_s for user = $username");

                $user_info['c_in'] = $c_in;
                $user_info['c_in_expiry'] = $expiry ?? null;
                if (empty($c_in)) error_log("Missing c_in for user = $username");

                // Optional: Fetch manager email
                /*
                try {
                    $apiManager = new apiManager();
                    $manager_email = $apiManager->curlrequest($i_s_val, $jsessionid_val);
                    if (!empty($manager_email)) {
                        $user_info['manager_email'] = $manager_email;
                    } else {
                        error_log("Manager email not fetched for user = $username");
                    }
                } catch (Exception $e) {
                    error_log("Manager API Exception: " . $e->getMessage());
                }
                */

                // Return after the first result (assuming only one relevant)
                break;
            }
         return $user_info;
// 			   do_action('after_api_authentication', $user_info);

        } catch (Exception $e) {
            error_log("Authentication API Exception for username = $username : " . $e->getMessage());
            return null;
        }
    }
}
?>