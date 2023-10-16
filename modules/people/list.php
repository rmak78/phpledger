<!-- Content Wrapper. Contains page content -->
<style>
    .container {
        /* width: 1170px; */
        width: auto;
    }
</style>
<div class="container">
    <div class="well well-sm" style="margin-top: 15px;">
        List
        <a href="<?php echo SITE_ROOT; ?>?route=modules/people/add_people" class="btn btn-primary btn-xs pull-right">Add Member</a>
    </div>
    <table class="table">
        <?php
        //Draft Journal Vouchers
        // $tbl_draft = new HTML_Table('', 'table table-striped table-bordered');
        // $tbl_draft->addRow();
        // $tbl_draft->addCell('ID', '', 'header');
        // $tbl_draft->addCell('Date', '', 'header');
        // $tbl_draft->addCell('Ref #', '', 'header');
        // $tbl_draft->addCell('Description', '', 'header');
        // $tbl_draft->addCell('Debit', '', 'header');
        // $tbl_draft->addCell('Credit', '', 'header');
        // $tbl_draft->addCell('Info', '', 'header');
        // $tbl_draft->addCell('Actions', '', 'header');
        ?>
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Image</th>
                <th scope="col">Name</th>
                <th scope="col">Phone</th>
                <th scope="col">Email</th>
                <th scope="col">Address</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <?php
        // $results = DB::query("SELECT * FROM people");
        $results = DB::query("SELECT * FROM people ORDER BY id DESC");

        // $results = DB::query("SELECT * FROM people Where id='2'"); just for practice
        // var_dump($results);
        foreach ($results as $index => $row) {
        ?>
            <tbody>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td>
                        <?php
                        if ($row['image']) {
                        ?>
                            <img src="modules/people/assets_people/<?php echo $row['image'] ?>" width="70px" height="70px" style="border-radius: 48px;" alt="">
                        <?php
                        } else {
                        ?>
                            <img src="modules/people/assets_people/download.png" width="70px" height="70px" style="border-radius: 48px;" alt="">
                        <?php
                        }
                        ?>


                    </td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['phone']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['address']; ?></td>
                    <td>
                        <!-- <a href="<?php echo SITE_ROOT; ?>?route=modules/people/add_people?recored_id=<?php //echo $row['id']; 
                                                                                                            ?>" class="btn btn-primary btn-sm">Edit</a> -->
                        <!-- Use & not ? -->
                        <a href="<?php echo SITE_ROOT; ?>?route=modules/people/add_people&record_id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Edit</a>

                        <a href="#" onclick="confirmDelete(<?php echo $row['id']; ?>);" class="btn btn-danger btn-sm">Delete</a>

                    </td>
                </tr>
            </tbody>
        <?php
        }
        ?>
    </table>
    <!-- </div>
    </div> -->
</div>

<!-- /.content-wrapper --> <!-- Main Footer -->

<?php
// Check if the record ID was provided via GET or POST (you can use either method)

if (isset($_GET["record_id"])) {
    // Get the record ID to delete
    $record_id = $_GET["record_id"];

    // First, select the image file associated with the record
    $imageResult = DB::queryFirstRow("SELECT image FROM people WHERE id=%i", $record_id);
    
    if ($imageResult) {
        $imageToDelete = $imageResult['image'];

        $imagePath = 'modules/people/assets_people/' . $imageToDelete;

        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    DB::delete('people', 'id=%i', $record_id);
    if (DB::affectedRows() > 0) {
?>
        <script>
            window.location.reload();
        </script>
<?php
    }
}
?>

<script>
    function confirmDelete(recordId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Deleted!',
                    text: 'Your file has been deleted.',
                    icon: 'success',
                    timer: 2000, // Display for 2 seconds
                    showConfirmButton: false
                });

                // Delay the redirection by 2 seconds
                setTimeout(function() {
                    // Redirect to your PHP script to delete the record (if needed)
                    window.location.href = '<?php echo SITE_ROOT; ?>?route=modules/people/list&record_id=' + recordId;
                    // 
                }, 1000); // 2000 milliseconds (2 seconds)
            }
        });
    }
</script>