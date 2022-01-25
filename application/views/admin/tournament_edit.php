<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="utf-8" />
        <title>GSL | New Tournament</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="" name="description" />
        <meta content="" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        
        <!-- App favicon -->
        <link rel="shortcut icon" href="<?php echo base_url() ?>assets/admin/images/favicon.ico">

        <!-- App css -->
        <link href="<?php echo base_url() ?>assets/admin/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url() ?>assets/admin/css/icons.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url() ?>assets/admin/css/app.min.css" rel="stylesheet" type="text/css" />
		<style>
			.form-control{
				border : 1px solid #b3b8d6 !important;
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
                                        <li class="breadcrumb-item"><a href="#">Tournaments</a></li>
                                        <li class="breadcrumb-item"><a href="#">Manage Tournaments</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Update Tournament</li>
                                    </ol>
                                </nav>
                                <h4 class="mb-1 mt-0">Update Tournament</h4>
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
						
						<form role="form" class="parsley-examples" action="<?php echo site_url('admin/processEditTournament/'.$tournament_id) ?>" method="post" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header bg-soft-primary ">
									 <h4 class="header-title mt-0 mb-1"> Basic Information</h4>
									</div>
                                    <div class="card-body">
                                       
										<div class="form-group row">
											<div class="col-md-6">
												<label for="tournament_name" class="col-md-12 col-form-label">Tournament Name <span class="text-danger">*</span></label>
												<div class="col-md-12">
													<input type="text" class="form-control" name="tournament_name" id="tournament_name" value="<?php echo stripslashes(urldecode($tournamentInfo['tournament_name'])); ?>" required />
												</div>
											</div>
											
											<div class="col-md-6">
												<label for="tournament_type" class="col-md-12 col-form-label">Type <span class="text-danger">*</span></label>
												<div class="col-md-12">
													<select class="form-control" name="tournament_type" id="tournament_type" required >
															<option value="">Choose option</option>
															<option value="1" <?php  if($tournamentInfo['tournament_type'] == '1'){ echo "selected"; } ?> >Free</option>
															<option value="2" <?php  if($tournamentInfo['tournament_type'] == '2'){ echo "selected"; } ?>>Paid</option>
															<option value="3" <?php  if($tournamentInfo['tournament_type'] == '3'){ echo "selected"; } ?>>Contest</option>
													</select>
												</div>
											</div>
										</div>
										
										<div class="form-group row">
											<div class="col-md-6">
												<label for="tournament_category_id" class="col-md-12 col-form-label">Category <span class="text-danger">*</span></label>
                                                <div class="col-md-12">
                                                    <select class="form-control" name="tournament_category_id" id="tournament_category_id" required >
															<option value="">Choose Category</option> 
															<?php foreach($categories as $category){ ?>
																<option value="<?php echo $category['category_id']; ?>" <?php  if($tournamentInfo['tournament_category_id'] == $category['category_id']){ echo "selected"; } ?> > <?php echo $category['category_name']; ?> </option> 
															<?php } ?>
													</select>
                                                </div>
											</div>
											
											<div class="col-md-6">
												<label for="tournament_game_id" class="col-md-12 col-form-label">Game <span class="text-danger">*</span></label>
                                                <div class="col-md-12">
                                                    <select class="form-control" name="tournament_game_id" id="tournament_game_id" required >
															<option value="">Choose Game</option> 
															<?php foreach($games as $game){ ?>
																<option value="<?php echo $game['gid']; ?>" <?php  if($tournamentInfo['tournament_game_id'] == $game['gid']){ echo "selected"; } ?> > <?php echo $game['Name']; ?> </option> 
															<?php } ?>
													</select>
                                                </div>
											</div>
										</div>
										
										<div class="form-group row">
											<div class="col-md-6">
												<label for="tournament_start_date" class="col-md-12 col-form-label">Start Time <span class="text-danger">*</span></label>
												<div class="row">
													<div class="col-md-6">
														<input type="date" class="form-control" name="tournament_start_date" id="tournament_start_date"  value="<?php echo date('Y-m-d', strtotime($tournamentInfo['tournament_start_date'])); ?>" required />
													</div>
													<div class="col-md-6">
														<input type="time" class="form-control" name="tournament_start_time" id="tournament_start_time"  value="<?php echo date('H:i', strtotime($tournamentInfo['tournament_start_time'])); ?>" required />
													</div>
												</div>
											</div>
											
											<div class="col-md-6">
												<label for="tournament_end_date" class="col-md-12 col-form-label">End Time <span class="text-danger">*</span></label>
												<div class="row">
													<div class="col-md-6">
														<input type="date" class="form-control" name="tournament_end_date" id="tournament_end_date"  value="<?php echo date('Y-m-d', strtotime($tournamentInfo['tournament_end_date'])); ?>"   required />
													</div>
													<div class="col-md-6">
														<input type="time" class="form-control" name="tournament_end_time" id="tournament_end_time" value="<?php echo date('H:i', strtotime($tournamentInfo['tournament_end_time'])); ?>" required />
													</div>
												</div>
											</div>
										</div>
											
										<div class="form-group row">
											
											<div class="col-md-6">
												<label for="tournament_section" class="col-md-12 col-form-label">Tournament Section Zone <span class="text-danger">*</span></label>
												<div class="col-md-12">
													<select class="form-control" name="tournament_section" id="tournament_section" required >
														<option value="">Choose option</option>
														<option value="1" <?php  if($tournamentInfo['tournament_section'] == 1){ echo "selected"; } ?>>Free Tournament</option>
														<option value="2" <?php  if($tournamentInfo['tournament_section'] == 2){ echo "selected"; } ?>>Vip Tournament</option>
														<option value="3" <?php  if($tournamentInfo['tournament_section'] == 3){ echo "selected"; } ?>>Pay & Play Tournament</option>
													</select>
												</div>
											</div>
										
											<div class="col-md-6">
												<label for="tournament_desc" class="col-md-12 col-form-label">Description <span class="text-danger">*</span></label>
												<div class="col-md-12">
													<textarea class="form-control" name="tournament_desc" id="tournament_desc"  required ><?php echo stripslashes(urldecode(nl2br($tournamentInfo['tournament_desc']))); ?></textarea>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<?php $count=0; foreach($rewardInfo as $row)
						{
						?>
						<div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header bg-soft-primary ">
									 <h4 class="header-title mt-0 mb-1">Fee & Rewards Distribution <?php echo "For ".ucfirst($row['c_name']); ?> </h4>
									</div>
                                    <div class="card-body">
									<input type="hidden" name="id[]" value="<?php echo $row['fee_id']; ?>">
									<input type="hidden" name="country_id[]" value="<?php echo $row['fee_country_id']; ?>">
                                       	<input type="hidden" name="country[]"  value="<?php echo $row['fee_country_name']; ?>">
										<div class="form-group row">
											<div class="col-md-4">
												<label for="tournament_fee_<?php echo $row['fee_id']; ?>" class="col-md-12 col-form-label">Fee (in Coins)<span class="text-danger">*</span></label>
												<div class="col-md-12">
													<input type="text" class="form-control" name="tournament_fee[]" id="tournament_fee_<?php echo $row['fee_id']; ?>" value="<?php echo $row['fee_tournament_fee']; ?>" required />
												</div>
											</div>
											
											<div class="col-md-4">
												<label for="tournament_reward_type_<?php echo $row['fee_id']; ?>" class="col-md-12 col-form-label">Reward Type <span class="text-danger">*</span></label>
												<div class="col-md-12">
													<select class="form-control" name="tournament_reward_type[]" onclick="changeType(<?php echo $row['fee_id']; ?>)"  id="tournament_reward_type_<?php echo $row['fee_id']; ?>" required >
															<option value="">Choose option</option>
															<option value="1" <?php  if($row['fee_tournament_rewards'] == '1'){ echo "selected"; } ?>>Coins</option>
															<option value="2" <?php  if($row['fee_tournament_rewards'] == '2'){ echo "selected"; } ?>>Data Pack</option>
															<option value="3" <?php  if($row['fee_tournament_rewards'] == '3'){ echo "selected"; } ?>>Talk Time</option>
													</select>
												</div>
											</div>
										</div>
											
										<div class="form-group row">
											<div class="col-md-4">
												<label for="tournament_prize_1_<?php echo $row['fee_id']; ?>" class="col-md-12 col-form-label">1st Rank Prize (in <span class='reward_type<?php echo $row['fee_id'] ?>'> <?php if($row['fee_tournament_rewards'] == '1'){ echo "Coins"; } if($row['fee_tournament_rewards'] == '2'){ echo "Data Pack"; } if($row['fee_tournament_rewards'] == '3'){ echo "Talk Time"; } ?> </span>)<span class="text-danger">*</span></label>
												<div class="col-md-12">
													<input type="number" min="0" class="form-control" name="tournament_prize_1[]" id="tournament_prize_1_<?php echo $row['fee_id']; ?>" value="<?php echo $row['fee_tournament_prize_1']; ?>" required />
												</div>
											</div>
											
											<div class="col-md-4">
												<label for="tournament_prize_2_<?php echo $row['fee_id']; ?>" class="col-md-12 col-form-label">2nd Rank Prize (in <span class='reward_type<?php echo $row['fee_id'] ?>'><?php if($row['fee_tournament_rewards'] == '1'){ echo "Coins"; } if($row['fee_tournament_rewards'] == '2'){ echo "Data Pack"; } if($row['fee_tournament_rewards'] == '3'){ echo "Talk Time"; } ?></span>)<span class="text-danger">*</span></label>
												<div class="col-md-12">
													<input type="number" min="0" class="form-control" name="tournament_prize_2[]" id="tournament_prize_2_<?php echo $row['fee_id']; ?>" value="<?php echo $row['fee_tournament_prize_2']; ?>" required />
												</div>
											</div>
											
											<div class="col-md-4">
												<label for="tournament_prize_3_<?php echo $row['fee_id']; ?>" class="col-md-12 col-form-label">3rd Rank Prize (in <span class='reward_type<?php echo $row['fee_id'] ?>'><?php if($row['fee_tournament_rewards'] == '1'){ echo "Coins"; } if($row['fee_tournament_rewards'] == '2'){ echo "Data Pack"; } if($row['fee_tournament_rewards'] == '3'){ echo "Talk Time"; } ?></span>)<span class="text-danger">*</span></label>
												<div class="col-md-12">
													<input type="number" min="0" class="form-control" name="tournament_prize_3[]" id="tournament_prize_3_<?php echo $row['fee_id']; ?>" value="<?php echo $row['fee_tournament_prize_3']; ?>" required />
												</div>
											</div>
										</div>
											
										<div class="form-group row">
											<div class="col-md-4">
												<label for="tournament_prize_4_<?php echo $row['fee_id']; ?>" class="col-md-12 col-form-label">4th - 5th Rank Prize (in <span class='reward_type<?php echo $row['fee_id'] ?>'><?php if($row['fee_tournament_rewards'] == '1'){ echo "Coins"; } if($row['fee_tournament_rewards'] == '2'){ echo "Data Pack"; } if($row['fee_tournament_rewards'] == '3'){ echo "Talk Time"; } ?></span>)</label>
												<div class="col-md-12">
													<input type="number" min="0" class="form-control" name="tournament_prize_4[]" id="tournament_prize_4_<?php echo $row['fee_id']; ?>" value="<?php echo $row['fee_tournament_prize_4']; ?>"  />
												</div>
											</div>
											
											<div class="col-md-4">
												<label for="tournament_prize_5_<?php echo $row['fee_id']; ?>" class="col-md-12 col-form-label">6th to 10th Rank Prize (in <span class='reward_type<?php echo $row['fee_id'] ?>'><?php if($row['fee_tournament_rewards'] == '1'){ echo "Coins"; } if($row['fee_tournament_rewards'] == '2'){ echo "Data Pack"; } if($row['fee_tournament_rewards'] == '3'){ echo "Talk Time"; } ?></span>)</label>
												<div class="col-md-12">
													<input type="number" min="0" class="form-control" name="tournament_prize_5[]" id="tournament_prize_5_<?php echo $row['fee_id']; ?>" value="<?php echo $row['fee_tournament_prize_5']; ?>"  />
												</div>
											</div>
											
											<div class="col-md-4">
												<label for="tournament_prize_6_<?php echo $row['fee_id']; ?>" class="col-md-12 col-form-label">11th to 25th Rank Prize (in <span class='reward_type<?php echo $row['fee_id'] ?>'><?php if($row['fee_tournament_rewards'] == '1'){ echo "Coins"; } if($row['fee_tournament_rewards'] == '2'){ echo "Data Pack"; } if($row['fee_tournament_rewards'] == '3'){ echo "Talk Time"; } ?></span>)</label>
												<div class="col-md-12">
													<input type="number" min="0" class="form-control" name="tournament_prize_6[]" id="tournament_prize_6_<?php echo $row['fee_id']; ?>" value="<?php echo $row['fee_tournament_prize_6']; ?>"  />
												</div>
											</div>
										</div>
											
										<div class="form-group row">
											<div class="col-md-4">
												<label for="tournament_prize_7_<?php echo $row['fee_id']; ?>" class="col-md-12 col-form-label">26th - 50th Postion Prize (in <span class='reward_type<?php echo $row['fee_id'] ?>'><?php if($row['fee_tournament_rewards'] == '1'){ echo "Coins"; } if($row['fee_tournament_rewards'] == '2'){ echo "Data Pack"; } if($row['fee_tournament_rewards'] == '3'){ echo "Talk Time"; } ?></span>)</label>
												<div class="col-md-12">
													<input type="number" min="0" class="form-control" name="tournament_prize_7[]" id="tournament_prize_7_<?php echo $row['fee_id']; ?>" value="<?php echo $row['fee_tournament_prize_7']; ?>"  />
												</div>
											</div>
											
											<div class="col-md-4">
												<label for="tournament_prize_8_<?php echo $row['fee_id']; ?>" class="col-md-12 col-form-label">51st to 100th Postion Prize (in <span class='reward_type<?php echo $row['fee_id'] ?>'><?php if($row['fee_tournament_rewards'] == '1'){ echo "Coins"; } if($row['fee_tournament_rewards'] == '2'){ echo "Data Pack"; } if($row['fee_tournament_rewards'] == '3'){ echo "Talk Time"; } ?></span>)</label>
												<div class="col-md-12">
													<input type="number" min="0" class="form-control" name="tournament_prize_8[]" id="tournament_prize_8_<?php echo $row['fee_id']; ?>"  value="<?php echo $row['fee_tournament_prize_8']; ?>" />
												</div>
											</div>
											<div class="col-md-4">
												<label for="tournament_prize_9_<?php echo $row['fee_id']; ?>" class="col-md-12 col-form-label">51st to 100th Postion Prize (in <span class='reward_type<?php echo $row['fee_id'] ?>'><?php if($row['fee_tournament_rewards'] == '1'){ echo "Coins"; } if($row['fee_tournament_rewards'] == '2'){ echo "Data Pack"; } if($row['fee_tournament_rewards'] == '3'){ echo "Talk Time"; } ?></span>)</label>
												<div class="col-md-12">
													<input type="number" min="0" class="form-control" name="tournament_prize_9[]" id="tournament_prize_9_<?php echo $row['fee_id']; ?>"  value="<?php echo $row['fee_tournament_prize_9']; ?>" />
												</div>
											</div>
										</div>
										
                                       
                                    </div>
                                </div> <!-- end card -->

                            </div> <!-- end col -->
                        </div>
                        <!-- end row -->
						<?php $count++; } ?>
						<?php if($count==0){ ?>
							<div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header bg-soft-primary ">
									 <h4 class="header-title mt-0 mb-1">Fee & Rewards Distribution </h4>
									</div>
                                    <div class="card-body">
										<div class="form-group row">
											<div class="col-md-4">
												<label for="tournament_fee" class="col-md-12 col-form-label">Fee (in Coins)<span class="text-danger">*</span></label>
												<div class="col-md-12">
													<input type="text" class="form-control" name="tournament_fee" id="tournament_fee" value="<?php echo $tournamentInfo['tournament_fee']; ?>" required />
												</div>
											</div>
											
											<div class="col-md-4">
												<label for="tournament_reward_type" class="col-md-12 col-form-label">Reward Type <span class="text-danger">*</span></label>
												<div class="col-md-12">
													<select class="form-control" name="tournament_reward_type"  id="tournament_reward_type" required >
															<option value="">Choose option</option>
															<option value="1" <?php  if($tournamentInfo['tournament_reward_type'] == '1'){ echo "selected"; } ?>>Coins</option>
															<option value="2" <?php  if($tournamentInfo['tournament_reward_type'] == '2'){ echo "selected"; } ?>>Data Pack</option>
															<option value="3" <?php  if($tournamentInfo['tournament_reward_type'] == '3'){ echo "selected"; } ?>>Talk Time</option>
													</select>
												</div>
											</div>
										</div>
											
										<div class="form-group row">
											<div class="col-md-4">
												<label for="tournament_prize_1" class="col-md-12 col-form-label">1st Rank Prize (in <span class='reward_type'>Coins</span>)<span class="text-danger">*</span></label>
												<div class="col-md-12">
													<input type="number" min="0" class="form-control" name="tournament_prize_1" id="tournament_prize_1" value="<?php echo $tournamentInfo['tournament_prize_1']; ?>" required />
												</div>
											</div>
											
											<div class="col-md-4">
												<label for="tournament_prize_2" class="col-md-12 col-form-label">2nd Rank Prize (in <span class='reward_type'>Coins</span>)<span class="text-danger">*</span></label>
												<div class="col-md-12">
													<input type="number" min="0" class="form-control" name="tournament_prize_2" id="tournament_prize_2" value="<?php echo $tournamentInfo['tournament_prize_2']; ?>" required />
												</div>
											</div>
											
											<div class="col-md-4">
												<label for="tournament_prize_3" class="col-md-12 col-form-label">3rd Rank Prize (in <span class='reward_type'>Coins</span>)<span class="text-danger">*</span></label>
												<div class="col-md-12">
													<input type="number" min="0" class="form-control" name="tournament_prize_3" id="tournament_prize_3" value="<?php echo $tournamentInfo['tournament_prize_3']; ?>" required />
												</div>
											</div>
										</div>
											
										<div class="form-group row">
											<div class="col-md-4">
												<label for="tournament_prize_4" class="col-md-12 col-form-label">4th - 5th Rank Prize (in <span class='reward_type'>Coins</span>)</label>
												<div class="col-md-12">
													<input type="number" min="0" class="form-control" name="tournament_prize_4" id="tournament_prize_4" value="<?php echo $tournamentInfo['tournament_prize_4']; ?>"  />
												</div>
											</div>
											
											<div class="col-md-4">
												<label for="tournament_prize_5" class="col-md-12 col-form-label">6th to 10th Rank Prize (in <span class='reward_type'>Coins</span>)</label>
												<div class="col-md-12">
													<input type="number" min="0" class="form-control" name="tournament_prize_5" id="tournament_prize_5" value="<?php echo $tournamentInfo['tournament_prize_5']; ?>"  />
												</div>
											</div>
											
											<div class="col-md-4">
												<label for="tournament_prize_6" class="col-md-12 col-form-label">11th to 25th Rank Prize (in <span class='reward_type'>Coins</span>)</label>
												<div class="col-md-12">
													<input type="number" min="0" class="form-control" name="tournament_prize_6" id="tournament_prize_6" value="<?php echo $tournamentInfo['tournament_prize_6']; ?>"  />
												</div>
											</div>
										</div>
											
										<div class="form-group row">
											<div class="col-md-4">
												<label for="tournament_prize_7" class="col-md-12 col-form-label">26th - 50th Postion Prize (in <span class='reward_type'>Coins</span>)</label>
												<div class="col-md-12">
													<input type="number" min="0" class="form-control" name="tournament_prize_7" id="tournament_prize_7" value="<?php echo $tournamentInfo['tournament_prize_7']; ?>"  />
												</div>
											</div>
											
											<div class="col-md-4">
												<label for="tournament_prize_8" class="col-md-12 col-form-label">51st to 100th Postion Prize (in <span class='reward_type'>Coins</span>)</label>
												<div class="col-md-12">
													<input type="number" min="0" class="form-control" name="tournament_prize_8" id="tournament_prize_8"  value="<?php echo $tournamentInfo['tournament_prize_8']; ?>" />
												</div>
											</div>
										</div>
										
                                       
                                    </div>
                                </div> <!-- end card -->

                            </div> <!-- end col -->
                        </div>
						<?php } ?>
						
							
						<div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header bg-soft-primary ">
									 <h4 class="header-title mt-0 mb-1">Update Banner</h4>
									</div>
                                    <div class="card-body">
                                       	
										<div class="form-group row">
											<label for="banner_location" class="col-md-4 col-form-label">Banner Page Location </label>
											<div class="col-md-6">
												<select class="form-control" name="banner_location" id="banner_location"  >
														<option value="">Choose Location</option> 
														<option value="1" <?php if($bannerInfo['banner_location'] == '1') { echo "selected"; } ?>>Home Page</option> 
														<option value="2" <?php if($bannerInfo['banner_location'] == '2') { echo "selected"; } ?>>Practice Zone Page</option> 
														
												</select>
											</div>
										</div>
										
										<div class="form-group row">
											<label for="banner_position" class="col-md-4 col-form-label">Banner Page Location </label>
											<div class="col-md-6">
												<select class="form-control" name="banner_position" id="banner_position"  >
														<option value="">Choose Position</option> 
														<option value="1" <?php if($bannerInfo['banner_position'] == '1') { echo "selected"; } ?>>Header</option> 
														<option value="2" <?php if($bannerInfo['banner_position'] == '2') { echo "selected"; } ?>>Footer</option> 
														
												</select>
											</div>
										</div>
										<?php if(!empty($bannerInfo['banner_image_path'])){ ?>
										<div class="form-group row">
											<?php if($bannerInfo['uploaded']==1){ ?>
											<label for="banner_image_path" class="col-md-4 col-form-label">Uploaded Image</label>
											<div class="col-md-5">
												<img src="<?php echo base_url() ?>uploads/tournaments-banners/<?php echo $bannerInfo['banner_image_path']; ?>" class="img-responsive" style="max-width:80%; border:2px solid #ccc; padding:3px; border-radius:5px;" />
											
											</div>
											<?php } ?>
											<?php if($bannerInfo['uploaded']==0){ ?>
											<label for="banner_image_path" class="col-md-4 col-form-label">Default Image</label>
											<div class="col-md-5">
												<img src="<?php echo base_url('uploads/games/'.$bannerInfo['banner_image_path']); ?>" class="img-responsive" style="max-width:80%; border:2px solid #ccc; padding:3px; border-radius:5px;" />	
											</div>
											<?php } ?>
										</div>
										
										<div class=" row"><div class="col-md-12"><br></div></div>
										<?php } ?>
										<div class="form-group row">
											<label for="banner_image_path" class="col-md-4 col-form-label">Upload New  Image   <br>  (Only if want to change the already uploaded image)</label>
											<div class="col-md-6">
												<input type="file"  name="banner_image_path" id="banner_image_path"  />
													
											</div>
										</div>
											
											
											
                                 
											<div class="form-group row">
                                               <div class="col-md-12"> <br> </div>
                                            </div>
											
											
                                           
                                 
                                            <div class="form-group row">
                                                <div class="col-md-8 offset-md-4">
                                                    <button type="submit" class="btn btn-primary btn-lg mr-1"> Save Tournament</button>
                                                    <button type="reset" class="btn btn-secondary btn-lg"> Reset </button>
                                                </div>
                                            </div>
                                       
                                    </div>
                                </div> <!-- end card -->

                            </div> <!-- end col -->
                        </div>
                        <!-- end row -->
					
						
						
						
					</form>
                     
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

      

        <!-- Right bar overlay-->
        <div class="rightbar-overlay"></div>

        <!-- Vendor js -->
        <script src="<?php echo base_url() ?>assets/admin/js/vendor.min.js"></script>

        <!-- Plugin js-->
        <script src="<?php echo base_url() ?>assets/admin/libs/parsleyjs/parsley.min.js"></script>

        <!-- Validation init js-->
        <script src="<?php echo base_url() ?>assets/admin/js/pages/form-validation.init.js"></script>

        <!-- App js -->
        <script src="<?php echo base_url() ?>assets/admin/js/app.min.js"></script>
        
		
		<script>
		$(document).ready(function(){
			$("#tournament_category_id").change(function(){
				var category = $(this).val();
				var dataStr = "category="+category;
				$.ajax({
					url: "<?php echo site_url('admin/getCategoryGamesAjax') ?>",
					type: "POST",
					data: dataStr,
					success: function(data){
						if(data){
							$("#tournament_game_id").html(data);
						} else {
							$("#tournament_game_id").val('');
						}
					}
				});
			});
		});
		</script>
		
		<script>
		function changeType(id)
		{
			// alert(id);
			$("#tournament_reward_type_"+id).change(function(){
				var type = $(this).val();
				if(type== '1'){
					$('.reward_type'+id).empty();
					$('.reward_type'+id).append('Coins');
				} else if(type== '2'){
					$('.reward_type'+id).empty();
					$('.reward_type'+id).append('Data Pack');
				} else if(type== '3'){
					$('.reward_type'+id).empty();
					$('.reward_type'+id).append('Talk Time');
				} else {
					$('.reward_type'+id).empty();
					$('.reward_type'+id).append('Coins');
				}
			});
		}
		</script>
		
		
		
    </body>
</html>