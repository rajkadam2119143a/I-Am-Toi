<?php
class apiManager {
	public function curlrequest($i_s, $jsessionid) {
		$url_decoded_i_s = urldecode($i_s);
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => MANAGER_API,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_HEADER => 1,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_SSL_VERIFYPEER => 0,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_HTTPHEADER => array(
				'Authorization: ' . $url_decoded_i_s,
				'Cookie: JSESSIONID=' . $jsessionid
			),
		));

		$response = curl_exec($curl);

		if (curl_errno($curl)) {
			error_log("Manager API Curl error: " . curl_error($curl));
			curl_close($curl);
			return null;
		}

		$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		$body = substr($response, $header_size);
		curl_close($curl);

		error_log("Manager API Raw Response Body: " . $body);

		$result = json_decode($body, true);

		if (empty($result) || !isset($result[0]['data'][0])) {
			error_log("Manager API Error: Invalid response structure for i_s=$i_s");
			return null;
		}

		$manager_data = $result[0]['data'][0]; // Assuming it's a flat array

		// Log and return email if it exists
		if (!empty($manager_data[1])) {
			$manager_email = $manager_data[1];
			error_log("Manager API Success: manager_email = $manager_email for i_s=$i_s");
			return $manager_email;
		} else {
			error_log("Manager API Warning: Email not found in response for i_s=$i_s");
			return null;
		}
	}
}
?>