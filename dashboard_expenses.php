<?php
session_start();
include 'connect.php';
$user_id = $_SESSION['user_id'];

/* ✅ Recommended plan fixed categories */
$recommended = ["Arrival","Leisure","Adventure","Sightseeing"];

// Sab categories (recommended + user-added distinct)
$sql = "SELECT DISTINCT category FROM expenses WHERE user_id=$user_id";
$res = mysqli_query($conn,$sql);
while($r=mysqli_fetch_assoc($res)){
    if(!in_array($r['category'],$recommended)){
        $recommended[] = $r['category'];
    }
}

// Final arrays
$labels = [];
$amounts = [];

// Har category ka sum nikalo
foreach($recommended as $cat){
    $q = mysqli_query($conn,"SELECT SUM(amount) as total 
                             FROM expenses 
                             WHERE user_id=$user_id 
                             AND category='".mysqli_real_escape_string($conn,$cat)."'");
    $row = mysqli_fetch_assoc($q);
    $labels[] = $cat;
    $amounts[] = $row['total'] ?? 0;
}

echo json_encode(["labels"=>$labels,"amounts"=>$amounts]);
