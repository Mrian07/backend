<?php

include_once('../common.php');
if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
// echo "<pre>"; print_r($_REQUEST); die;
$section = isset($_REQUEST['section']) ? $_REQUEST['section'] : '';
//$searchData = isset($_REQUEST['searchData']) ? $_REQUEST['searchData'] : '';
$sortby = isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : 0;
$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
$option = isset($_REQUEST['option']) ? $_REQUEST['option'] : "";
$keyword = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : "";
$startDate = isset($_REQUEST['startDate']) ? $_REQUEST['startDate'] : "";
$endDate = isset($_REQUEST['endDate']) ? $_REQUEST['endDate'] : "";
$type = isset($_REQUEST['exportType']) ? $_REQUEST['exportType'] : '';
$ssql = "";
require('fpdf/fpdf.php');

function cleanData(&$str) {
    $str = preg_replace("/\t/", "\\t", $str);
    $str = preg_replace("/\r?\n/", "\\n", $str);
    if (strstr($str, '"'))
        $str = '"' . str_replace('"', '""', $str) . '"';
}

if ($section == 'admin') {
	
	$ord = ' ORDER BY ad.iAdminId DESC';
	if($sortby == 1){
	  if($order == 0)
	  $ord = " ORDER BY ad.vFirstName ASC";
	  else
	  $ord = " ORDER BY ad.vFirstName DESC";
	}

	if($sortby == 2){
	  if($order == 0)
	  $ord = " ORDER BY ad.vEmail ASC";
	  else
	  $ord = " ORDER BY ad.vEmail DESC";
	}

	if($sortby == 3){
	  if($order == 0)
	  $ord = " ORDER BY ag.vGroup ASC";
	  else
	  $ord = " ORDER BY ag.vGroup DESC";
	}

	if($sortby == 4){
	  if($order == 0)
	  $ord = " ORDER BY ad.eStatus ASC";
	  else
	  $ord = " ORDER BY ad.eStatus DESC";
	}
	//End Sorting

	// $adm_ssql = "";
	// if (SITE_TYPE == 'Demo') {
		// $adm_ssql = " And ad.tRegistrationDate > '" . WEEK_DATE . "'";
	// }

    if ($keyword != '') {
        if ($option != '') {
            if (strpos($option, 'eStatus') !== false) {
                $ssql .= " AND " . stripslashes($option) . " LIKE '" . stripslashes($keyword) . "'";
            } else {
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
            $ssql .= " AND (concat(ad.vFirstName,' ',ad.vLastName) LIKE '%".$keyword."%' OR ad.vEmail LIKE '%".$keyword."%' OR ag.vGroup LIKE '%".$keyword."%' OR ad.vContactNo LIKE '%".$keyword."%' OR ad.eStatus LIKE '%".$keyword."%')";
        }
    }

    $sql = "SELECT ad.iAdminId as Id, CONCAT(ad.vFirstName,' ',ad.vLastName) as Name,ad.vEmail as Email,ag.vGroup as `Admin Roles`, ad.vContactNo as Mobile,ad.eStatus as Status FROM administrators AS ad LEFT JOIN admin_groups AS ag ON ad.iGroupId=ag.iGroupId where ad.eStatus != 'Deleted' $ssql $ord";
    //die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        // $result = mysql_query($sql) 
		$result = $obj->MySQLSelect($sql) or die('Query failed!');
		echo implode("\t", array_keys($result[0])) . "\r\n";
		
		foreach($result as $value){
			foreach($value as $key=>$val) {
			// echo $key.' => '.$val;
				if($key == 'Name'){
					$val = $generalobjAdmin->clearName($val);
				}
				if($key == 'Email'){
					$val = $generalobjAdmin->clearEmail($val);
				}
				if($key == 'Mobile'){
					$val = $generalobjAdmin->clearPhone($val);
				}
				echo $val."\t";
			}
			echo "\r\n";
		}
    } else {
        $heading = array('Id', 'Name', 'Email', 'Admin Roles', 'Mobile', 'Status');
        $result = mysql_query($sql);
        while ($row = mysql_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Admin Users");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Id') {
                $pdf->Cell(10, 10, $column_heading, 1);
            } else if ($column_heading == 'Mobile') {
                $pdf->Cell(30, 10, $column_heading, 1);
            } else if ($column_heading == 'Status') {
                $pdf->Cell(25, 10, $column_heading, 1);
            } else {
                $pdf->Cell(45, 10, $column_heading, 1);
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
				$values = $key;
				if($column == 'Name'){
					$values = $generalobjAdmin->clearName($key);
				}
				if($column == 'Email'){
					$values = $generalobjAdmin->clearEmail($key);
				}
				if($column == 'Mobile'){
					$values = $generalobjAdmin->clearPhone($key);
				}
				
                if ($column == 'Id') {
                    $pdf->Cell(10, 10, $values, 1);
                } else if ($column == 'Mobile') {
                    $pdf->Cell(30, 10, $values, 1);
                } else if ($column == 'Status') {
                    $pdf->Cell(25, 10, $values, 1);
                } else {
                    $pdf->Cell(45, 10, $values, 1);
                }
            }
        }
        $pdf->Output('D');
    }
}



if ($section == 'company') {
	
	$ord = ' ORDER BY c.iCompanyId DESC';
	if($sortby == 1){
	  if($order == 0)
	  $ord = " ORDER BY c.vCompany ASC";
	  else
	  $ord = " ORDER BY c.vCompany DESC";
	}

	if($sortby == 2){
	  if($order == 0)
	  $ord = " ORDER BY c.vEmail ASC";
	  else
	  $ord = " ORDER BY c.vEmail DESC";
	}

	if($sortby == 4){
	  if($order == 0)
	  $ord = " ORDER BY c.eStatus ASC";
	  else
	  $ord = " ORDER BY c.eStatus DESC";
	}
	//End Sorting
	
    if ($keyword != '') {
        if ($option != '') {
			if (strpos($option, 'eStatus') !== false) {
				$ssql.= " AND ".stripslashes($option)." LIKE '".stripslashes($keyword)."'";
			}else {
				$ssql.= " AND ".stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
			}
        } else {
            $ssql.= " AND (c.vCompany LIKE '%".$keyword."%' OR c.vEmail LIKE '%".$keyword."%' OR c.vPhone LIKE '%".$keyword."%' OR c.eStatus LIKE '%".$keyword."%')";
        }
    }
	
	$cmp_ssql = "";
	// if (SITE_TYPE == 'Demo') {
		// $cmp_ssql = " And c.tRegistrationDate > '" . WEEK_DATE . "'";
	// }

    $sql = "SELECT c.iCompanyId AS Id, c.vCompany AS Name, c.vEmail AS Email,(SELECT count(rd.iDriverId) FROM register_driver AS rd WHERE rd.iCompanyId=c.iCompanyId) AS `Total Drivers`, CONCAT(c.vCode,'',c.vPhone) AS Mobile,c.eStatus AS Status FROM company AS c
        WHERE c.eStatus != 'Deleted' $ssql $cmp_ssql $ord";
		//die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = $obj->MySQLSelect($sql) or die('Query failed!');
		echo implode("\t", array_keys($result[0])) . "\r\n";
		
		foreach($result as $value){
			foreach($value as $key=>$val) {
			// echo $key.' => '.$val;
				if($key == 'Email'){
					$val = $generalobjAdmin->clearEmail($val);
				}
				if($key == 'Mobile'){
					$val = $generalobjAdmin->clearPhone($val);
				}
				if($key == 'Name'){
					$val = $generalobjAdmin->clearCmpName($val);
				}
				echo $val."\t";
			}
			echo "\r\n";
		}
    } else {
        $heading = array('Id', 'Name', 'Email', 'Total Drivers', 'Mobile', 'Status');
        $result = mysql_query($sql);
        while ($row = mysql_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Companies");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Id') {
                $pdf->Cell(10, 10, $column_heading, 1);
            } else if ($column_heading == 'Mobile') {
                $pdf->Cell(30, 10, $column_heading, 1);
            } else if ($column_heading == 'Status') {
                $pdf->Cell(25, 10, $column_heading, 1);
            } else {
                $pdf->Cell(45, 10, $column_heading, 1);
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
				$values = $key;
				if($column == 'Email'){
					$values = $generalobjAdmin->clearEmail($key);
				}
				if($column == 'Mobile'){
					$values = $generalobjAdmin->clearPhone($key);
				}
				if($column == 'Name'){
					$values = $generalobjAdmin->clearCmpName($key);
				}
                if ($column == 'Id') {
                    $pdf->Cell(10, 10, $values, 1);
                } else if ($column == 'Mobile') {
                    $pdf->Cell(30, 10, $values, 1);
                } else if ($column == 'Status') {
                    $pdf->Cell(25, 10, $values, 1);
                } else {
                    $pdf->Cell(45, 10, $values, 1);
                }
            }
        }
        $pdf->Output('D');
    }
}


