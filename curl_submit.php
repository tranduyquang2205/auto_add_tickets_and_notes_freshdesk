<?php
include 'func.php';
$file = 'kho.txt';
$searchfor = $_GET['kho'];
$contents = file_get_contents($file);
$pattern = preg_quote($searchfor, '/');
$pattern = "/^.*$pattern.*\$/m";
if(preg_match_all($pattern, $contents, $matches)){
   $kho =  implode("\n", $matches[0]);
}

		$nguon=$_POST['cf_ngun_ticket'];
		$total = $_GET['total'];
		$madh=$_GET['madh'];
		$subject=$_POST['subject'];
		$note=$_POST['note'];
		$ticket_type=$_POST['ticket_type'];
		$description=$_POST['description'];
		$hopdong = $_POST['cf_loi_yu_cu_ca_khch_hng'];
        
		if(isset($_POST['cf_lu_hnh_ni_b']))
		{
		    $noibo = true;
		}else{
		    $noibo = false;
		}
		$email = $_POST['email'];
		if(isset($_POST['cf_l_do_t_vn'])){
		    $lydo=$_POST['cf_l_do_t_vn'];
		}
		if(isset($_POST['cf_hi_giaolytr_hng'])){
		    $lydo=$_POST['cf_hi_giaolytr_hng'];
		}
		if(isset($_POST['cf_l_do_thay_i_thng_tin'])){
		    $lydo=$_POST['cf_l_do_thay_i_thng_tin'];
		}
		$custom_fields = array(
			"cf_ngun_ticket" => $nguon,
			"cf_l_do_t_vn" => $lydo,
			"cf_hi_giaolytr_hng" => $lydo,
			"cf_l_do_thay_i_thng_tin" => $lydo,
			"cf_m_n_hng_new" => $madh,
			"cf_loi_yu_cu_ca_khch_hng" => $hopdong,
			"cf_tn_kho" => $kho,
			"cf_danh_sch_kho" => "Chọn",
			"cf_lu_hnh_ni_b" => $noibo,
			
		);
if($_GET['agent']=="2043029185739"){
			$api_key = "DctFBnSmwPUvfqXAOyB";}else
if($_GET['agent']=="2043029185711"){
			$api_key = "Sue123CPofUwygzGfRgh";}
			
			$yourdomain = "ghncs";
		$ticket_data = json_encode(array(
		    "subject" => $subject,
			"description" =>  $description,
			"responder_id" => (int)$_GET['agent'],
			"group_id" => 2043001015368,
			"email_config_id" => null,
			"type" => $ticket_type,
			"priority" => (int)$_POST['priority'],
			"email" => $email,
			"status" => 2,
			"source" =>3,
			"custom_fields" => $custom_fields
		));


		$url = "https://$yourdomain.freshdesk.com/api/v2/tickets";
		$ch = curl_init($url);
		$header[] = "Content-type: application/json";
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_USERPWD, "$api_key");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $ticket_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$server_output = curl_exec($ch);
		$info = curl_getinfo($ch);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$headers = substr($server_output, 0, $header_size);
		$response = substr($server_output, $header_size);
			if($info['http_code'] == 201) {
				$ticket_id =  substr($headers,strpos($headers,'v2/tickets/')+11,7);
			}
		curl_close($ch);
		$response=json_decode($response, true);
        $response = ($response["errors"][0]["message"]);
		$ticket_data = json_encode(array(
			"group_id" => 2043001015368,
		));	
		$url = "https://$yourdomain.freshdesk.com/api/v2/tickets/$ticket_id";
		$ch = curl_init($url);
		$header[] = "Content-type: application/json";
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_USERPWD, "$api_key");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $ticket_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$server_output = curl_exec($ch);
		$info = curl_getinfo($ch);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$headers = substr($server_output, 0, $header_size);
		$response = substr($server_output, $header_size);
		curl_close($ch);
		$url = "https://$yourdomain.freshdesk.com/api/v2/tickets/$ticket_id/notes";
$note_payload = array(
  'body' => $note
);
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_USERPWD, "$api_key");
curl_setopt($ch, CURLOPT_POSTFIELDS, $note_payload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$server_output = curl_exec($ch);
$info = curl_getinfo($ch);
$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($server_output, 0, $header_size);
$response = substr($server_output, $header_size);
if($_SESSION["stt"]==$total){
$_SESSION["stt"]=1;
}else{
$_SESSION["stt"]++;
}
			if($info['http_code'] < 300) {
			    upload_ticket($ticket_id,$_GET['agent'],$email,$madh,0);
				$message =  "Tạo thành công Ticket ".$_SESSION["stt"]."/".$total;
			$jsondata =	array(
                'status' => 'success',
                'message'=> $message
                        );
			}else{
			   $message =  "Lỗi khi tạo Ticket ".$_SESSION["stt"]."/".$total;
			   	$jsondata =	array(
                'status' => 'error',
                'message'=> $message
                        );
			}
		curl_close($ch);
		echo json_encode($jsondata);
?>