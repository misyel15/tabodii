<?php
session_start();
include('db_connect.php');
include 'includes/header.php';

if (isset($_GET['id'])) {
    $stmt = $conn->prepare("SELECT * FROM loading WHERE id = ?");
    $stmt->bind_param("i", $_GET['id']); // 'i' specifies the type as integer
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        foreach ($row as $k => $v) {
            $meta[$k] = $v;
        }
    }
    $stmt->close();
}


// Assuming you store the department ID in the session during login
$dept_id = $_SESSION['dept_id']; // Get the department ID from the session
?>
<div class="container-fluid" style="margin-top:100px; margin-left:-15px;">
    <div class="container-fluid mt-5">
        <!-- Table Panel for Monday/Wednesday -->
        <div class="card mb-4">
            <div class="card-header text-center">
                <h3>Monday/Wednesday</h3>
                <div class="d-flex justify-content-end">
                    <!-- Print Button -->
                    <button type="button" class="btn btn-success btn-sm btn-flat mr-2" id="print">
                        <span class="glyphicon glyphicon-print"></span><i class="fa fa-print"></i> Print
                    </button>
                    <button class="btn btn-primary btn-sm" id="new_schedule_mw" data-toggle="modal"
                        data-target="#newScheduleModal">
                        <i class="fa fa-user-plus"></i> New Entry
                    </button>
                </div>
                <form method="POST" class="form-inline mt-2" id="printra" action="roomassign_generate.php">
                    <!-- Form elements if needed -->
                </form>
            </div>
            <div class="card-body">
                <table class="table table-bordered waffle no-grid" id="insloadtable">
                    <thead>
                        <tr>
                            <th class="text-center">Time</th>
                            <?php
                            // PHP code to generate table headers
                            $rooms = array();
                            $roomsdata = $conn->query("SELECT * FROM roomlist WHERE dept_id = '$dept_id' ORDER BY room_id;");
                            while ($r = $roomsdata->fetch_assoc()) {
                                $rooms[] = $r['room_name'];
                            }
                            foreach ($rooms as $room) {
                                echo '<th class="text-center">' . htmlspecialchars($room) . '</th>';
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $times = array();
                        $timesdata = $conn->query("SELECT * FROM timeslot WHERE schedule='MW' AND dept_id = '$dept_id' ORDER BY time_id;");
                        while ($t = $timesdata->fetch_assoc()) {
                            $times[] = $t['timeslot'];
                        }

                        foreach ($times as $time) {
                            echo "<tr><td>$time</td>";
                            foreach ($rooms as $room) {
                                $query = "SELECT * FROM loading WHERE timeslot='$time' AND room_name='$room' AND days='MW'";
                                $result = mysqli_query($conn, $query);
                                if (mysqli_num_rows($result) > 0) {
                                    $row = mysqli_fetch_assoc($result);
                                    $course = $row['course'];
                                    $subject = $row['subjects'];
                                    $faculty = $row['faculty'];
                                    $load_id = $row['id'];
                                    $scheds = $subject . " " . $course;
                                    $faculty_name = $conn->query("SELECT CONCAT(lastname, ', ', firstname, ' ', middlename) AS name FROM faculty WHERE id=$faculty")->fetch_assoc()['name'];
                                    $newSched = htmlspecialchars($scheds . " " . $faculty_name);
                                    echo '<td class="text-center content" data-id="' . $load_id . '" data-scode="' . htmlspecialchars($subject) . '">'
                                        . $newSched
                                        . '<br>'
                                        . '<span><button class="btn btn-sm btn-primary edit_load" type="button" data-id="' . $load_id . '"><i class="fa fa-edit"></i> Edit</button></span>'
                                        . '<span><button class="btn btn-sm btn-danger delete_load" type="button" data-id="' . $load_id . '" data-scode="' . htmlspecialchars($subject) . '"><i class="fa fa-trash-alt"></i> Delete</button></span>'
                                        . '</td>';
                                } else {
                                    echo "<td></td>";
                                }
                            }
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Table Panel for Tuesday/Thursday -->
<div class="container-fluid mt-5">
    <!-- Table Panel for Monday/Wednesday -->
    <div class="card mb-4">
        <div class="card-header text-center">
            <h3>Tuesday/Thursday</h3>
            <div class="d-flex justify-content-end">
                <!-- Print Button -->
                <button type="button" class="btn btn-success btn-sm btn-flat mr-2" id="printtth">
                    <span class="glyphicon glyphicon-print"></span><i class="fa fa-print"></i> Print
                </button>
                <form method="POST" class="form-inline" id="printratth" action="roomassign_generatetth.php">
                    <!-- Form elements if needed -->
                </form>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered waffle no-grid" id="insloadtable">
                <thead>
                    <tr>
                        <th class="text-center">Time</th>
                        <?php
                        // PHP code to generate table headers
                        $rooms = array();
                        $roomsdata = $conn->query("SELECT * FROM roomlist WHERE dept_id = '$dept_id' order by room_id;");
                        while ($r = $roomsdata->fetch_assoc()) {
                            $rooms[] = $r['room_name'];
                        }
                        foreach ($rooms as $room) {
                            echo '<th class="text-center">' . $room . '</th>';
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $times = array();
                    $timesdata = $conn->query("SELECT * FROM timeslot WHERE schedule='TTH' AND dept_id = '$dept_id' order by time_id;");
                    while ($t = $timesdata->fetch_assoc()) {
                        $times[] = $t['timeslot'];
                    }

                    foreach ($times as $time) {
                        echo "<tr><td>$time</td>";
                        foreach ($rooms as $room) {
                            $query = "SELECT * FROM loading WHERE timeslot='$time' AND room_name='$room' AND days='TTH'";
                            $result = mysqli_query($conn, $query);
                            if (mysqli_num_rows($result) > 0) {
                                $row = mysqli_fetch_assoc($result);
                                $course = $row['course'];
                                $subject = $row['subjects'];
                                $faculty = $row['faculty'];
                                $load_id = $row['id'];
                                $scheds = $subject . " " . $course;
                                $faculty_name = $conn->query("SELECT concat(lastname, ', ', firstname, ' ', middlename) as name FROM faculty WHERE id=$faculty")->fetch_assoc()['name'];
                                $newSched = $scheds . " " . $faculty_name;
                                echo '<td class="text-center content" data-id="' . $load_id . '" data-scode="' . $subject . '">'
                                    . $newSched
                                    . '<br>'
                                    . '<span><button class="btn btn-sm btn-primary edit_load" type="button" data-id="' . $load_id . '" data-toggle="modal" data-target="#newScheduleModal"><i class="fa fa-edit"></i> Edit</button></span>'

                                    . '<span><button class="btn btn-sm btn-danger delete_load" type="button" data-id="' . $load_id . '" data-scode="' . $subject . '"><i class="fa fa-trash-alt"></i> Delete</button></span>'
                                    . '</td>';
                            } else {
                                echo "<td></td>";
                            }
                        }
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Table Panel for Tuesday/Thursday -->
<div class="container-fluid mt-5">
    <!-- Table Panel for Monday/Wednesday -->
    <div class="card mb-4">
        <div class="card-header text-center">
            <h3>Friday/Saturday</h3>
            <div class="d-flex justify-content-end">
                <!-- Print Button -->
                <button type="button" class="btn btn-success btn-sm btn-flat mr-2" id="printfri">
                    <span class="glyphicon glyphicon-print"></span><i class="fa fa-print"></i> Print
                </button>
                <form method="POST" class="form-inline" id="printrafri" action="roomassign_generatefri.php">
                    <!-- Form elements if needed -->
                </form>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered waffle no-grid" id="insloadtable">
                <thead>
                    <tr>
                        <th class="text-center">Time</th>
                        <?php
                        // PHP code to generate table headers
                        $rooms = array();
                        $roomsdata = $conn->query("SELECT * FROM roomlist WHERE dept_id = '$dept_id' order by room_id;");
                        while ($r = $roomsdata->fetch_assoc()) {
                            $rooms[] = $r['room_name'];
                        }
                        foreach ($rooms as $room) {
                            echo '<th class="text-center">' . $room . '</th>';
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $times = array();
                    $timesdata = $conn->query("SELECT * FROM timeslot WHERE schedule='FS' AND dept_id = '$dept_id' order by time_id;");
                    while ($t = $timesdata->fetch_assoc()) {
                        $times[] = $t['timeslot'];
                    }

                    foreach ($times as $time) {
                        echo "<tr><td>$time</td>";
                        foreach ($rooms as $room) {
                            $query = "SELECT * FROM loading WHERE timeslot='$time' AND room_name='$room' AND days='FS'";
                            $result = mysqli_query($conn, $query);
                            if (mysqli_num_rows($result) > 0) {
                                $row = mysqli_fetch_assoc($result);
                                $course = $row['course'];
                                $subject = $row['subjects'];
                                $faculty = $row['faculty'];
                                $load_id = $row['id'];
                                $scheds = $subject . " " . $course;
                                $faculty_name = $conn->query("SELECT concat(lastname, ', ', firstname, ' ', middlename) as name FROM faculty WHERE id=$faculty")->fetch_assoc()['name'];
                                $newSched = $scheds . " " . $faculty_name;
                                echo '<td class="text-center content" data-id="' . $load_id . '" data-scode="' . $subject . '">'
                                    . $newSched
                                    . '<br>'
                                    . '<span><button class="btn btn-sm btn-primary edit_load" type="button" data-id="' . $load_id . '" data-toggle="modal" data-target="#newScheduleModal"><i class="fa fa-edit"></i> Edit</button></span>'

                                    . '<span><button class="btn btn-sm btn-danger delete_load" type="button" data-id="' . $load_id . '" data-scode="' . $subject . '"><i class="fa fa-trash-alt"></i> Delete</button></span>'
                                    . '</td>';
                            } else {
                                echo "<td></td>";
                            }
                        }
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>



<!-- New Entry Modal -->
<div class="modal fade" id="newScheduleModal" tabindex="-1" role="dialog" aria-labelledby="newScheduleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newScheduleModalLabel">New Schedule Entry</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="newScheduleForm" method="post">
                <input type="hidden" name="id" id="load_id" value="">
                <input type="hidden" name="dept_id" value="<?php echo $dept_id; ?>">
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-12">

                        <div class="form-group" id="faculty-input-group">
                                <div class="col-sm-12">
                                    <label>
                                        <input type="checkbox" id="toggle-faculty"> Show Faculty Dropdown
                                    </label>
                                    <label for="faculty-display" class="control-label">Faculty Name</label>
                                    <input type="text" id="faculty-display" name="faculty_display" class="form-control" placeholder="Enter Faculty Name" readonly>
                                    <input type="hidden" id="faculty-id" name="faculty_id" value="">
                                </div>
                            </div>
                            <div class="form-group" id="faculty-dropdown-group">
                                <div class="col-sm-12">
                                    <label for="" class="control-label">Faculty</label>
                                    <select name="faculty" id="" class="custom-select select2">
                                        <option value="0">All</option>
                                        <?php
                                        // Fix the query by moving the dept_id filter to the WHERE clause
                                        $faculty = $conn->query("SELECT *, concat(lastname, ', ', firstname, ' ', middlename) as name 
                                         FROM faculty 
                                         WHERE dept_id = '$dept_id' 
                                         ORDER BY concat(lastname, ', ', firstname, ' ', middlename) ASC");
                                        while ($row = $faculty->fetch_array()):
                                            $facultyId = $row['id'];
                                            $facultyname = $row['name'];
                                            ?>
                                            <option value="<?php echo $facultyId ?>" <?php echo isset($meta['faculty']) && $meta['faculty'] == $facultyId ? 'selected' : '' ?>>
                                                <?php echo ucwords($facultyname) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-12">
                                    <label for="" class="control-label">Semester</label>
                                    <select name="semester" id="semester" class="form-control">
                                        <option value="0" disabled selected>Select Semester</option>
                                        <?php
                                        // Query to fetch semesters based on department
                                        $sql = "SELECT * FROM semester";
                                        $query = $conn->query($sql);

                                        // Loop through the fetched rows and populate the dropdown
                                        while ($row = $query->fetch_array()):
                                            $semester = $row['sem'];
                                            ?>
                                            <option value="<?php echo $semester ?>" <?php echo isset($meta['semester']) && $meta['semester'] == $semester ? 'selected' : '' ?>>
                                                <?php echo ucwords($semester) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="course" class="col-sm-3 control-label">Course</label>

                                <div class="col-sm-12">
                                    <select class="form-control" name="course" id="course" required
                                        onchange="populateYear(this.value)">
                                        <option value="0" disabled selected>Select Course</option>
                                        <?php
                                        // Query to fetch courses based on department
                                        $sql = "SELECT * FROM courses WHERE dept_id = '$dept_id'";
                                        $query = $conn->query($sql);

                                        // Loop through the fetched rows and populate the dropdown
                                        while ($row = $query->fetch_array()):
                                            $course = $row['course'];
                                            ?>
                                            <option value="<?php echo $course ?>" <?php echo isset($meta['coursedesc']) && $meta['coursedesc'] == $course ? 'selected' : '' ?>>
                                                <?php echo ucwords($course) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group" id="section-input-group">
                                <div class="col-sm-12">
                                    <label>
                                        <input type="checkbox" id="toggle-section"> Show Section Dropdown
                                    </label>
                                    <label for="section-display" class="control-label">Yr. and Section</label>
                                    <input type="text" id="section-display" name="section_display" class="form-control" readonly>
                                </div>
                            </div>

                            <div class="form-group" id="section-dropdown-group">
                                <label for="yrsection" class="col-sm-6 control-label">Section</label>

                                <div class="col-sm-12">
                                    <select class="form-control" name="yrsection" id="yrsection" required
                                        onchange="populateSubjects()">
                                        <option value="0" disabled selected>Select Yr. & Sec.</option>
                                        <?php
                                        // Modify SQL query to create an object with year and section
                                    $sql = "SELECT *, CONCAT(year, ' ', section) AS yr_section FROM section WHERE dept_id = '$dept_id' ORDER BY year ASC, section ASC";

                                    $query = $conn->query($sql);

                                    // Loop through and create objects
                                    while ($row = $query->fetch_array()):
                                    $yrsection_data = new stdClass();
                                    $yrsection_data->year = $row['year'];
                                    $yrsection_data->section = $row['section'];
                                    $yrsection_display = ucwords($row['yr_section']); // Combined year and section for display
                                    ?>
                                    <option value="<?php echo htmlspecialchars(json_encode($yrsection_data)) ?>" <?php echo isset($meta['course']) && json_encode($meta['course']) === json_encode($yrsection_data) ? 'selected' : '' ?>>
                                        <?php echo $yrsection_display ?>
                                    </option>
                                    <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>

                                <div class="form-group">
                                    <label for="subject" class="col-sm-3 control-label">Subject</label>

                                    <div class="col-sm-12">
                                        <select class="form-control" name="subject" id="subject" required>
                                            <option value="" disabled selected>Select Subject</option>
                                            <?php
                                            // Query to fetch subjects based on department
                                            $sql = "SELECT * FROM subjects WHERE dept_id = '$dept_id'";
                                            $query = $conn->query($sql);

                                            // Loop through the fetched rows and populate the dropdown
                                            while ($prow = $query->fetch_array()):
                                                $subject = $prow['subject']; // Get the subject value
                                                ?>
                                                <option value="<?php echo htmlspecialchars($subject) ?>" <?php echo isset($meta['subjects']) && $meta['subjects'] == $subject ? 'selected' : '' ?>>
                                                    <?php echo ucwords(htmlspecialchars($subject)) ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" name="description" id="description"
                                    value="<?php echo isset($meta['sub_description']) ? htmlspecialchars($meta['sub_description']) : '' ?>">
                                <input type="hidden" name="total_units" id="total_units"
                                    value="<?php echo isset($meta['total_units']) ? htmlspecialchars($meta['total_units']) : '' ?>">
                                <input type="hidden" name="lec_units" id="lec_units"
                                    value="<?php echo isset($meta['lec_units']) ? htmlspecialchars($meta['lec_units']) : '' ?>">
                                <input type="hidden" name="lab_units" id="lab_units"
                                    value="<?php echo isset($meta['lab_units']) ? htmlspecialchars($meta['lab_units']) : '' ?>">
                                <input type="hidden" name="room_name" id="room_name"
                                    value="<?php echo isset($meta['room_name']) ? htmlspecialchars($meta['room_name']) : '' ?>">

                                <div class="form-group">
                                    <label for="room" class="col-sm-3 control-label">Room</label>

                                    <div class="col-sm-12">
                                        <select class="form-control" name="room" id="room" required>
                                            <option value="" disabled selected>Select Room</option>
                                            <?php
                                            // Query to fetch room list based on department
                                            $sql = "SELECT * FROM roomlist WHERE dept_id = '$dept_id'";
                                            $query = $conn->query($sql);

                                            // Loop through the fetched rows and populate the dropdown
                                            while ($row = $query->fetch_array()):
                                                $room_id = $row['room_id'];
                                                $room_name = $row['room_name'];
                                                ?>
                                                <option value="<?php echo htmlspecialchars($room_id) ?>" <?php echo isset($meta['rooms']) && $meta['rooms'] == $room_id ? 'selected' : '' ?>>
                                                    <?php echo ucwords(htmlspecialchars($room_name)) ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label for="specialization" class="control-label">Days of Week</label>
                                        <select class="form-control" name="days" id="days">
                                            <option value="" disabled selected>Select Days of Week</option>
                                            <?php
                                            // Query to fetch days based on department
                                            $sql = "SELECT * FROM days ";
                                            $query = $conn->query($sql);

                                            // Loop through the fetched rows and populate the dropdown
                                            while ($row = $query->fetch_array()):
                                                ?>
                                                <option value="<?php echo htmlspecialchars($row['days']) ?>" <?php echo isset($meta['days']) && $meta['days'] == $row['days'] ? 'selected' : '' ?>>
                                                    <?php echo ucwords(htmlspecialchars($row['days'])) ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Timeslot Selection -->
                                <input type="hidden" name="timeslot" id="timeslot"
                                    value="<?php echo isset($meta['timeslot']) ? htmlspecialchars($meta['timeslot']) : '' ?>">

                                <div class="form-group">
                                    <label for="timeslot_id" class="col-sm-3 control-label">Timeslot</label>

                                    <div class="col-sm-12">
                                        <select class="form-control" name="timeslot_id" id="timeslot_id" required>
                                            <option value="" disabled selected>Select Timeslot</option>
                                            <?php
                                            // Query to fetch timeslots based on department
                                            $sql = "SELECT * FROM timeslot";
                                            $query = $conn->query($sql);

                                            // Loop through the fetched rows and populate the dropdown
                                            while ($row = $query->fetch_array()):
                                                ?>
                                                <option value="<?php echo htmlspecialchars($row['id']) ?>" <?php echo isset($meta['timeslot_id']) && $meta['timeslot_id'] == $row['id'] ? 'selected' : '' ?>>
                                                    <?php echo ucwords(htmlspecialchars($row['timeslot'] . " " . $row['schedule'])) ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Hidden Inputs -->
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <input type="hidden" class="form-control" name="hours" id="hours"
                                            value="<?php echo isset($meta['hours']) ? htmlspecialchars($meta['hours']) : '' ?>">
                                        <input type="hidden" name="timeslot_sid" id="timeslot_sid"
                                            value="<?php echo isset($meta['timeslot_sid']) ? htmlspecialchars($meta['timeslot_sid']) : '' ?>">
                                    </div>
                                </div>

                                <!-- Modal Footer -->
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>

                                <div class="imgF" style="display: none " id="img-clone">
                                    <span class="rem badge badge-primary" onclick="rem_func($(this))"><i
                                            class="fa fa-times"></i></span>
                                </div>
                                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                <script>
                                    // document.addEventListener('DOMContentLoaded', function () {
                                    // // Event listener for edit buttons
                                    // document.querySelectorAll('.edit_load').forEach(button => {
                                    //     button.addEventListener('click', function () {
                                    //         const loadId = this.getAttribute('data-id');
                                    //         // Call the function to open and populate the edit modal
                                    //         openEditModal(loadId);
                                    //     });
                                    // });



                                    //     });

                                    // $('.edit_load').click(function () {
                                    //     // Get the data-id attribute from the clicked button
                                    //     const loadId = $(this).attr('data-id');

                                    //     // Add the ID to the URL parameter
                                    //     const currentUrl = window.location.href.split('?')[0]; // Remove existing parameters
                                    //     const newUrl = `${currentUrl}?id=${loadId}`;
                                    //     window.history.pushState(null, '', newUrl); // Update the URL without reloading

                                    //     // Set the value of the hidden input in the modal
                                    //     $('#load_id').val(loadId);

                                    //     // Open the modal
                                    //     $('#newScheduleModal').modal('show');
                                    // });
                                    let isEditLoadClicked = false; // Flag to check if edit_load was clicked
                                            // Hide the faculty-dropdown-group when "edit_load" is clicked
                                    $('.edit_load').on('click', function () {
                                        isEditLoadClicked = true;
                                        $('#faculty-dropdown-group').hide();
                                        $('#faculty-input-group').show();
                                        $('#section-dropdown-group').hide();
                                        $('#section-input-group').show();
                                    });

                                    // Show the faculty-dropdown-group when newScheduleModal is closed
                                    $('#newScheduleModal').on('hidden.bs.modal', function () {
                                        $('#faculty-dropdown-group').show();
                                        $('#faculty-input-group').hide();
                                        $('#section-dropdown-group').show();
                                        $('#section-input-group').hide();
                                        isEditLoadClicked = false;
                                    });

                                     // Toggle Faculty Dropdown
                                    $('#toggle-faculty').change(function () {
                                        if ($(this).is(':checked')) {
                                            $('#faculty-dropdown-group').show();
                                            $('#faculty-display').prop('readonly', true); // Set to readonly
                                        } else {
                                            $('#faculty-dropdown-group').hide();
                                            $('#faculty-display').prop('readonly', false); // Remove readonly
                                        }
                                    });

                                    // Toggle Section Dropdown
                                    $('#toggle-section').change(function () {
                                        if ($(this).is(':checked')) {
                                            $('#section-dropdown-group').show();
                                            $('#section-display').prop('readonly', true); // Set to readonly
                                        } else {
                                            $('#section-dropdown-group').hide();
                                            $('#section-display').prop('readonly', false); // Remove readonly
                                        }
                                    });

                                    $(document).on('click', '.edit_load', function () {
                                        const loadId = $(this).data('id'); // Get the data-id from the clicked button
                                        console.log(loadId);
                                        $.ajax({
                                            url: 'fetch_load.php', // Your PHP script to fetch data
                                            method: 'GET',
                                            data: { id: loadId },
                                            dataType: 'json',
                                            success: function (response) {
                                                if (response.success) {
                                                    console.log(response.data.faculty)
                                                    // Populate inputs with the fetched data
                                                    $('#load_id').val(response.data.id);
                                                    $('#faculty-id').val(response.data.faculty).trigger('change');
                                                    $('#faculty-display').val(response.data.full_name).trigger('change'); // Trigger change for select2
                                                    $('#semester').val(response.data.semester);
                                                    $('#course').val(response.data.coursedesc).trigger('change');
                                                    $('#section-display').val(response.data.course);
                                                    //$('#yrsection').val(response.data.year + ' ' + response.data.section);
                                                    $('#subject').val(response.data.subjects).trigger('change');
                                                    $('#room').val(response.data.rooms).trigger('change');
                                                    $('#days').val(response.data.days);
                                                    $('#timeslot_id').val(response.data.timeslot_id).trigger('change');
                                                    
                                                    // Populate hidden inputs
                                                    $('#timeslot').val(response.data.timeslot);
                                                    $('#description').val(response.data.sub_description);
                                                    $('#total_units').val(response.data.total_units);
                                                    $('#lec_units').val(response.data.lec_units);
                                                    $('#lab_units').val(response.data.lab_units);
                                                    $('#hours').val(response.data.hours);
                                                    $('#room_name').val(response.data.room_name);
                                                    $('#timeslot_sid').val(response.data.timeslot_id);

                                                     // Open the modal
                                                    $('#newScheduleModal').modal('show');
                                                } else {
                                                    alert('Failed to fetch data.');
                                                }
                                            },
                                            error: function () {
                                                alert('An error occurred while fetching data.');
                                            }
                                        });
                                    });

                                    $('#newScheduleModal').on('hide.bs.modal', function () {
                                    // Clear all input values in the modal
                                    $('#load_id').val('');
                                    $('#faculty-id').val('').trigger('change');
                                    $('#faculty-display').val('').trigger('change');
                                    $('#semester').val('');
                                    $('#course').val('').trigger('change');
                                    $('#section-display').val('');
                                    $('#subject').val('').trigger('change');
                                    $('#room').val('').trigger('change');
                                    $('#days').val('');
                                    $('#timeslot_id').val('').trigger('change');
                                    
                                    // Clear hidden inputs
                                    $('#timeslot').val('');
                                    $('#description').val('');
                                    $('#total_units').val('');
                                    $('#lec_units').val('');
                                    $('#lab_units').val('');
                                    $('#hours').val('');
                                    $('#room_name').val('');
                                    $('#timeslot_sid').val('');

                                  
                                    // If you are using a select2 plugin, you may need to reset it as well
                                    // Example:
                                    // $('#faculty-id').val('').trigger('change'); // To reset select2 if necessary
                                });

                                    $('.select2').select2({
                                        placeholder: 'Please Select Here',
                                        width: '100%'
                                    })

                                    // Enable validateForm when edit_load is not clicked
                                    $('#newScheduleForm').submit(function (e) {
                                        e.preventDefault();

                                        // If edit_load is clicked, don't validate
                                        if (isEditLoadClicked) {
                                            // Perform the form submission directly if edit_load was clicked
                                            submitForm();
                                            return;
                                        }

                                        // Validate form if edit_load was not clicked
                                        if (!validateForm()) {
                                            Swal.fire({
                                                icon: 'warning',
                                                title: 'Warning!',
                                                text: 'Please fill in all required fields.',
                                                showConfirmButton: true
                                            });
                                            return;
                                        }

                                        // Clear any previous messages
                                        $('#msg').html('');
                                        
                                        // Perform the form submission
                                        submitForm();
                                    });

                                    // Form submission via AJAX
                                    function submitForm() {
                                        $.ajax({
                                            url: 'ajax.php?action=save_roomschedule',
                                            data: new FormData($('#newScheduleForm')[0]),
                                            cache: false,
                                            contentType: false,
                                            processData: false,
                                            method: 'POST',
                                            type: 'POST',
                                            success: function (resp) {
                                                if (resp == 1) {
                                                    Swal.fire({
                                                        icon: 'success',
                                                        title: 'Success',
                                                        text: 'Data successfully saved',
                                                        showConfirmButton: true
                                                    }).then(() => {
                                                        setTimeout(function () {
                                                            location.reload();
                                                        }, 1500);
                                                    });
                                                } else if (resp == 2) {
                                                    Swal.fire({
                                                        icon: 'error',
                                                        title: 'Error',
                                                        text: 'Room and Timeslot already exist',
                                                        showConfirmButton: true
                                                    }).then(() => {
                                                        setTimeout(function () {
                                                            location.reload();
                                                        }, 1500);
                                                    });
                                                } else {
                                                    Swal.fire({
                                                        icon: 'error',
                                                        title: 'Error',
                                                        text: 'An unexpected error occurred'
                                                    });
                                                }
                                            }
                                        });
                                    }

                                    // Form validation function
                                    function validateForm() {
                                        let isValid = true;
                                        $('#newScheduleForm select').each(function () {
                                            if ($(this).val() === null || $(this).val() === "0" || $(this).val() === "") {
                                                isValid = false;
                                                return false;  // Break out of loop if invalid
                                            }
                                        });
                                        return isValid;
                                    }




                                    document.getElementById('timeslot_id').addEventListener('change', function () {
                                        var timeslot_id = document.getElementById("timeslot_id").value;
                                        populatetime(timeslot_id);
                                        populateHours(timeslot_id);
                                    });

                                    document.getElementById('days').addEventListener('change', function () {
                                        var subject = document.getElementById("subject").value;
                                        var days = document.getElementById("days").value;
                                        populatedesc(subject);
                                        populateTimeslot(subject, days);
                                    });
                                    document.getElementById('room').addEventListener('change', function () {
                                        var room_id = document.getElementById("room").value;
                                        populateRoomname(room_id)
                                    });




                                    function getSched(time, id) {
                                        var date = new Date();
                                        var myval = time.split('-');
                                        var dateNow = (date.getMonth() + 1) + "/" + date.getDate() + "/" + date.getFullYear();
                                        var starttime = new Date(dateNow + " " + myval[0])
                                        var endtime = new Date(dateNow + " " + myval[1])
                                        var starthours = starttime.getHours().toString();
                                        var startmins = starttime.getMinutes().toString();
                                        var endhours = endtime.getHours().toString();
                                        var endmins = endtime.getMinutes().toString();
                                        if (starthours.length <= 1) {
                                            starthours = "0" + starthours;
                                        }
                                        else {
                                            starthours = starthours;
                                        }
                                        if (startmins.length <= 1) {
                                            startmins = "0" + startmins;
                                        }
                                        else {
                                            startmins = startmins;
                                        }
                                        if (endhours.length <= 1) {
                                            endhours = "0" + endhours;
                                        } else {
                                            endhours = endhours;
                                        }
                                        if (endmins.length <= 1) {
                                            endmins = "0" + endmins;
                                        }
                                        else {
                                            endmins = endmins
                                        }

                                        var timefrom = starthours + ":" + startmins;
                                        var timeto = endhours + ":" + endmins;
                                        document.getElementById("time_from").value = timefrom;
                                        document.getElementById("time_to").value = timeto;
                                        document.getElementById("timeslot").value = id;
                                    }
                                    function populateRoomname(room_id) {

                                        var room_name = document.getElementById('room_name');


                                        // AJAX request
                                        var xhttp = new XMLHttpRequest();
                                        xhttp.open("POST", "gfg.php", true);
                                        xhttp.setRequestHeader("Content-Type", "application/json");
                                        xhttp.onreadystatechange = function () {
                                            if (this.readyState == 4 && this.status == 200) {
                                                // Response
                                                var response = JSON.parse(this.responseText);

                                                var len = 0;
                                                if (response != null) {
                                                    len = response.length;
                                                }

                                                if (len > 0) {

                                                    for (var i = 0; i < len; i++) {

                                                        var id = response[i].id;
                                                        var room = response[i].room_name;
                                                        room_name.value = room;

                                                    }
                                                }
                                            }
                                        };
                                        var data = { request: 'getRoomName', id: room_id };
                                        xhttp.send(JSON.stringify(data));
                                    }

                                    function populatetime(timeslot_id) {

                                        var desc = document.getElementById('description');


                                        // AJAX request
                                        var xhttp = new XMLHttpRequest();
                                        xhttp.open("POST", "gfg.php", true);
                                        xhttp.setRequestHeader("Content-Type", "application/json");
                                        xhttp.onreadystatechange = function () {
                                            if (this.readyState == 4 && this.status == 200) {
                                                // Response
                                                var response = JSON.parse(this.responseText);

                                                var len = 0;
                                                if (response != null) {
                                                    len = response.length;
                                                }

                                                if (len > 0) {

                                                    for (var i = 0; i < len; i++) {

                                                        var id = response[i].id;
                                                        var time = response[i].timeslot;
                                                        document.getElementById("timeslot").value = time;

                                                    }
                                                }
                                            }
                                        };
                                        var data = { request: 'getTime', id: timeslot_id };
                                        xhttp.send(JSON.stringify(data));
                                    }

                                    function populateHours(timeslot_id) {

                                        var hours = document.getElementById('hours');
                                        var timeid = document.getElementById('timeslot_sid');


                                        // AJAX request
                                        var xhttp = new XMLHttpRequest();
                                        xhttp.open("POST", "gfg.php", true);
                                        xhttp.setRequestHeader("Content-Type", "application/json");
                                        xhttp.onreadystatechange = function () {
                                            if (this.readyState == 4 && this.status == 200) {
                                                // Response
                                                var response = JSON.parse(this.responseText);

                                                var len = 0;
                                                if (response != null) {
                                                    len = response.length;
                                                }

                                                if (len > 0) {

                                                    for (var i = 0; i < len; i++) {

                                                        var hoursdata = response[i].hours;
                                                        var timeiddata = response[i].timeslot_sid;

                                                        hours.value = hoursdata;
                                                        timeid.value = timeiddata;
                                                    }
                                                }
                                            }
                                        };
                                        var data = { request: 'getHours', id: timeslot_id };
                                        xhttp.send(JSON.stringify(data));
                                    }

                                    function populatedesc(subject) {

                                        var desc = document.getElementById('description');
                                        var total_units = document.getElementById('total_units');
                                        var lec_units = document.getElementById('lec_units');
                                        var lab_units = document.getElementById('lab_units');


                                        // AJAX request
                                        var xhttp = new XMLHttpRequest();
                                        xhttp.open("POST", "gfg.php", true);
                                        xhttp.setRequestHeader("Content-Type", "application/json");
                                        xhttp.onreadystatechange = function () {
                                            if (this.readyState == 4 && this.status == 200) {
                                                // Response
                                                var response = JSON.parse(this.responseText);

                                                var len = 0;
                                                if (response != null) {
                                                    len = response.length;
                                                }

                                                if (len > 0) {

                                                    for (var i = 0; i < len; i++) {

                                                        var descriptiondata = response[i].description;
                                                        var unitsdata = response[i].total_units;
                                                        var lecunitsdata = response[i].lec_units;
                                                        var labunitsdata = response[i].lab_units;

                                                        desc.value = descriptiondata;
                                                        total_units.value = unitsdata;
                                                        lec_units.value = lecunitsdata;
                                                        lab_units.value = labunitsdata;

                                                    }
                                                }
                                            }
                                        };
                                        var data = { request: 'getDesc', id: subject };
                                        xhttp.send(JSON.stringify(data));
                                    }

                                    function populateSubjects() {
                                        var e = document.getElementById('yrsection');
                                        var sem = document.getElementById('semester');
                                        var semester = sem.options[sem.selectedIndex].value;
                                        var course = e.options[e.selectedIndex].getAttribute('data-course');
                                        var year = e.options[e.selectedIndex].getAttribute('data-year');
                                        //var attrs = option.attributes;
                                        var courseyear = course + " " + year;
                                        console.log(course);
                                        console.log(year);
                                        console.log(semester);


                                        var subjectel = document.getElementById('subject');

                                        subjectel.innerHTML = "";

                                        var subjectopt = document.createElement('option');
                                        subjectopt.value = 0;
                                        subjectopt.innerHTML = 'Select Subject';
                                        subjectel.appendChild(subjectopt);


                                        // AJAX request
                                        var xhttp = new XMLHttpRequest();
                                        xhttp.open("POST", "gfg.php", true);
                                        xhttp.setRequestHeader("Content-Type", "application/json");
                                        xhttp.onreadystatechange = function () {
                                            if (this.readyState == 4 && this.status == 200) {
                                                // Response
                                                var response = JSON.parse(this.responseText);

                                                var len = 0;
                                                if (response != null) {
                                                    len = response.length;
                                                }

                                                if (len > 0) {
                                                    // Read data and create <option >
                                                    for (var i = 0; i < len; i++) {

                                                        var id = response[i].id;
                                                        var name = response[i].name;
                                                        var specialization = response[i].specialization;

                                                        // Add option to state dropdown
                                                        var opt = document.createElement('option');
                                                        opt.value = id;
                                                        opt.innerHTML = name;

                                                        const special = document.createAttribute("data-special");
                                                        // Set the value of the class attribute:
                                                        special.value = specialization;
                                                        opt.setAttributeNode(special);
                                                        subjectel.appendChild(opt);

                                                    }
                                                }
                                            }
                                        };
                                        var data = { request: 'getSubjects', course: course, year: year, semester: semester };
                                        xhttp.send(JSON.stringify(data));
                                    }

                                    function populateTimeslot(subject, days) {
                                        var e = document.getElementById('subject');
                                        var specialization = e.options[e.selectedIndex].getAttribute('data-special');
                                        console.log(subject)
                                        console.log(days)
                                        console.log(specialization)


                                        var timeslotel = document.getElementById('timeslot_id');

                                        timeslotel.innerHTML = "";

                                        var timeslotopt = document.createElement('option');
                                        timeslotopt.value = 0;
                                        timeslotopt.innerHTML = 'Select Timeslot';
                                        timeslotel.appendChild(timeslotopt);


                                        // AJAX request
                                        var xhttp = new XMLHttpRequest();
                                        xhttp.open("POST", "gfg.php", true);
                                        xhttp.setRequestHeader("Content-Type", "application/json");
                                        xhttp.onreadystatechange = function () {
                                            if (this.readyState == 4 && this.status == 200) {
                                                // Response
                                                var response = JSON.parse(this.responseText);

                                                var len = 0;
                                                if (response != null) {
                                                    len = response.length;
                                                }

                                                if (len > 0) {
                                                    // Read data and create <option >
                                                    for (var i = 0; i < len; i++) {

                                                        var id = response[i].id;
                                                        var name = response[i].name;

                                                        // Add option to state dropdown
                                                        var opt = document.createElement('option');
                                                        opt.value = id;
                                                        opt.innerHTML = name;

                                                        timeslotel.appendChild(opt);

                                                    }
                                                }
                                            }
                                        };
                                        var data = { request: 'getTimeslot', subject: subject, days: days, specialization: specialization };
                                        xhttp.send(JSON.stringify(data));
                                    }

                                    function populateYear(course) {

                                        var yrsectionel = document.getElementById('yrsection');

                                        yrsectionel.innerHTML = "";

                                        var yearopt = document.createElement('option');
                                        yearopt.value = 0;
                                        yearopt.innerHTML = 'Select Yr. & Sec.';
                                        yrsectionel.appendChild(yearopt);


                                        // AJAX request
                                        var xhttp = new XMLHttpRequest();
                                        xhttp.open("POST", "gfg.php", true);
                                        xhttp.setRequestHeader("Content-Type", "application/json");
                                        xhttp.onreadystatechange = function () {
                                            if (this.readyState == 4 && this.status == 200) {
                                                // Response
                                                var response = JSON.parse(this.responseText);

                                                var len = 0;
                                                if (response != null) {
                                                    len = response.length;
                                                }

                                                if (len > 0) {
                                                    // Read data and create <option >
                                                    for (var i = 0; i < len; i++) {

                                                        var id = response[i].id;
                                                        var name = response[i].name;
                                                        var course = response[i].course;
                                                        var year = response[i].year;

                                                        // Add option to state dropdown
                                                        var opt = document.createElement('option');
                                                        opt.value = id;
                                                        opt.innerHTML = name;

                                                        const attcourse = document.createAttribute("data-course");
                                                        const attyear = document.createAttribute("data-year");


                                                        // Set the value of the class attribute:
                                                        attcourse.value = course;
                                                        attyear.value = year;

                                                        opt.setAttributeNode(attcourse);
                                                        opt.setAttributeNode(attyear);
                                                        yrsectionel.appendChild(opt);

                                                    }
                                                }
                                            }
                                        };
                                        var data = { request: 'getYear', course: course };
                                        xhttp.send(JSON.stringify(data));
                                    }

                                    function populateSection(section_id) {
                                        var sectionel = document.getElementById('section');

                                        sectionel.innerHTML = "";

                                        var sectionopt = document.createElement('option');
                                        sectionopt.value = 0;
                                        sectionopt.innerHTML = 'Select Section';
                                        sectionel.appendChild(sectionopt);

                                        // AJAX request
                                        var xhttp = new XMLHttpRequest();
                                        xhttp.open("POST", "gfg.php", true);
                                        xhttp.setRequestHeader("Content-Type", "application/json");
                                        xhttp.onreadystatechange = function () {
                                            if (this.readyState == 4 && this.status == 200) {
                                                // Response
                                                var response = JSON.parse(this.responseText);

                                                var len = 0;
                                                if (response != null) {
                                                    len = response.length;
                                                }

                                                if (len > 0) {
                                                    // Read data and create <option >
                                                    for (var i = 0; i < len; i++) {

                                                        var id = response[i].id;
                                                        var name = response[i].name;

                                                        // Add option to state dropdown
                                                        var opt = document.createElement('option');
                                                        opt.value = id;
                                                        opt.innerHTML = name;
                                                        sectionel.appendChild(opt);

                                                    }
                                                }
                                            }
                                        };
                                        var data = { request: 'getSection', section_id: section_id };
                                        xhttp.send(JSON.stringify(data));
                                    }


                                </script>
                                <style>
                                    td {
                                        vertical-align: middle !important;
                                    }
                                </style>
                                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

                                <script>
                                    $(document).ready(function () {
                                        $(document).on('mouseenter', 'div', function () {
                                            $(this).find(".edit_load").show();
                                            $(this).find(".delete_load").show();
                                        }).on('mouseleave', 'div', function () {
                                            $(this).find(".edit_load").hide();
                                            $(this).find(".delete_load").hide();
                                        });
                                        $('.dropdown-toggle').dropdown();
                                    });
                                    $('.delete_load').click(function () {
                                        confirmDeletion("Are you sure you want to delete this room?", "delete_load", $(this).attr('data-id'));
                                    });

                                    $('.delete_MW').click(function () {
                                        confirmDeletion("Are you sure you want to delete this room?", "delete_MW", $(this).attr('data-day'));
                                    });

                                    $('.delete_TTh').click(function () {
                                        confirmDeletion("Are you sure you want to delete this room?", "delete_TTh", $(this).attr('data-day'));
                                    });

                                    $('.delete_Fri').click(function () {
                                        confirmDeletion("Are you sure you want to delete this room?", "delete_Fri", $(this).attr('data-day'));
                                    });

                                    $('.delete_Sat').click(function () {
                                        confirmDeletion("Are you sure you want to delete this room?", "delete_Sat", $(this).attr('data-day'));
                                    });

                                    function confirmDeletion(message, action, data) {
                                        Swal.fire({
                                            title: 'Confirm Deletion',
                                            text: message,
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#d33',
                                            cancelButtonColor: '#3085d6',
                                            confirmButtonText: 'Yes, delete it!'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                deleteData(action, data);
                                            }
                                        });
                                    }

                                    function deleteData(action, data) {
                                        $.ajax({
                                            url: 'ajax.php?action=' + action,
                                            method: 'POST',
                                            data: { id: data }, // Adjust this if `data` is `days` for other actions
                                            success: function (resp) {
                                                if (resp == 1) {
                                                    Swal.fire({
                                                        icon: 'success',
                                                        title: 'Deleted!',
                                                        text: 'Data successfully deleted.',
                                                        showConfirmButton: true,
                                                    }).then(() => {
                                                        setTimeout(function () {
                                                            location.reload();
                                                        }, 1500);
                                                    });
                                                } else {
                                                    Swal.fire({
                                                        icon: 'error',
                                                        title: 'Error',
                                                        text: 'An unexpected error occurred'
                                                    });
                                                }
                                            }
                                        });
                                    }

                                    $('#print').click(function (e) {
                                        e.preventDefault();
                                        $('#printra').attr('action', 'roomassign_generate.php');
                                        $('#printra').submit();
                                    });
                                    $('#printtth').click(function (e) {
                                        e.preventDefault();
                                        $('#printratth').attr('action', 'roomassign_generatetth.php');
                                        $('#printratth').submit();
                                    });
                                    $('#printfri').click(function (e) {
                                        e.preventDefault();
                                        $('#printrafri').attr('action', 'roomassign_generatefri.php');
                                        $('#printrafri').submit();
                                    });
                                    $('#printsat').click(function (e) {
                                        e.preventDefault();
                                        $('#printrasat').attr('action', 'roomassign_generatesat.php');
                                        $('#printrasat').submit();
                                    });

                                </script>