if ($section == 'rider') {
	
	$rdr_ssql = "";
	if (SITE_TYPE == 'Demo') {
		$rdr_ssql = " And tRegistrationDate > '" . WEEK_DATE . "'";
	}
	
    if ($keyword != '') {
        if ($option != '') {
            if (strpos($option, 'eStatus') !== false) {
                $ssql .= " AND " . stripslashes($option) . " LIKE '" . stripslashes($keyword) . "'";
            } else {
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
            $ssql .= " AND (concat(ad.vFirstName,' ',ad.vLastName) LIKE '%".$keyword."%' OR ad.vEmail LIKE '%".$keyword."%' OR ad.vContactNo LIKE '%".$keyword."%' OR ad.eStatus LIKE '%".$keyword."%')";
        }
    }

    $sql = "SELECT iUserId as Id, CONCAT(vName,' ',vLastName) as Name,vEmail as Email,CONCAT(vPhoneCode,' ',vPhone) AS Mobile,eStatus as Status FROM register_user
        WHERE eStatus != 'Deleted' $ssql $rdr_ssql";
    //die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = $obj->MySQLSelect($sql) or die('Query failed!');
		echo implode("\t", array_keys($result[0])) . "\r\n";
		
		foreach($result as $value){
			foreach($value as $key=>$val) {
			// echo $key.' => '.$val;
				if($key == 'Name'){
					$val = $generalobjAdmin->clearName($val);
				}
				if($key == 'Email'){
					$val = $generalobjAdmin->clearEmail($val);
				}
				if($key == 'Mobile'){
					$val = $generalobjAdmin->clearPhone($val);
				}
				echo $val."\t";
			}
			echo "\r\n";
		}
    } else {
        $heading = array('Id', 'Name', 'Email', 'Mobile', 'Status');
        $result = mysql_query($sql);
        while ($row = mysql_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Admin Users");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Id') {
                $pdf->Cell(10, 10, $column_heading, 1);
            } else if ($column_heading == 'Mobile') {
                $pdf->Cell(30, 10, $column_heading, 1);
            } else if ($column_heading == 'Status') {
                $pdf->Cell(25, 10, $column_heading, 1);
            } else {
                $pdf->Cell(45, 10, $column_heading, 1);
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
				$values = $key;
				if($column == 'Name'){
					$values = $generalobjAdmin->clearName($key);
				}
				if($column == 'Email'){
					$values = $generalobjAdmin->clearEmail($key);
				}
				if($column == 'Mobile'){
					$values = $generalobjAdmin->clearPhone($key);
				}
                if ($column == 'Id') {
                    $pdf->Cell(10, 10, $values, 1);
                } else if ($column == 'Mobile') {
                    $pdf->Cell(30, 10, $values, 1);
                } else if ($column == 'Status') {
                    $pdf->Cell(25, 10, $values, 1);
                } else {
                    $pdf->Cell(45, 10, $values, 1);
                }
            }
        }
        $pdf->Output('D');
    }
}
//make 
if ($section == 'make') {
    if ($keyword != '') {
        if ($option != '') {
            if (strpos($option, 'eStatus') !== false) {
                $ssql .= " AND " . stripslashes($option) . " LIKE '" . stripslashes($keyword) . "'";
            } else {
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
            $ssql .= " AND vMake LIKE '%".$keyword."%' OR eStatus LIKE '%".$keyword."%'";
        }
    }

    $sql = "SELECT iMakeId as Id, vMake as Make, eStatus as Status FROM make where eStatus != 'Deleted' $ssql";
    //die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = mysql_query($sql) or die('Query failed!');
        while (false !== ($row = mysql_fetch_assoc($result))) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
    } else {
        $heading = array('Id', 'Make','Status');
        $result = mysql_query($sql);
        while ($row = mysql_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Make");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Id') {
                $pdf->Cell(45, 10, $column_heading, 1);
            } /* else if ($column_heading == 'Mobile') {
                $pdf->Cell(30, 10, $column_heading, 1);
            }*/ else if ($column_heading == 'Status') {
                $pdf->Cell(70, 10, $column_heading, 1);
            } else {
                $pdf->Cell(80, 10, $column_heading, 1); 
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                if ($column == 'Id') {
                    $pdf->Cell(45, 10, $key, 1);
                } /* else if ($column == 'Mobile') {
                    $pdf->Cell(30, 10, $key, 1);
                } */ else if ($column == 'Status') {
                    $pdf->Cell(70, 10, $key, 1);
                } else {
                    $pdf->Cell(80, 10, $key, 1);
                }
            }
        }
        $pdf->Output('D');
    }
}

//make 

//model
if ($section == 'model') {
    if ($keyword != '') {
        if ($option != '') {
            if (strpos($option, 'eStatus') !== false) {
                $ssql .= " AND " . stripslashes($option) . " LIKE '" . stripslashes($keyword) . "'";
            } else {
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
            $ssql .= " AND vTitle LIKE '%".$keyword."%' OR eStatus LIKE '%".$keyword."%'";
        }
    }

    $sql = "SELECT iModelId as Id, vTitle as Title, eStatus as Status FROM model where eStatus != 'Deleted' $ssql";
    //die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = mysql_query($sql) or die('Query failed!');
        while (false !== ($row = mysql_fetch_assoc($result))) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
    } else {
        $heading = array('Id', 'Title','Status');
        $result = mysql_query($sql);
        while ($row = mysql_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Model");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Id') {
                $pdf->Cell(45, 10, $column_heading, 1);
            } /* else if ($column_heading == 'Mobile') {
                $pdf->Cell(30, 10, $column_heading, 1);
            }*/ else if ($column_heading == 'Status') {
                $pdf->Cell(70, 10, $column_heading, 1);
            } else {
                $pdf->Cell(80, 10, $column_heading, 1); 
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                if ($column == 'Id') {
                    $pdf->Cell(45, 10, $key, 1);
                } /* else if ($column == 'Mobile') {
                    $pdf->Cell(30, 10, $key, 1);
                } */ else if ($column == 'Status') {
                    $pdf->Cell(70, 10, $key, 1);
                } else {
                    $pdf->Cell(80, 10, $key, 1);
                }
            }
        }
        $pdf->Output('D');
    }
}

