<?php

include("../../db_connect_info/db.php");

header('Access-Control-Allow-Origin: *');

// MANUAL ADMIN CREATION - EDIT AND UNCOMMENT BELOW THEN COPY AND PASTE THE RESULT INTO SQL
//echo "INSERT INTO chrm_backend_master_user (id, email, password, metamask_id, username, status, password_reset_status, user_type) VALUES (NULL, '".aes_encrypt('ngupta@chaarmi.com')."', '".aes_encrypt('12345678')."', NULL, '".aes_encrypt('admin')."', NULL, NULL, '".aes_encrypt('metaverse_owner')."')";

echo aes_encrypt("views").",<br>".aes_encrypt("0");

?>