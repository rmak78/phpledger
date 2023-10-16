<?php
if (isset($_GET["record_id"])) {
    $record_id = $_GET["record_id"];

    // Sanitize the input by casting it to an integer
    $record_id = (int)$record_id;

    // Select data based on the sanitized record_id
    $row = DB::query("SELECT * FROM people WHERE id=%i", $record_id);
    // var_dump($row);

}
?>

<div class="container">
    <style>
        .container {
            /* width: 1170px; */
            width: auto;
        }
    </style>
    <div class="well well-sm" style="margin-top: 15px;">
        <!-- Add Member -->

        <?php echo isset($record_id) ? "Update Member" : "Add Member"; ?>

        <!-- <a href="<?php //echo SITE_ROOT; 
                        ?>?route=modules/people/list" class=" btn btn-primary btn-xs pull-right">Back</a> -->

    </div>

    <form action="#" method="POST" enctype="multipart/form-data">

        <input type="hidden" id="operation" name="operation" value="<?php echo isset($record_id) ? $record_id : 'create'; ?>">

        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" value="<?php echo isset($row) ? $row[0]['name'] : ''; ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email address:</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" value="<?php echo isset($row) ? $row[0]['email'] : ''; ?>" required>
        </div>
        <div class="form-group">
            <label for="phone">Phone:</label>
            <!-- <input type="tel" class="form-control" id="phone" name="phone" placeholder="0333-3453544" pattern="[0-9]{4}[0-9]{7}" maxlength=" 11" required> -->
            <input type="tel" class="form-control" id="phone" name="phone" placeholder="0333-3453544" value="<?php echo isset($row) ? $row[0]['phone'] : ''; ?>" required>
        </div>
        <div class="form-group">
            <label for="address">Address:</label>
            <input type="text" class="form-control" id="address" name="address" placeholder="Enter your address" value="<?php echo isset($row) ? $row[0]['address'] : ''; ?>" required>
        </div>
        <div class="form-group">
            <label for="image">Image:</label>
            <input type="hidden" class="form-control" id="image" name="oldimage" value="<?php echo isset($row) ? $row[0]['image'] : ''; ?>">
            <input type="file" class="form-control-file mt-3 mb-3" id="image" name="image" accept="image/*">
        </div>

        <button type="submit" name="submit" class="btn btn-success">Submit</button>
        <a href="<?php echo SITE_ROOT; ?>?route=modules/people/list" class=" btn btn-primary">Cancel</a>

    </form>


</div>

<?php

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve form data
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $address = $_POST["address"];
    $oldimage = $_POST["oldimage"];

    // Handle image upload (if needed)
    // $image = ""; // Initialize to an empty string

    if (isset($_FILES["image"])) {
        $imageFile = $_FILES["image"];
        $imageFileName = $imageFile["name"];
        $image = time() . $imageFileName;
        // var_dump($image);
        // die();
        // $uploadDirectory = "people/assets_people/"; 
        $uploadDirectory = 'modules/people/assets_people/';

        // Move the uploaded image to the specified directory
        if (move_uploaded_file($imageFile["tmp_name"], $uploadDirectory . $image)) {
            if (!empty($oldimage)) {
                $oldImagePath = 'modules/people/assets_people/' . $oldimage;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            // echo "Image uploaded successfully.";
        } else {
            // echo "Image upload failed.";
            $image = $oldimage;
        }
    }

    // Check the operation type (create or update)
    $operation = $_POST["operation"];

    if ($operation === "create") {
        // Insert the data into the "people" table
        DB::insert('people', [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'image' => $image
        ]);

        // Check if the insertion was successful
        if (DB::affectedRows() > 0) {
            // echo "Member Added Successfully";
            // header("Location: your_redirect_page.php");
            // header("Location: " . SITE_ROOT . "?route=modules/people/list");
?>
            <script>
                Swal.fire({
                    title: "Data Saved",
                    text: "Your data has been saved successfully.",
                    icon: "success"
                }).then(function() {
                    window.location.href = "<?php echo SITE_ROOT; ?>?route=modules/people/list";
                });
            </script>
        <?php
        } else {
            // echo "Error: " . DB::error();
        ?>
            <script>
                Swal.fire({
                    title: "Data is Required",
                    text: "Nothing to Insert.",
                    icon: "question"
                }).then(function() {
                    // window.location.href = "<?php //echo SITE_ROOT; 
                                                ?>?route=modules/people/list";
                });
            </script>
        <?php
        }
    } elseif ($operation !== "create") {

        DB::update('people', [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'image' => $image
        ], "id=%i", $record_id);

        if (DB::affectedRows() > 0) {
        ?>
            <script>
                Swal.fire({
                    title: "Data Saved",
                    text: "Your data has been Updated successfully.",
                    icon: "success"
                }).then(function() {
                    window.location.href = "<?php echo SITE_ROOT; ?>?route=modules/people/list";
                });
            </script>
        <?php
        } else {
            // echo "Error: " . DB::error();
            // echo "Nothing is Updated";
        ?>
            <script>
                Swal.fire({
                    title: "Changes Are Not Detected",
                    text: "Nothing to Update.",
                    icon: "error"
                }).then(function() {
                    // window.location.href = "<?php //echo SITE_ROOT; 
                                                ?>?route=modules/people/list";
                });
            </script>
<?php
        }
    }
}
?>