//model

//country
if ($section == 'country') {
	
	$ord = ' ORDER BY iCountryId DESC';
	if($sortby == 1){
	  if($order == 0)
	  $ord = " ORDER BY vCountry ASC";
	  else
	  $ord = " ORDER BY vCountry DESC";
	}

	if($sortby == 2){
	  if($order == 0)
	  $ord = " ORDER BY vPhoneCode ASC";
	  else
	  $ord = " ORDER BY vPhoneCode DESC";
	}

	if($sortby == 3){
	  if($order == 0)
	  $ord = " ORDER BY eUnit ASC";
	  else
	  $ord = " ORDER BY eUnit DESC";
	}

	if($sortby == 4){
	  if($order == 0)
	  $ord = " ORDER BY eStatus ASC";
	  else
	  $ord = " ORDER BY eStatus DESC";
	}
	//End Sorting
	
	
    if ($keyword != '') {
        if ($option != '') {
            if (strpos($option, 'eStatus') !== false) {
                $ssql .= " AND " . stripslashes($option) . " LIKE '" . stripslashes($keyword) . "'";
            } else {
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
            $ssql .= " AND (vCountry LIKE '%".$keyword."%' OR vPhoneCode LIKE '%".$keyword."%' OR vCountryCodeISO_3 LIKE '%".$keyword."%' OR eStatus LIKE '%".$keyword."%')";
        }
    }

    $sql = "SELECT iCountryId as Id, vCountry as Country,vPhoneCode as PhoneCode, eUnit as Unit, eStatus as Status FROM country where eStatus != 'Deleted' $ssql";
    //die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = mysql_query($sql) or die('Query failed!');
        while (false !== ($row = mysql_fetch_assoc($result))) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
    } else {
        $heading = array('Id', 'Country','PhoneCode','Unit','Status');
        $result = mysql_query($sql);
        while ($row = mysql_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Country");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Id') {
                $pdf->Cell(18, 10, $column_heading, 1);
            } /* else if ($column_heading == 'Mobile') {
                $pdf->Cell(30, 10, $column_heading, 1);
            }*/ else if ($column_heading == 'Status') {
                $pdf->Cell(44, 10, $column_heading, 1);
            } else {
                $pdf->Cell(44, 10, $column_heading, 1); 
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                if ($column == 'Id') {
                    $pdf->Cell(18, 10, $key, 1);
                } /* else if ($column == 'Mobile') {
                    $pdf->Cell(30, 10, $key, 1);
                } */ else if ($column == 'Status') {
                    $pdf->Cell(44, 10, $key, 1);
                } else {
                    $pdf->Cell(44, 10, $key, 1);
                }
            }
        }
        $pdf->Output('D');
        //$pdf->Output();
    }
}


//State
if ($section == 'state') {
	
	$ord = ' ORDER BY s.iStateId DESC';
	if($sortby == 1){
	  if($order == 0)
	  $ord = " ORDER BY c.vCountry ASC";
	  else
	  $ord = " ORDER BY c.vCountry DESC";
	}

	if($sortby == 2){
	  if($order == 0)
	  $ord = " ORDER BY s.vState ASC";
	  else
	  $ord = " ORDER BY s.vState DESC";
	}

	if($sortby == 3){
	  if($order == 0)
	  $ord = " ORDER BY s.vStateCode ASC";
	  else
	  $ord = " ORDER BY s.vStateCode DESC";
	}

	if($sortby == 4){
	  if($order == 0)
	  $ord = " ORDER BY s.eStatus ASC";
	  else
	  $ord = " ORDER BY s.eStatus DESC";
	}
	//End Sorting
	
    if($keyword != ''){
		if($option != '') {
			if (strpos($option, 's.eStatus') !== false) {
				$ssql.= " AND ".stripslashes($option)." LIKE '".stripslashes($keyword)."'";
			}else {
				$ssql.= " AND ".stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
			}
		}else {
			$ssql.= " AND (c.vCountry LIKE '%".$keyword."%' OR s.vState LIKE '%".$keyword."%' OR s.vStateCode LIKE '%".$keyword."%' OR s.eStatus LIKE '%".$keyword."%')";
		}
	}

	$sql = "SELECT s.iStateId AS Id, s.vState AS State,s.vStateCode AS `State Code`,c.vCountry AS Country,s.eStatus
			FROM state AS s
			LEFT JOIN country AS c ON c.iCountryId = s.iCountryId
			WHERE s.eStatus !=  'Deleted' $ssql $ord";
    //die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = mysql_query($sql) or die('Query failed!');
        while (false !== ($row = mysql_fetch_assoc($result))) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
    } else {
        $heading = array('Id','State','State Code', 'Country','Status');
        $result = mysql_query($sql);
        while ($row = mysql_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "State");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Id') {
                $pdf->Cell(15, 10, $column_heading, 1);
            } /* else if ($column_heading == 'Mobile') {
                $pdf->Cell(30, 10, $column_heading, 1);
            }*/ else if ($column_heading == 'Status') {
                $pdf->Cell(40, 10, $column_heading, 1);
            } else {
                $pdf->Cell(40, 10, $column_heading, 1); 
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                if ($column == 'Id') {
                    $pdf->Cell(15, 10, $key, 1);
                } /* else if ($column == 'Mobile') {
                    $pdf->Cell(30, 10, $key, 1);
                } */ else if ($column == 'Status') {
                    $pdf->Cell(40, 10, $key, 1);
                } else {
                    $pdf->Cell(40, 10, $key, 1);
                }
            }
        }
        $pdf->Output('D');
        //$pdf->Output();
    }
}


//State
if ($section == 'city') {
	
	if($sortby == 1){
	  if($order == 0)
		$ord = " ORDER BY st.vState ASC";
	  else
		$ord = " ORDER BY st.vState DESC";
	}

	if($sortby == 2){
	  if($order == 0)
		$ord = " ORDER BY ct.vCity ASC";
	  else
		$ord = " ORDER BY ct.vCity DESC";
	}


	if($sortby == 3){
	  if($order == 0)
		$ord = " ORDER BY c.vCountry ASC";
	  else
		$ord = " ORDER BY c.vCountry DESC";
	}

	if($sortby == 4){
		if($order == 0)
			$ord = " ORDER BY ct.eStatus ASC";
		else
			$ord = " ORDER BY ct.eStatus DESC";
	}
	
    if($keyword != ''){
		if($option != '') {
			if (strpos($option, 'eStatus') !== false) {
				$ssql.= " AND ".stripslashes($option)." LIKE '".stripslashes($keyword)."'";
			}else {
				$ssql.= " AND ".stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
			}
		}else {
			$ssql.= " AND (ct.vCity LIKE '%".$keyword."%' OR st.vState LIKE '%".$keyword."%' OR c.vCountry LIKE '%".$keyword."%' OR ct.eStatus LIKE '%".$keyword."%')";
		}
	}

    $sql = "SELECT ct.iCityId AS Id,ct.vCity AS City,st.vState AS State,c.vCountry AS Country, ct.eStatus AS Status
			FROM city AS ct 
			left join country AS c ON c.iCountryId =ct.iCountryId
			left join state AS st ON st.iStateId=ct.iStateId
			WHERE  ct.eStatus != 'Deleted' $ssql $ord";
	
    //die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = mysql_query($sql) or die('Query failed!');
        while (false !== ($row = mysql_fetch_assoc($result))) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
    } else {
        $heading = array('Id', 'City','State','Country','Status');
        $result = mysql_query($sql);
        while ($row = mysql_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "City");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Id') {
                $pdf->Cell(18, 10, $column_heading, 1);
            } /* else if ($column_heading == 'Mobile') {
                $pdf->Cell(30, 10, $column_heading, 1);
            }*/ else if ($column_heading == 'Status') {
                $pdf->Cell(35, 10, $column_heading, 1);
            } else {
                $pdf->Cell(35, 10, $column_heading, 1); 
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                if ($column == 'Id') {
                    $pdf->Cell(18, 10, $key, 1);
                } /* else if ($column == 'Mobile') {
                    $pdf->Cell(30, 10, $key, 1);
                } */ else if ($column == 'Status') {
                    $pdf->Cell(35, 10, $key, 1);
                } else {
                    $pdf->Cell(35, 10, $key, 1);
                }
            }
        }
        $pdf->Output('D');
        //$pdf->Output();
    }
}

