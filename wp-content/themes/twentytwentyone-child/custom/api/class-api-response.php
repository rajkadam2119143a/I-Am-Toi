<?php

require_once get_stylesheet_directory() . '/custom/api/class-api-manager.php';

class apiResponse {

	public function curlresponse() {
		$api_response = [];

		try {
			// Get cookie values
			$i_s = isset($_COOKIE['i_s']) ? trim(stripslashes($_COOKIE['i_s']), '"') : "";
			$c_in = isset($_COOKIE['c_in']) ? trim(stripslashes($_COOKIE['c_in']), '"') : "";

			// Start CURL
			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => RESPONSE_API,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_HEADER => 1,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_SSL_VERIFYHOST => 0,
				CURLOPT_SSL_VERIFYPEER => 0,
				CURLOPT_COOKIE => "i_s=" . $i_s . "; c_in=" . $c_in,
				CURLOPT_HTTPHEADER => array(
					"appnName: ZING",
					"authKey: 1lxVJZ/89KCNhoQzfds+URrveGLhwPcGap2w5F6WeIMPVNU8i6a/tldiWHQOfYnNn8xs8Y",
					"Content-Type: application/x-www-form-urlencoded",
					"Origin: ".API_ORIGIN_NAME
				),
			));

			$response = curl_exec($curl);

			if (curl_errno($curl)) {
				error_log("Curl error: " . curl_error($curl));
				curl_close($curl);
				return $api_response;
			}

			$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
			$header = substr($response, 0, $header_size);
			$body = substr($response, $header_size);

			curl_close($curl);

			$result = json_decode($body, true);

			// Extract JSESSIONID
			$jsessionid_val = '';
			if (preg_match('/JSESSIONID=([^;]+);/', $header, $matches)) {
				$jsessionid_val = $matches[1];
			}

			if (!empty($result) && is_array($result)) {
				foreach ($result as $val) {
					if (isset($jsessionid_val)) {
						$api_response['jsessionid_val'] = $jsessionid_val;
					}

					$fields = [
						'firstName'     => 'first_name',
						'surname'       => 'last_name',
						'emailId'       => 'email',
						'branchName'    => 'branch',
						'locationName'  => 'location',
						'designation'   => 'designation',
						'deptGroupName' => 'department',
						'sapNumber'     => 'sap'
					];

					foreach ($fields as $key => $field) {
						if (!empty($val['data'][$key])) {
							$api_response[$field] = $val['data'][$key];
						} else {
							error_log("Cookie API Response: data.$key is empty for i_s=$i_s | c_in=$c_in");
						}
					}

					$api_response['i_s'] = $i_s;
					$api_response['c_in'] = $c_in;

					if (!empty($val['status']) && $val['status'] === 'success') {
						$api_response['resp'] = 'true';
					} else {
						error_log("Cookie API Response: status is " . ($val['status'] ?? 'empty') . " for i_s=$i_s | c_in=$c_in");
					}
				}

				// Optional: Call Manager API (commented out)
				/*
				try {
					$apiManager = new apiManager();
					$api_manager_response = $apiManager->curlrequest($i_s, $jsessionid_val);
					if (!empty($api_manager_response)) {
						$api_response['manager_email'] = $api_manager_response;
					} else {
						error_log("Manager API returned empty for i_s=$i_s");
					}
				} catch (Exception $e) {
					error_log("Manager API Exception: " . $e->getMessage());
				}
				*/

			} else {
				error_log("Cookie API Response: No valid data fetched for i_s=$i_s");
			}

		} catch (Exception $e) {
			error_log("Cookie API Response Exception: " . $e->getMessage() . " | i_s=$i_s");
		}
		
		/**
		 * 🔹 Trigger a hook so functions.php can listen and update ACF fields
		 */
// 		do_action('after_api_authentication', $api_response);

		return $api_response;
	}
}
?>