<?php
include_once('../common.php');
if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();

require_once(TPATH_CLASS . "/Imagecrop.class.php");
$thumb = new thumbnail();

 $rowid = isset($_REQUEST['rowid']) ? $_REQUEST['rowid'] : '';
  $id = explode('-',$rowid);
  //print_r($id);
		
 $sql = "select  dm.`doc_masterid`, dm.`doc_usertype`, dm.`doc_name`, dm.`ex_status`, dl.`doc_id`, dl.`doc_masterid`, dl.`doc_usertype`, dl.`doc_userid`, dl.`ex_date`, dl.`doc_file`,rd.`iDriverId`
 from document_master as dm 
 left join document_list  as dl on dl.doc_masterid= dm.doc_masterid
 left join  register_driver as rd on  dl.doc_userid= rd.iDriverId AND iDriverId='".$id[1]."' 
 where dm.doc_masterid='".$id[0]."' " ;

$db_user = $obj->MySQLSelect($sql);
$vName = $db_user[0]['doc_file'];
?>

<link rel="stylesheet" href="../assets/plugins/datepicker/css/datepicker.css" />
<script src="../assets/plugins/daterangepicker/daterangepicker.js"></script>
<div class="upload-content">
    <h4><?php echo $db_user[0]['doc_name']; ?></h4>
    <form class="form-horizontal" id="frm6" method="post" enctype="multipart/form-data" action="driver_document_action.php?id=<?php echo $id[1] ; ?>" name="frm6">
        <input type="hidden" name="action" value ="document"/>
        <input type="hidden" name="doc_type" value="<?php echo $id[0]; ?>" />
        <input type="hidden" name="doc_path" value =" <?php echo $tconfig["tsite_upload_driver_doc_path"]; ?>"/>
        
        <div class="form-group">
            <div class="col-lg-12">
                <div class="fileupload fileupload-new" data-provides="fileupload">
                    <div class="fileupload-preview thumbnail" style="width: 200px; height: 150px; ">
                        <?php if ($db_user[0]['doc_file'] == '') { 
                            echo $vName;
                            
                        } else { ?>
                            <?php
                            $file_ext = $generalobj->file_ext($db_user[0]['doc_file']);
                            if ($file_ext == 'is_image') {
                                ?>
                                <img src = "<?= $tconfig["tsite_upload_driver_doc"] . '/' . $id[1] . '/' . $db_user[0]['doc_file'] ?>" style="width:200px;" alt ="Licence not found"/>
                            <?php } else { ?>
                                <a href="<?= $tconfig["tsite_upload_driver_doc"] . '/' . $id[1] . '/' . $db_user[0]['doc_file'] ?>" target="_blank"><?php echo $db_user[0]['doc_name']; ?></a>
                            <?php } ?>
                        <?php } ?>
                    </div>
                    <div>
                        <span class="btn btn-file btn-success"><span class="fileupload-new">Upload <?php echo $db_user[0]['doc_name']; ?> Image </span>
                            <span class="fileupload-exists">Change</span>
                            <input type="file" name="driver_doc" /></span>
                        <a href="#" class="btn btn-danger fileupload-exists" data-dismiss="fileupload">Remove</a>
                    </div>
                </div>
            </div>
        </div>
        <?php if($db_user[0]['ex_status']=='yes') { ?>
        EXP. DATE<br>
        <div class="col-lg-13">
            <div class="input-group input-append date" id="dp3" data-date="" data-date-format="yyyy-mm-dd">
                <input class="form-control" type="text" name="dLicenceExp" value="<?php echo isset($db_user[0]['ex_date']) ? $db_user[0]['ex_date'] : ' '; ?>" readonly="" />
                <span class="input-group-addon add-on"><i class="icon-calendar"></i></span>
            </div>
        </div>
        <?php }  ?>
        <input type="submit" class="save" name="save" value="Save">
        <input type="button" class="cancel" data-dismiss="modal" name="cancel" value="Cancel">
    </form>
</div>
<script>
    $(function () {

        // var nowTemp = new Date();
        // var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

        $('#dp3').datepicker({
            // onRender: function (date) {
                // return date.valueOf() < now.valueOf() ? 'disabled' : '';
           // }
        });
        //formInit();
    });
</script>