//country

//faq
$default_lang 	= $generalobj->get_default_lang();
if ($section == 'faq') {
    if ($keyword != '') {
        if ($option != '') {
            if (strpos($option, 'eStatus') !== false) {
                $ssql .= " AND " . stripslashes($option) . " LIKE '" . stripslashes($keyword) . "'";
            } else {
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
            $ssql .= " AND f.vTitle_".$default_lang." LIKE '%".$keyword."%' OR fc.vTitle LIKE '%".$keyword."%' OR f.iDisplayOrder LIKE '%".$keyword."%' OR f.eStatus LIKE '%".$keyword."%'";
        }
    }    								
    
	$tbl_name 		= 'faqs';
    $sql = "SELECT f.iFaqId AS `Id`,f.vTitle_".$default_lang." as `Title`, fc.vTitle as `Category` ,f.iDisplayOrder as `DisplayOrder` ,f.eStatus  as `Status` FROM ".$tbl_name." f, faq_categories fc WHERE f.iFaqcategoryId = fc.iUniqueId AND fc.vCode = '".$default_lang."' $ssql ORDER BY f.iFaqcategoryId, f.iDisplayOrder"; 
	
    //die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = mysql_query($sql) or die('Query failed!');
        while (false !== ($row = mysql_fetch_assoc($result))) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
    } else {
        $heading = array('Id', 'Title','Category','Order','Status');
        $result = mysql_query($sql);
        while ($row = mysql_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
		//print_r($result);die;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "FAQ");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Id') {
                $pdf->Cell(15, 10, $column_heading, 1);
            }  else if ($column_heading == 'Title') {
                $pdf->Cell(80, 10, $column_heading, 1);
			 }  else if ($column_heading == 'Category') {
                $pdf->Cell(45, 10, $column_heading, 1);
            }  else if ($column_heading == 'Order') {
                $pdf->Cell(28, 10, $column_heading, 1);				
            } else if ($column_heading == 'Status') {
                $pdf->Cell(28, 10, $column_heading, 1);
            } else {
                $pdf->Cell(28, 10, $column_heading, 1); 
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
				if ($column == 'Id') {
                    $pdf->Cell(15, 10, $key, 1);
                }  else if ($column == 'Title') {
                    $pdf->Cell(80, 10, $key, 1);
				}  else if ($column == 'Category') {
                $pdf->Cell(45, 10, $key, 1);
                }  else if ($column == 'Order') {
                $pdf->Cell(28, 10, $key, 1);	
                }  else if ($column == 'Status') {
                    $pdf->Cell(28, 10, $key, 1);
                } else {
                    $pdf->Cell(28, 10, $key, 1);
                }
            }
        }
        $pdf->Output('D');
		//$pdf->Output();
		}
}
//faq

//faq category
if ($section == 'faq_category') {
    if ($keyword != '') {
        if ($option != '') {
            if (strpos($option, 'eStatus') !== false) {
                $ssql .= " AND " . stripslashes($option) . " LIKE '" . stripslashes($keyword) . "'";
            } else {
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
            $ssql .= " AND vTitle LIKE '%".$keyword."%' OR eStatus LIKE '%".$keyword."%'";
        }
    }

     $sql = "SELECT iFaqcategoryId as `Id`, vImage as `Image`,vTitle as `Title`, iDisplayOrder as `Order`, eStatus as `Status` FROM faq_categories where eStatus != 'Deleted' $ssql";
    //die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = mysql_query($sql) or die('Query failed!');
        while (false !== ($row = mysql_fetch_assoc($result))) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
    } else {
        $heading = array('Id', 'Image','Title','Order','Status');
        $result = mysql_query($sql);
        while ($row = mysql_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "FAQ Category");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Id') {
                $pdf->Cell(18, 10, $column_heading, 1);
            } /* else if ($column_heading == 'Mobile') {
                $pdf->Cell(30, 10, $column_heading, 1);
            }*/ else if ($column_heading == 'Status') {
                $pdf->Cell(44, 10, $column_heading, 1);
            } else {
                $pdf->Cell(44, 10, $column_heading, 1); 
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                if ($column == 'Id') {
                    $pdf->Cell(18, 10, $key, 1);
                } /* else if ($column == 'Mobile') {
                    $pdf->Cell(30, 10, $key, 1);
                } */ else if ($column == 'Status') {
                    $pdf->Cell(44, 10, $key, 1);
                } else {
                    $pdf->Cell(44, 10, $key, 1);
                }
            }
        }
        $pdf->Output('D');
       // $pdf->Output();
    }
}
//faq category

//pages
$default_lang 	= $generalobj->get_default_lang();
if ($section == 'page') {
    if ($keyword != '') {
        if ($option != '') {
            if (strpos($option, 'eStatus') !== false) {
                $ssql .= " AND " . stripslashes($option) . " LIKE '" . stripslashes($keyword) . "'";
            } else {
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
            $ssql .= " AND vPageName LIKE '%".$keyword."%' OR vPageTitle_$default_lang LIKE '%".$keyword."%' OR eStatus LIKE '%".$keyword."%'";
        }
    }
    
    $sql = "SELECT iPageId as `Id`, vPageName as `Name`, vPageTitle_$default_lang as `PageTitle` ,eStatus as `Status` FROM pages where ipageId NOT IN('5','20','21','20') AND eStatus != 'Deleted' $ssql";
    //die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = mysql_query($sql) or die('Query failed!');
        while (false !== ($row = mysql_fetch_assoc($result))) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
    } else {
        $heading = array('Id', 'Name','PageTitle','Status');
        $result = mysql_query($sql);
        while ($row = mysql_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Pages");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Id') {
                $pdf->Cell(12, 10, $column_heading, 1);
            }  else if ($column_heading == 'Name') {
                $pdf->Cell(57, 10, $column_heading, 1);
            } else if ($column_heading == 'PageTitle') {
                $pdf->Cell(100, 10, $column_heading, 1);
            } 			
			else if ($column_heading == 'Status') {
                $pdf->Cell(20, 10, $column_heading, 1);
            } else {
                $pdf->Cell(20, 10, $column_heading, 1); 
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                if ($column == 'Id') {
                    $pdf->Cell(12, 10, $key, 1);
                }  else if ($column == 'Name') {
                    $pdf->Cell(57, 10, $key, 1);
                }  else if ($column == 'PageTitle') {
                    $pdf->Cell(100, 10, $key, 1);
                } else if ($column == 'Status') {
                    $pdf->Cell(20, 10, $key, 1);
                } else {
                    $pdf->Cell(20, 10, $key, 1);
                }
            }
        }
        $pdf->Output('D');
        //$pdf->Output();
    }
}
//pages

