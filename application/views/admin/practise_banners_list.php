<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>GSL | Practice Banners</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta content="" name="description" />
	<meta content="" name="author" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	
	<!-- App favicon -->
	<link rel="shortcut icon" href="<?php echo base_url() ?>assets/admin/images/favicon.ico">

	<!-- plugins -->
	
	<link href="<?php echo base_url() ?>assets/admin/libs/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo base_url() ?>assets/admin/libs/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo base_url() ?>assets/admin/libs/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo base_url() ?>assets/admin/libs/datatables/select.bootstrap4.min.css" rel="stylesheet" type="text/css" /> 

	<!-- App css -->
	<link href="<?php echo base_url() ?>assets/admin/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo base_url() ?>assets/admin/css/icons.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo base_url() ?>assets/admin/css/app.min.css" rel="stylesheet" type="text/css" />
	<style>
		.form-control{
			border: 1px solid #b3b8d6 !important;
		}
		
		td img{
			max-width:200px !important;
			border: 1px solid #b3b8d6 !important;
			padding:3px !important;
			border-radius:5px !important;
		}
	</style>
</head>

    <body>
        <!-- Begin page -->
        <div id="wrapper">

            <!-- Topbar Start -->
			 <?php include ('topbar.php'); ?>
            <!-- end Topbar -->

            <!-- ========== Left Sidebar Start ========== -->
			 <?php include ('left_sidebar.php'); ?>
           <!-- Left Sidebar End -->

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">
                  <!-- Start Content-->
                    <div class="container-fluid">
                        <div class="row page-title">
                            <div class="col-md-12">
                                <nav aria-label="breadcrumb" class="float-right mt-1">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                                        <li class="breadcrumb-item"><a href="#">Games Master </a></li>
                                        
                                        <li class="breadcrumb-item active" aria-current="page">Practice Zone Banners</li>
                                    </ol>
                                </nav>
                                <h4 class="mb-1 mt-0">Practice Zone Banners</h4>
                            </div>
                        </div>
						
						<div class="row">
                           <div class="col-lg-12">
								<?php if(@$this->session->flashdata('error')) { ?>
									<div class="alert alert-danger alert-dismissible fade show" role="alert">
										<?php echo $this->session->flashdata('error'); ?>
										<button type="button" class="close" data-dismiss="alert" aria-label="Close">
											<span aria-hidden="true">×</span>
										</button>
									</div>
								<?php } ?>
								<?php if(@$this->session->flashdata('success')) { ?>
									<div class="alert alert-success alert-dismissible fade show" role="alert">
										<?php echo $this->session->flashdata('success'); ?>
										<button type="button" class="close" data-dismiss="alert" aria-label="Close">
											<span aria-hidden="true">×</span>
										</button>
									</div>
								<?php } ?>
							</div>
						</div> 
						
                    <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                       <div class="row">
											<div class="col-8">
												<h4 class="header-title mt-0 mb-1">All Practice Zone Banners List</h4>
											</div>
											<div class="col-4 text-right">
												<a href="javascript(0);" data-toggle="modal" data-target="#addBanner" class="btn btn-primary btn-rounded" >Upload Banners</a>
											</div>  
										</div>
                                        
										<p class="sub-header">
                                            &nbsp; <br>
                                        </p>
										
										

                                        <table id="myDataTable" class="table dt-responsive nowrap">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Game Name</th>
													<th>Status?</th>
													<th>Banner Link</th>
                                                    <th>Last Updated</th>
                                                    <th class="text-center">Active/Inactive</th>
                                                    <th class="text-center">Delete</th>
                                                </tr>
                                            </thead>
                                        
                                        
                                            <tbody>
											<?php if(is_array($list) && count($list)>0){ $i=1;  ?>
											<?php foreach($list as $row){  ?>
                                                <tr>
                                                    <td><?php echo $i; $i++; ?></td>
                                                   <td><?php echo $row['banner_game_name'] ?></td>
                                                    <td>
														<?php if($row['banner_status']== '1' ){  ?>
															<span class="badge badge-soft-primary">Active</span>
														<?php } else if($row['banner_status']== '2' ){  ?>
															<span class="badge badge-soft-danger">Inactive</span>
														
														<?php }  ?>
													</td>
													<td><a target="_blank" href="<?php echo base_url().'uploads/practise-banners/'.$row['banner_image_path'] ?>" class="img-responsive">View Banner</a></td>
                                                  
													<td><?php echo date('d/M/Y', $row['banner_added_on']); ?></td>
													
													<td class="text-center">
														<div class="custom-control custom-switch mb-2">
															<input type="checkbox" class="custom-control-input active_banner" <?php if( $row['banner_status'] == '1'){ echo "checked"; } ?>  id="checkbox_<?php echo $row['banner_id'] ?>">
															<label class="custom-control-label" for="checkbox_<?php echo $row['banner_id'] ?>">&nbsp; </label>
														</div>
													</td>
													
													<td class="text-center">
														<a href="<?php echo site_url('admin/deletePracticeBanner/'.base64_encode($row['banner_id'])); ?>" class="text-danger" onClick="return confirm('Are you sure to remove this banner from the list?');"><i data-feather="trash-2" width="24" height="24"></i></a>
													</td>
													
                                                </tr>
                                            <?php } ?>   
                                            <?php } ?>   
                                            </tbody>
                                        </table>

                                    </div> <!-- end card body-->
                                </div> <!-- end card -->
                            </div><!-- end col-->
                        </div>
                        <!-- end row-->

					</div> <!-- container-fluid -->
				</div> <!-- content -->

                

                <!-- Footer Start -->
                <?php include ('footer.php'); ?>
                <!-- end Footer -->

            </div>

            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->


        </div>
        <!-- END wrapper -->
	                     

      		
	<div id="addBanner" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="myModalLabel">Add New Banner</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form role="form" class="parsley-examples" action="<?php echo site_url('admin/processPracticeBanner') ?>" method="post" enctype="multipart/form-data">
                 
				<div class="modal-body">
				
				
					<div class="form-group row">
						<label for="category_status" class="col-md-4 col-form-label">Choose Game </label>
						<div class="col-md-8">
							<select class="form-control" id="banner_game_id"  name="banner_game_id"  required >
								<option value="">Choose from list</option>
								<?php foreach($games as $game){ ?>
									<option value="<?php echo $game['gid']; ?>"> <?php echo $game['Name']; ?> </option> 
								<?php } ?>
							</select>
						</div>
					</div>
					
					<div class="row"><div class="col-md-12"><br> </div></div>
					
					<div class="form-group row"><br>
						<label for="category_status" class="col-md-4 col-form-label">Choose Image </label>
						<div class="col-md-8">
							<input type="file" class="" id="banner_image_path"  name="banner_image_path"  required />
						</div>
					</div>
									
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Save changes</button>
				</div>
				</form>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	
	
        <!-- Right bar overlay-->
        <div class="rightbar-overlay"></div>
		
		<!-- Vendor js -->
        <script src="<?php echo base_url() ?>assets/admin/js/vendor.min.js"></script>

		<!-- datatable js -->
        <script src="<?php echo base_url() ?>assets/admin/libs/datatables/jquery.dataTables.min.js"></script>
        <script src="<?php echo base_url() ?>assets/admin/libs/datatables/dataTables.bootstrap4.min.js"></script>
        <script src="<?php echo base_url() ?>assets/admin/libs/datatables/dataTables.responsive.min.js"></script>
        <script src="<?php echo base_url() ?>assets/admin/libs/datatables/responsive.bootstrap4.min.js"></script>
        
        <script src="<?php echo base_url() ?>assets/admin/libs/datatables/dataTables.buttons.min.js"></script>
        <script src="<?php echo base_url() ?>assets/admin/libs/datatables/buttons.bootstrap4.min.js"></script>
        <script src="<?php echo base_url() ?>assets/admin/libs/datatables/buttons.html5.min.js"></script>
        <script src="<?php echo base_url() ?>assets/admin/libs/datatables/buttons.flash.min.js"></script>
        <script src="<?php echo base_url() ?>assets/admin/libs/datatables/buttons.print.min.js"></script>

        <script src="<?php echo base_url() ?>assets/admin/libs/datatables/dataTables.keyTable.min.js"></script>
        <script src="<?php echo base_url() ?>assets/admin/libs/datatables/dataTables.select.min.js"></script>

        <!-- Datatables init -->
        <script src="<?php echo base_url() ?>assets/admin/js/pages/datatables.init.js"></script>
		
		<!-- Plugin js-->
        <script src="<?php echo base_url() ?>assets/admin/libs/parsleyjs/parsley.min.js"></script>

        <!-- Validation init js-->
        <script src="<?php echo base_url() ?>assets/admin/js/pages/form-validation.init.js"></script>


        <!-- App js -->
        <script src="<?php echo base_url() ?>assets/admin/js/app.min.js"></script>
		
		
		<script>
		$(document).ready(function(){
			$(".active_banner").click(function(){
				var id = $(this).attr('id');
				id = id.split("_").pop();
				id = parseInt(id);
				var dataStr = "id="+id;
				$.ajax({
					url: "<?php echo site_url('admin/updatePractiseBannerStatusAjax') ?>",
					type: "POST",
					data: dataStr,
					success: function(data){
						location.reload();
					}
				});
			});
		});
		</script>

		<script>
		$('#myDataTable').dataTable( {
			"pageLength": 50
		});
		</script>
		
    </body>
</html>