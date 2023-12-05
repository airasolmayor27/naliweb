<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-user-circle-o" aria-hidden="true"></i> Task Management
        <small>Add, Edit, Delete</small>
      </h1>
    </section>
    <section class="content">
         <!-- 
        <div class="row">
            <div class="col-xs-12 text-right">
                <div class="form-group">
                    <a class="btn btn-primary" href="<?php echo base_url(); ?>task/add"><i class="fa fa-plus"></i> Add New task</a>
                </div>
            </div>
        </div>
        -->
        <div class="row">
            <div class="col-md-12">
                <?php
                    $this->load->helper('form');
                    $error = $this->session->flashdata('error');
                    if($error)
                    {
                ?>
                <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <?php echo $this->session->flashdata('error'); ?>                    
                </div>
                <?php } ?>
                <?php  
                    $success = $this->session->flashdata('success');
                    if($success)
                    {
                ?>
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <?php echo $this->session->flashdata('success'); ?>
                </div>
                <?php } ?>
                
                <div class="row">
                    <div class="col-md-12">
                        <?php echo validation_errors('<div class="alert alert-danger alert-dismissable">', ' <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
              <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Task List</h3>
                    <div class="box-counting">
    <span class="counting-text"></span>
</div>
                    <div class="box-tools">
                        <form action="<?php echo base_url() ?>task/taskListing" method="POST" id="searchList">
                            <div class="input-group">
                              <input type="text" name="searchText" value="<?php echo $searchText; ?>" class="form-control input-sm pull-right" style="width: 150px;" placeholder="Search"/>
                              <div class="input-group-btn">
                                <button class="btn btn-sm btn-default searchList"><i class="fa fa-search"></i></button>
                              </div>
                            </div>
                        </form>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                  <table class="table table-hover">
                    <tr>
                        <th>Nali User</th>
                        <th>Nali ID</th>
                        <th>Emergency Type</th>
                        <th>Town/City</th>
                        <th>Location</th>          
                        <th>DEVICE</th>
                        <th>RESCUE STATUS</th>
                        <th>Message</th>
                        <th>Created On</th>
                        <th class="text-center">Actions</th>
                    </tr>
                    <?php
                    if(!empty($records))
                    {
                        foreach($records as $record)
                        {
                    ?>
                    <tr>
                        <td><?php echo $record->taskTitle ?></td>
                        <td><?php echo $record->device_id ?></td>
                        <td><?php echo $record->emergency_type ?></td>
                        <td><?php echo $record->town ?></td>
                        <td><a href="<?php echo $record->link ?>" target="_blank"><?php echo $record->location ?></a></td>
                    
                        <td><?php echo $record->status ?></td>
                        <td><?php echo $record->rescue_status ?></td>
                        <td><?php echo $record->description ?></td>
                        
                        <td><?php echo date("d-m-Y", strtotime($record->createdDtm)) ?></td>
                        <td class="text-center">
                            <a class="btn btn-sm btn-info" href="<?php echo base_url().'task/edit/'.$record->taskId; ?>" title="Edit"><i class="fa fa-pencil"></i></a>
                            <a class="btn btn-sm btn-danger deletetask" href="#" data-taskid="<?php echo $record->taskId; ?>" title="Delete"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php
                        }
                    }
                    ?>
                  </table>
                  
                </div><!-- /.box-body -->
                <div class="box-footer clearfix">
                    <?php echo $this->pagination->create_links(); ?>
                </div>
              </div><!-- /.box -->
            </div>
        </div>
    </section>
</div>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/common.js" charset="utf-8"></script>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('ul.pagination li a').click(function (e) {
            e.preventDefault();            
            var link = jQuery(this).get(0).href;            
            var value = link.substring(link.lastIndexOf('/') + 1);
            jQuery("#searchList").attr("action", baseURL + "task/taskListing/" + value);
            jQuery("#searchList").submit();
        });
    });
    $(document).ready(function () {
        // Function to fetch data using AJAX
        function fetchData() {

            
            $.ajax({
                url: "<?php echo base_url() ?>task/taskListing",
                method: "POST",
                data: { searchText: "<?php echo $searchText; ?>" },
                success: function (data) {
                    // Update the table with new data
                    $(".table-responsive table").html($(data).find("table").html());
                    // Update the counting text
                    updateCountingText();
                },
                error: function (xhr, status, error) {
                 
                }
            });
        }

        var previousTableHtml = null; // Variable to store the previous table HTML content
        var confirmedDeviceIds = JSON.parse(localStorage.getItem('confirmedDeviceIds')) || [];
// Function to fetch data using AJAX
function fetchDataDevice() {
            $.ajax({
                url: '<?php echo base_url() ?>/firebase/retrieve',
                type: 'POST',
                success: function (data) {
                    var jsonData = JSON.parse(data);

                    // Check if the device_id has already been confirmed
                    if (confirmedDeviceIds.includes(jsonData.data.ID)) {
                        console.log("Device already confirmed:", jsonData.data.ID);
                        return; // Do not display Swal.fire
                    }
                    fetchData(); 
                },
                error: function (error) {
                    console.error('Error retrieving data:', error);
                }
            });
        }

// Function to update the counting text
function updateCountingText() {
    var countingText = "Fetching new data in ";
    var countdown = 10;

    // Update the counting text
    $(".counting-text").text(countingText + countdown + " seconds");

    // Start countdown timer
    var intervalId = setInterval(function () {
        countdown--;

        // Update the counting text
        $(".counting-text").text(countingText + countdown + " seconds");

        // When countdown reaches 0, fetch new data and reset countdown
        if (countdown === 0) {
            fetchDataDevice();
            countdown = 10;
        }
    }, 1000);
}

        // Initial fetch and countdown
        fetchData();
        fetchDataDevice(); 

        // Fetch data every 10 seconds
       setInterval(function () {
      
            fetchDataDevice(); // Call fetchDataDevice every 10 seconds
        }, 10000);
    });
</script>