//languages
$default_lang 	= $generalobj->get_default_lang();
if ($section == 'languages') {
    if ($keyword != '') {
        if ($option != '') {
            if (strpos($option, 'eStatus') !== false) {
                $ssql .= " AND " . stripslashes($option) . " LIKE '" . stripslashes($keyword) . "'";
            } else {
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
            $ssql .= " AND vLabel LIKE '%".$keyword."%' OR vValue LIKE '%".$keyword."%' ";
        }
    }    	
	$tbl_name = 'language_label';
    $sql = "SELECT LanguageLabelId as `Id`,vLabel as `Code`,vValue as `Value in English Language`  FROM ".$tbl_name." WHERE vCode = '".$default_lang."' $ssql"; //die;
    
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = mysql_query($sql) or die('Query failed!');
        while (false !== ($row = mysql_fetch_assoc($result))) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
    } else {
        $heading = array('Id', 'Code','Value in English Language');
        $result = mysql_query($sql);
        while ($row = mysql_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Languages");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Id') {
                $pdf->Cell(22, 10, $column_heading, 1);
            } /* else if ($column_heading == 'Mobile') {
                $pdf->Cell(30, 10, $column_heading, 1);
            }*/ else if ($column_heading == 'Status') {
                $pdf->Cell(88, 10, $column_heading, 1);
            } else {
                $pdf->Cell(88, 10, $column_heading, 1); 
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                if ($column == 'Id') {
                    $pdf->Cell(22, 10, $key, 1);
                } /* else if ($column == 'Mobile') {
                    $pdf->Cell(30, 10, $key, 1);
                } */ else if ($column == 'Status') {
                    $pdf->Cell(88, 10, $key, 1);
                } else {
                    $pdf->Cell(88, 10, $key, 1);
                }
            }
        }
        $pdf->Output('D');        
    }
}

//language label other
if ($section == 'language_label_other') {
    if ($keyword != '') {
        if ($option != '') {
            if (strpos($option, 'eStatus') !== false) {
                $ssql .= " AND " . stripslashes($option) . " LIKE '" . stripslashes($keyword) . "'";
            } else {
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
            $ssql .= " AND vLabel LIKE '%".$keyword."%' OR vValue LIKE '%".$keyword."%' ";
        }
    }   
	
	$tbl_name = 'language_label_other';
    $sql = "SELECT LanguageLabelId as `Id`,vLabel as `Code`,vValue as `Value in English Language`  FROM ".$tbl_name." WHERE vCode = '".$default_lang."' $ssql"; 
    //die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = mysql_query($sql) or die('Query failed!');
        while (false !== ($row = mysql_fetch_assoc($result))) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
    } else {
        $heading = array('Id', 'Code','Value in English Language');
        $result = mysql_query($sql);
        while ($row = mysql_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Admin Language Label");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Id') {
                $pdf->Cell(22, 10, $column_heading, 1);
            } /* else if ($column_heading == 'Mobile') {
                $pdf->Cell(30, 10, $column_heading, 1);
            }*/ else if ($column_heading == 'Status') {
                $pdf->Cell(88, 10, $column_heading, 1);
            } else {
                $pdf->Cell(88, 10, $column_heading, 1); 
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                if ($column == 'Id') {
                    $pdf->Cell(22, 10, $key, 1);
                } /* else if ($column == 'Mobile') {
                    $pdf->Cell(30, 10, $key, 1);
                } */ else if ($column == 'Status') {
                    $pdf->Cell(88, 10, $key, 1);
                } else {
                    $pdf->Cell(88, 10, $key, 1);
                }
            }
        }
       $pdf->Output('D');        
    }
}
//language label other

//vehicle_type
if ($section == 'vehicle_type') {
    if ($keyword != '') {
        if ($option != '') {
            if (strpos($option, 'eStatus') !== false) {
                $ssql .= " AND " . stripslashes($option) . " LIKE '" . stripslashes($keyword) . "'";
            } else {
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
            $ssql .= " AND vVehicleType LIKE '%".$keyword."%'";
        }
    }    								
    
	$tbl_name 		= 'vehicle_type';
	$Vehicle_type_name = 'Ride';
	$ord = " ORDER BY iVehicleTypeId DESC";
      $sql = "SELECT iVehicleTypeId as Id,vVehicleType as Type,fPricePerKM as PricePerKM,fPricePerMin as PricePerMin,iBaseFare as BaseFare,fCommision as Commision,iPersonSize as PersonSize  from  ".$tbl_name." where eType='".$Vehicle_type_name."'  $ssql $ord"; 
	
    //die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = mysql_query($sql) or die('Query failed!');
        while (false !== ($row = mysql_fetch_assoc($result))) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
    } else {
        $heading = array('Id', 'Type','PricePerKM','PricePerMin','BaseFare','Commision','PersonSize');
        $result = mysql_query($sql);
        while ($row = mysql_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
		//print_r($result);die;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Vehicle Type");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Id') {
                $pdf->Cell(15, 10, $column_heading, 1);
            }  else if ($column_heading == 'Type') {
                $pdf->Cell(50, 10, $column_heading, 1);
			 }   else if ($column_heading == 'PricePerKM') {
                $pdf->Cell(25, 10, $column_heading, 1);
            }  else if ($column_heading == 'BaseFare') {
                $pdf->Cell(28, 10, $column_heading, 1);				
            } else if ($column_heading == 'Commision') {
                $pdf->Cell(28, 10, $column_heading, 1);
            } else if ($column_heading == 'PersonSize') {
                $pdf->Cell(26, 10, $column_heading, 1);
            } 			
			else {
                $pdf->Cell(26, 10, $column_heading, 1); 
            } 
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
				if ($column == 'Id') {
                    $pdf->Cell(15, 10, $key, 1);
                }  else if ($column == 'Type') {
                    $pdf->Cell(50, 10, $key, 1);
				}   else if ($column == 'PricePerKM') {
                $pdf->Cell(25, 10, $key, 1);
                } else if ($column == 'BaseFare') {
                $pdf->Cell(28, 10, $key, 1);	
                }   else if ($column == 'Commision') {
                    $pdf->Cell(28, 10, $key, 1);
                } else if ($column == 'PersonSize') {
                    $pdf->Cell(26, 10, $key, 1);
                }
				
				else {
                    $pdf->Cell(26, 10, $key, 1);
                } 
            }
        }
        $pdf->Output('D');
		//$pdf->Output();
		}
}
//vehicle_type


