<?php 
require 'login.php';
require_once("data-config.php");
if(isset($_POST['vote'])){
  $points = $_POST['vote'];
  /* This should not be needed anymore.
  switch($_POST['vote']){
    case 'Satisfactory Answer':
      $points = 1;
      break;
    case 'Excellent Answer':
      $points = 2;
      break;
    case 'Alarming Answer':
      $points = -1;
      break;
    default:
      $points = 0;
  }
 */

$vote = "INSERT INTO int_qadm (vote_comment,r_dm_id,r_a_id,r_q_id,vote_int) VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE vote_comment = ?, vote_int = ?";

 //$vote = "INSERT INTO int_qadm (vote_comment,r_dm_id,r_a_id,r_q_id,vote_int) VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE vote_comment=?, vote_int=?";
	$stmt = $con->prepare($vote) or die($con->error);
$stmt->bind_param('sssiisi', $_POST['comment'],$_POST['dm'],$_POST['character'],$_POST['question'],$_POST['vote'],$_POST['comment'],$_POST['vote']) or die($con->error);	//,$_POST['vote']

//$stmt->bind_param('sssssss', $_POST['comment'],$_POST['dm'],$_POST['character'],$_POST['vote'], $_POST['comment'], $_POST['question']) or die($con->error);	


$stmt->execute() or die($con->error);
return $vote;
}  

