<?php
$action = $_REQUEST['action'];

if (!empty($action)) {
    require_once 'includes/rental.php';
    $obj = new rental();
}

if ($action == 'adduser' && !empty($_POST)) {
    $pname = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $photo = $_FILES['photo'];
    $rentalId = (!empty($_POST['userid'])) ? $_POST['userid'] : '';

    // file (photo) upload
    $imagename = '';
    if (!empty($photo['name'])) {
        $imagename = $obj->uploadPhoto($photo);
        $rentalData = [
            'pname' => $pname,
            'email' => $email,
            'phone' => $phone,
            'photo' => $imagename,
        ];
    } else {
        $rentalData = [
            'pname' => $pname,
            'email' => $email,
            'phone' => $phone,
        ];
    }

    if ($rentalId) {
        $obj->update($rentalData, $rentalId);
    } else {
        $rentalId = $obj->add($rentalData);
    }

    if (!empty($rentalId)) {
        $rental = $obj->getRow('id', $rentalId);
        echo json_encode($rental);
        exit();
    }
}

if ($action == "getusers") {
    $page = (!empty($_GET['page'])) ? $_GET['page'] : 1;
    $limit = 4;
    $start = ($page - 1) * $limit;

    $rentals = $obj->getRows($start, $limit);
    if (!empty($rentals)) {
        $rentalslist = $rentals;
    } else {
        $rentalslist = [];
    }
    $total = $obj->getCount();
    $rentalArr = ['count' => $total, 'rentals' => $rentalslist];
    echo json_encode($rentalArr);
    exit();
}

if ($action == "getuser") {
    $rentalId = (!empty($_GET['id'])) ? $_GET['id'] : '';
    if (!empty($rentalId)) {
        $rental = $obj->getRow('id', $rentalId);
        echo json_encode($rental);
        exit();
    }
}

if ($action == "deleteuser") {
    $rentalId = (!empty($_GET['id'])) ? $_GET['id'] : '';
    if (!empty($rentalId)) {
        $isDeleted = $obj->deleteRow($rentalId);
        if ($isDeleted) {
            $message = ['deleted' => 1];
        } else {
            $message = ['deleted' => 0];
        }
        echo json_encode($message);
        exit();
    }
}

if ($action == 'search') {
    $queryString = (!empty($_GET['searchQuery'])) ? trim($_GET['searchQuery']) : '';
    $results = $obj->searchrental($queryString);
    echo json_encode($results);
    exit();
}