//coupon
if ($section == 'coupon') {
    if ($keyword != '') {
        if ($option != '') {
            if (strpos($option, 'eStatus') !== false) {
                $ssql .= " AND " . stripslashes($option) . " LIKE '" . stripslashes($keyword) . "'";
            } else {
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
            $ssql .= " AND vCouponCode LIKE '%".$keyword."%' OR fDiscount LIKE '%".$keyword."%' OR eValidityType LIKE '%".$keyword."%' OR DATE_FORMAT(dActiveDate,'%d/%m/%Y') LIKE '%".$keyword."%' OR DATE_FORMAT(dExpiryDate,'%d/%m/%Y') LIKE '%".$keyword."%' OR
            iUsageLimit LIKE '%".$keyword."%' OR iUsed LIKE '%".$keyword."%' OR	eStatus LIKE '%".$keyword."%'";
        }
    }    								
    
	
	 $ord = " ORDER BY iVehicleTypeId DESC";
     // $sql = "SELECT *,DATE_FORMAT(dExpiryDate,'%d/%m/%Y') AS dExpiryDate,DATE_FORMAT(dActiveDate,'%d/%m/%Y') AS dActiveDate FROM coupon WHERE eStatus != 'Deleted' $ssql $adm_ssql";
	$sql = "SELECT vCouponCode as `Gift Certificate`,fDiscount as `Discount`,eValidityType as `ValidityType`,DATE_FORMAT(dActiveDate,'%d/%m/%Y') AS `Active Date`,DATE_FORMAT(dExpiryDate,'%d/%m/%Y') AS `ExpiryDate`,iUsageLimit as `Usage Limit`,iUsed as `Used`,eStatus as `Status` FROM coupon WHERE eStatus != 'Deleted' $ssql"; 
	
    //die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = mysql_query($sql) or die('Query failed!');
        while (false !== ($row = mysql_fetch_assoc($result))) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
    } else {
        $heading = array('Gift Certificate','Discount','ValidityType','Active Date','ExpiryDate','Usage Limit','Used','Status');
        $result = mysql_query($sql);
        while ($row = mysql_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
		//print_r($result);die;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Coupon");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Gift Certificate') {
                $pdf->Cell(24, 10, $column_heading, 1);
            }  else if ($column_heading == 'Discount') {
                $pdf->Cell(20, 10, $column_heading, 1);
			 }   else if ($column_heading == 'Validity Type') {
                $pdf->Cell(26, 10, $column_heading, 1);
            }  else if ($column_heading == 'Active Date') {
                $pdf->Cell(28, 10, $column_heading, 1);				
            } else if ($column_heading == 'ExpiryDate') {
                $pdf->Cell(25, 10, $column_heading, 1);
			} else if ($column_heading == 'Usage Limit') {
                $pdf->Cell(24, 10, $column_heading, 1);	
            } else if ($column_heading == 'Used') {
                $pdf->Cell(22, 10, $column_heading, 1);
            } 	
             else if ($column_heading == 'Status') {
                $pdf->Cell(25, 10, $column_heading, 1);
            } 				
			else {
                $pdf->Cell(25, 10, $column_heading, 1); 
            } 
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
				//echo '<pre>'; print_r($column);die;
				if ($column == 'Gift Certificate') {
                    $pdf->Cell(24, 10, $key, 1);
                }  else if ($column == 'Discount') {
					
						$key = $key.' $';
                    $pdf->Cell(20, 10, $key, 1);
				}   else if ($column == 'ValidityType') {					     
				    	if($key=='Defined'){
						$key='Custom';
						 $pdf->Cell(25, 10, $key, 1);	
					}else{
					 $pdf->Cell(25, 10, $key, 1);		
					}
					//$pdf->Cell(26, 10, $key, 1);					
					
                } else if ($column == 'Active Date') {
					/* if($key=='00/00/0000'){
						$key='---';
						 $pdf->Cell(28, 10, $key, 1);	
					}else{
					 $pdf->Cell(28, 10, $key, 1);		
					} */
                    $pdf->Cell(28, 10, $key, 1);
                }   else if ($column == 'ExpiryDate') {
                    $pdf->Cell(25, 10, $key, 1);
                } else if ($column == 'Usage Limit') {
                    $pdf->Cell(24, 10, $key, 1);
                }
				else if ($column == 'Used') {
                    $pdf->Cell(22, 10, $key, 1);
                }
				else if ($column == 'Status') {
                    $pdf->Cell(25, 10, $key, 1);
                }
				
				else {
                    $pdf->Cell(25, 10, $key, 1);
                } 
            }
        }
        $pdf->Output('D');
		//$pdf->Output();
		}
}
//coupon


//driver 
if ($section == 'driver') {
	
	$ord = ' ORDER BY rd.iDriverId DESC';
	if($sortby == 1){
	  if($order == 0)
	  $ord = " ORDER BY rd.vName ASC";
	  else
	  $ord = " ORDER BY rd.vName DESC";
	}
	if($sortby == 2){
	  if($order == 0)
	  $ord = " ORDER BY c.vCompany ASC";
	  else
	  $ord = " ORDER BY c.vCompany DESC";
	}

	if($sortby == 3){
	  if($order == 0)
	  $ord = " ORDER BY rd.vEmail ASC";
	  else
	  $ord = " ORDER BY rd.vEmail DESC";
	}

	if($sortby == 4){
	  if($order == 0)
	  $ord = " ORDER BY rd.tRegistrationDate ASC";
	  else
	  $ord = " ORDER BY rd.tRegistrationDate DESC";
	}

	if($sortby == 5){
	  if($order == 0)
	  $ord = " ORDER BY rd.eStatus ASC";
	  else
	  $ord = " ORDER BY rd.eStatus DESC";
	}
	
    if ($keyword != '') {
        if ($option != '') {
            if (strpos($option, 'eStatus') !== false) {
                $ssql .= " AND " . stripslashes($option) . " LIKE '" . stripslashes($keyword) . "'";
            } else {
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
            $ssql .= " AND (CONCAT(rd.vName,' ',rd.vLastName) LIKE '%".$keyword."%' OR c.vCompany LIKE '%".$keyword."%' OR rd.vEmail LIKE '%".$keyword."%' OR rd.tRegistrationDate LIKE '%".$keyword."%' OR rd.vPhone LIKE '%".$keyword."%' OR rd.eStatus LIKE '%".$keyword."%')";
        }
    }
	
	$dri_ssql = "";
	if (SITE_TYPE == 'Demo') {
		$dri_ssql = " And rd.tRegistrationDate > '" . WEEK_DATE . "'";
	}
	
	$sql = "SELECT CONCAT(rd.vName,' ',rd.vLastName) AS `Driver Name`,c.vCompany as `Company Name`,rd.vEmail as `Email Id`,(select count(dv2.iDriverVehicleId)  from driver_vehicle as dv2 where dv2.iDriverId=rd.iDriverId ) as `Taxi Count`, rd.tRegistrationDate as `signupdate`,rd.vPhone as `Phone`,rd.eStatus as `status` FROM register_driver rd LEFT JOIN company c ON rd.iCompanyId = c.iCompanyId 
	WHERE rd.eStatus != 'Deleted' $ssql $dri_ssql $ord"; 
	
    //die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = $obj->MySQLSelect($sql) or die('Query failed!');
		echo implode("\t", array_keys($result[0])) . "\r\n";
		
		foreach($result as $value){
			foreach($value as $key=>$val) {
			// echo $key.' => '.$val;
				if($key == 'Driver Name'){
					$val = $generalobjAdmin->clearName($val);
				}
				if($key == 'Email Id'){
					$val = $generalobjAdmin->clearEmail($val);
				}
				if($key == 'Phone'){
					$val = $generalobjAdmin->clearPhone($val);
				}
				if($key == 'Company Name'){
					$val = $generalobjAdmin->clearCmpName($val);
				}
				echo $val."\t";
			}
			echo "\r\n";
		}
    } else {
        $heading = array('Driver Name','Company Name','Email Id','Count','signupdate','Phone','Status');
        $result = mysql_query($sql);
		
        while ($row = mysql_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
		//print_r($result);die;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Driver");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Driver Name') {
                $pdf->Cell(29, 10, $column_heading, 1);
            }  else if ($column_heading == 'Company Name') {
                $pdf->Cell(35, 10, $column_heading, 1);
			 }   else if ($column_heading == 'Email Id') {
                $pdf->Cell(46, 10, $column_heading, 1);
            }  else if ($column_heading == 'TaxiCount') {
                $pdf->Cell(3, 10, $column_heading, 1);				
            } else if ($column_heading == 'signupdate') {
                $pdf->Cell(31, 10, $column_heading, 1);
			} else if ($column_heading == 'Phone') {
                $pdf->Cell(21, 10, $column_heading, 1);	
            } else if ($column_heading == 'status') {
                $pdf->Cell(20, 10, $column_heading, 1);
            }              				
			else {
                $pdf->Cell(20, 10, $column_heading, 1); 
            }         }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
				//echo '<pre>'; print_r($result);die;
				$values = $key;
				if($column == 'Driver Name'){
					$values = $generalobjAdmin->clearName($key);
				}
				if($column == 'Email Id'){
					$values = $generalobjAdmin->clearEmail($key);
				}
				if($column == 'Phone'){
					$values = $generalobjAdmin->clearPhone($key);
				}
				if($column == 'Company Name'){
					$values = $generalobjAdmin->clearCmpName($key);
				}
				if ($column == 'Driver Name') {
                    $pdf->Cell(29, 10, $values, 1);
                }  else if ($column == 'Company Name') {							
                    $pdf->Cell(35, 10, $values, 1);
				}   else if ($column == 'Email Id') {					
					$pdf->Cell(46, 10, $values, 1); 
                }  else if ($column == 'TaxiCount') {
                   $pdf->Cell(3, 10, $values, 1);	
                }   else if ($column == 'signupdate') {
                    $pdf->Cell(31, 10, $values, 1);
                } else if ($column == 'Phone') {
                    $pdf->Cell(21, 10, $values, 1);
                } else if ($column == 'status') {
                    $pdf->Cell(20, 10, $values, 1);
                }
								
				else {
                    $pdf->Cell(20, 10, $key, 1);
                } 
            }
        }
        $pdf->Output('D');
		//$pdf->Output();
		}
}
//driver

//vehicles 
if ($section == 'vehicles') {
   
    $ord = ' ORDER BY dv.iDriverVehicleId DESC';
if($sortby == 1){
  if($order == 0)
  $ord = " ORDER BY m.vMake ASC";
  else
  $ord = " ORDER BY m.vMake DESC";
}
if($sortby == 2){
  if($order == 0)
  $ord = " ORDER BY c.vCompany ASC";
  else
  $ord = " ORDER BY c.vCompany DESC";
}
if($sortby == 3){
  if($order == 0)
  $ord = " ORDER BY rd.vName ASC";
  else
  $ord = " ORDER BY rd.vName DESC";
}

if($sortby == 5){
  if($order == 0)
  $ord = " ORDER BY dv.eStatus ASC";
  else
  $ord = " ORDER BY dv.eStatus DESC";
}
//End Sorting

$dri_ssql = "";
if (SITE_TYPE == 'Demo') {
    $dri_ssql = " And rd.tRegistrationDate > '" . WEEK_DATE . "'";
}

// Start Search Parameters
$option = isset($_REQUEST['option'])?stripslashes($_REQUEST['option']):"";
$keyword = isset($_REQUEST['keyword'])?stripslashes($_REQUEST['keyword']):"";
$searchDate = isset($_REQUEST['searchDate'])?$_REQUEST['searchDate']:"";
$ssql = '';
if($keyword != ''){
    if($option != '') {
        if (strpos($option, 'eStatus') !== false) {
            $ssql.= " AND ".stripslashes($option)." LIKE '".stripslashes($keyword)."'";
        }else {
            $ssql.= " AND ".stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
        }
    }else {
        $ssql.= " AND (m.vMake LIKE '%".$keyword."%' OR c.vCompany LIKE '%".$keyword."%' OR CONCAT(rd.vName,' ',rd.vLastName) LIKE '%".$keyword."%')";
    }
}
// End Search Parameters

if($iDriverId != "") {
	$ssql .= " AND dv.iDriverId='".$iDriverId."'";
}

//Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page

if($APP_TYPE == 'UberX'){
	$sql = "SELECT COUNT(dv.iDriverVehicleId) AS Total
	FROM driver_vehicle AS dv, register_driver rd, make m, model md, company c
	WHERE dv.eStatus != 'Deleted' AND dv.iDriverId = rd.iDriverId  AND dv.iCompanyId = c.iCompanyId".$ssql.$dri_ssql;
}else{
	$sql = "SELECT COUNT(dv.iDriverVehicleId) AS Total
		FROM driver_vehicle AS dv, register_driver rd, make m, model md, company c
		WHERE dv.eStatus != 'Deleted'
		AND dv.iDriverId = rd.iDriverId
		AND dv.iCompanyId = c.iCompanyId
		AND dv.iModelId = md.iModelId
		AND dv.iMakeId = m.iMakeId".$ssql.$dri_ssql ;
}

$totalData = $obj->MySQLSelect($sql);
$total_results = $totalData[0]['Total'];
$total_pages = ceil($total_results / $per_page); //total pages we going to have
$show_page = 1;

//-------------if page is setcheck------------------//
if (isset($_GET['page'])) {
    $show_page = $_GET['page'];             //it will telles the current page
    if ($show_page > 0 && $show_page <= $total_pages) {
        $start = ($show_page - 1) * $per_page;
        $end = $start + $per_page;
    } else {
        // error - show first set of results
        $start = 0;
        $end = $per_page;
    }
} else {
    // if page isn't set, show first set of results
    $start = 0;
    $end = $per_page;
}
// display pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 0;
$tpages=$total_pages;
if ($page <= 0)
    $page = 1;
//Pagination End

if($APP_TYPE == 'UberX'){

	$sql = "SELECT dv.iDriverVehicleId,dv.eStatus,CONCAT(rd.vName,' ',rd.vLastName) AS driverName,dv.vLicencePlate, c.vCompany FROM driver_vehicle dv, register_driver rd,company c
		WHERE  dv.eStatus != 'Deleted'  AND dv.iDriverId = rd.iDriverId  AND dv.iCompanyId = c.iCompanyId $ssql $dri_ssql";
}else{
	$sql = "SELECT  CONCAT(m.vMake,' ', md.vTitle) AS TAXIS, c.vCompany AS Company, CONCAT(rd.vName,' ',rd.vLastName) AS Driver ,dv.eStatus as Status
		FROM driver_vehicle dv, register_driver rd, make m, model md, company c
		WHERE dv.eStatus != 'Deleted'
		AND dv.iDriverId = rd.iDriverId
		AND dv.iCompanyId = c.iCompanyId
		AND dv.iModelId = md.iModelId
		AND dv.iMakeId = m.iMakeId $ssql $dri_ssql $ord LIMIT $start, $per_page";
}


    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
           $result = $obj->MySQLSelect($sql) or die('Query failed!');
		echo implode("\t", array_keys($result[0])) . "\r\n";
		
		foreach($result as $value){
			foreach($value as $key=>$val) {
			// echo $key.' => '.$val;
				if($key == 'TAXIS'){
					$val;
				}
				if($key == 'Company'){
					$val = $generalobjAdmin->clearCmpName($val);
				}
				if($key == 'Driver'){
					$val = $generalobjAdmin->clearName($val);
				}
				if($key == 'Status'){
					$val ;
				}
				echo $val."\t";
			}
			echo "\r\n";
		}
    } else {
        $heading = array('TAXIS','Company','Driver','Status');
        $result = mysql_query($sql);
        while ($row = mysql_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
		//print_r($result);die;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Taxis");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'TAXIS') {
                $pdf->Cell(70, 10, $column_heading, 1);
            }  else if ($column_heading == 'Company') {
                $pdf->Cell(45, 10, $column_heading, 1);
	    }   else if ($column_heading == 'Driver') {
                $pdf->Cell(45, 10, $column_heading, 1);
            }   else if ($column_heading == 'Status') {
                $pdf->Cell(45, 10, $column_heading, 1);
			}             				
			else {
                $pdf->Cell(45, 10, $column_heading, 1); 
            } 
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
				//echo '<pre>'; print_r($result);die;
				if ($column == 'TAXIS') {
                    $pdf->Cell(70, 10, $key, 1);
                }  else if ($column == 'Company') {							
                    $pdf->Cell(45, 10, $generalobjAdmin->clearCmpName($key), 1);
		}   else if ($column == 'Driver') {					
		    $pdf->Cell(45, 10, $generalobjAdmin->clearName($key), 1); //}
                }   else if ($column == 'Status') {
                    $pdf->Cell(45, 10, $key, 1);
                } 								
				else {
                    $pdf->Cell(45, 10, $key, 1);
                } 
            }
        }
        $pdf->Output('D');
		//$pdf->Output();
		}
}
//vehicles

//email_template
$default_lang 	= $generalobj->get_default_lang();
if ($section == 'email_template') {
    if ($keyword != '') {
        if ($option != '') {
            if (strpos($option, 'eStatus') !== false) {
                $ssql .= " AND " . stripslashes($option) . " LIKE '" . stripslashes($keyword) . "'";
            } else {
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
            $ssql .= " AND vPageName LIKE '%".$keyword."%' OR vPageTitle_$default_lang LIKE '%".$keyword."%' OR eStatus LIKE '%".$keyword."%')";
        }
    }
	$default_lang 	= $generalobj->get_default_lang();
    $tbl_name 		= 'email_templates';
    $sql = "SELECT vSubject_$default_lang as `Email Templates` FROM ".$tbl_name." WHERE eStatus = 'Active' $ssql $ord $ord ORDER BY vSubject_$default_lang ASC"; 
    //die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = mysql_query($sql) or die('Query failed!');
        while (false !== ($row = mysql_fetch_assoc($result))) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
    } else {
        $heading = array('Email Templates');
        $result = mysql_query($sql);
        while ($row = mysql_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Pages");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Email Templates') {
                $pdf->Cell(82, 10, $column_heading, 1);
            } /* else if ($column_heading == 'Mobile') {
                $pdf->Cell(30, 10, $column_heading, 1);
            }*/ else if ($column_heading == '') {
                $pdf->Cell(8, 10, $column_heading, 1);
            } else {
                $pdf->Cell(8, 10, $column_heading, 1); 
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                if ($column == 'Email Templates') {
                    $pdf->Cell(82, 10, $key, 1);
                } /* else if ($column == 'Mobile') {
                    $pdf->Cell(30, 10, $key, 1);
                } */ else if ($column == '') {
                    $pdf->Cell(8, 10, $key, 1);
                } else {
                    $pdf->Cell(8, 10, $key, 1);
                }
            }
        }
        $pdf->Output('D');
    }
}
//email_template

//Restricted Area
if ($section == 'restrict_area') {
	
	if($sortby == 1){
	  if($order == 0)
	  $ord = " ORDER BY c.vCountry ASC";
	  else
	  $ord = " ORDER BY c.vCountry DESC";
	}

	if($sortby == 2){
	  if($order == 0)
	  $ord = " ORDER BY st.vState ASC";
	  else
	  $ord = " ORDER BY st.vState DESC";
	}

	if($sortby == 3){
	  if($order == 0)
	  $ord = " ORDER BY ct.vCity ASC";
	  else
	  $ord = " ORDER BY ct.vCity DESC";
	}

	if($sortby == 4){
	  if($order == 0)
	  $ord = " ORDER BY ra.vAddress ASC";
	  else
	  $ord = " ORDER BY ra.vAddress DESC";
	}

	if($sortby == 5){
	  if($order == 0)
	  $ord = " ORDER BY ra.eRestrictType ASC";
	  else
	  $ord = " ORDER BY ra.eRestrictType DESC";
	}

	if($sortby == 6){
	  if($order == 0)
	  $ord = " ORDER BY ra.eStatus ASC";
	  else
	  $ord = " ORDER BY ra.eStatus DESC";
	}

	if($sortby == 7){
	  if($order == 0)
	  $ord = " ORDER BY ra.eType ASC";
	  else
	  $ord = " ORDER BY ra.eType DESC";
	}
	//End Sorting
	
    if($keyword != ''){
		if($option != '') {
			if (strpos($option, 'ra.eStatus') !== false) {
				$ssql.= " AND ".stripslashes($option)." LIKE '".stripslashes($keyword)."'";
			}else {
				$ssql.= " AND ".stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
			}
		}else {
			$ssql.= " AND (c.vCountry LIKE '%".$keyword."%' OR st.vState LIKE '%".$keyword."%' OR ct.vCity LIKE '%".$keyword."%' OR ra.vAddress LIKE '%".$keyword."%' OR ra.eStatus LIKE '%".$keyword."%')";
		}
	}
    $sql = "SELECT c.vCountry AS Country, st.vState AS State, ct.vCity AS City, ra.vAddress AS Address, ra.eRestrictType AS Area, ra.eType AS Type, ra.eStatus AS Status FROM restricted_negative_area AS ra
		LEFT JOIN country AS c ON c.iCountryId=ra.iCountryId
		LEFT JOIN state AS st ON st.iStateId=ra.iStateId
		LEFT JOIN city AS ct ON ct.iCityId=ra.iCityId
		WHERE 1=1 $ssql $ord";
    //die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = mysql_query($sql) or die('Query failed!');
        while (false !== ($row = mysql_fetch_assoc($result))) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
    } else {
        $heading = array('Country','State','City','Address','Area','Type','Status');
        $result = mysql_query($sql);
        while ($row = mysql_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Country");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Area') {
                $pdf->Cell(18, 10, $column_heading, 1);
            }else if ($column_heading == 'Address') {
                $pdf->Cell(35, 10, $column_heading, 1);
            } else {
                $pdf->Cell(25, 10, $column_heading, 1); 
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                if ($column == 'Area') {
                    $pdf->Cell(18, 10, $key, 1);
                }else if ($column == 'Address') {
                    $pdf->Cell(35, 10, $key, 1);
                } else {
                    $pdf->Cell(25, 10, $key, 1);
                }
            }
        }
        $pdf->Output('D');
        //$pdf->Output();
    }
}

?>