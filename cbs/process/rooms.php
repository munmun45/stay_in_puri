<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require("../config/config.php");

// Debug function to log information
function debugLog($message) {
    $logFile = dirname(__DIR__) . '/debug_upload.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message" . PHP_EOL, FILE_APPEND | LOCK_EX);
}

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../login");
    exit();
}

// Add debug information at the start
debugLog("=== ROOM PROCESSING START ===");
debugLog("POST data: " . print_r($_POST, true));
debugLog("FILES data: " . print_r($_FILES, true));
debugLog("PHP upload_max_filesize: " . ini_get('upload_max_filesize'));
debugLog("PHP post_max_size: " . ini_get('post_max_size'));
debugLog("PHP max_file_uploads: " . ini_get('max_file_uploads'));

// Handle image upload
function uploadRoomImage($file) {
    debugLog("uploadRoomImage called with: " . print_r($file, true));
    
    // Check if file was uploaded without errors
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        debugLog("No file uploaded or empty tmp_name");
        return ["success" => false, "message" => "No file uploaded or upload error."];
    }
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $upload_errors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
        ];
        
        $error_message = isset($upload_errors[$file['error']]) 
            ? $upload_errors[$file['error']] 
            : 'Unknown upload error';
        
        debugLog("Upload error: " . $error_message);
        return ["success" => false, "message" => $error_message];
    }
    
    // Determine target directory (absolute path)
    $target_dir = dirname(__DIR__) . "/uploads/room_images/";
    debugLog("Target directory: " . $target_dir);
    
    // Create directory if it doesn't exist
    if (!file_exists($target_dir)) {
        debugLog("Creating directory: " . $target_dir);
        if (!mkdir($target_dir, 0755, true)) {
            debugLog("Failed to create directory");
            return ["success" => false, "message" => "Failed to create upload directory: " . $target_dir];
        }
        // Also create index.html to prevent directory listing
        file_put_contents($target_dir . "index.html", "");
        debugLog("Directory created successfully");
    }
    
    // Check if directory is writable
    if (!is_writable($target_dir)) {
        debugLog("Directory is not writable: " . $target_dir);
        return ["success" => false, "message" => "Upload directory is not writable: " . $target_dir];
    }
    
    $fileName = basename($file["name"]);
    $imageFileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $newFileName = uniqid() . '_' . time() . '.' . $imageFileType;
    $target_file = $target_dir . $newFileName;
    
    debugLog("Original filename: " . $fileName);
    debugLog("New filename: " . $newFileName);
    debugLog("Target file: " . $target_file);
    
    // Check if image file is an actual image
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        debugLog("File is not a valid image");
        return ["success" => false, "message" => "File is not a valid image."];
    }
    
    debugLog("Image info: " . print_r($check, true));
    
    // Check file size (limit to 5MB)
    if ($file["size"] > 5000000) {
        debugLog("File too large: " . $file["size"] . " bytes");
        return ["success" => false, "message" => "File is too large. Maximum 5MB allowed."];
    }
    
    // Allow certain file formats
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if(!in_array($imageFileType, $allowed_types)) {
        debugLog("Invalid file type: " . $imageFileType);
        return ["success" => false, "message" => "Only " . implode(', ', $allowed_types) . " files are allowed."];
    }
    
    // Check if file already exists (though unlikely with unique names)
    if (file_exists($target_file)) {
        debugLog("File already exists: " . $target_file);
        return ["success" => false, "message" => "File already exists."];
    }
    
    // Upload file
    debugLog("Attempting to move file from " . $file["tmp_name"] . " to " . $target_file);
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        debugLog("File moved successfully");
        // Return relative path for database storage
        $relative_path = "uploads/room_images/" . $newFileName;
        debugLog("Returning relative path: " . $relative_path);
        return ["success" => true, "file_path" => $relative_path];
    } else {
        // Get more detailed error information
        $error = error_get_last();
        $detailed_error = $error ? $error['message'] : 'Unknown error during file move';
        debugLog("Failed to move file. Error: " . $detailed_error);
        
        return ["success" => false, "message" => "Failed to move uploaded file. Error: " . $detailed_error];
    }
}

// Add new room
if (isset($_POST['add_room'])) {
    debugLog("=== ADD ROOM PROCESSING ===");
    
    $hotel_id = (int)$_POST['hotel_id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $max_capacity = (int)$_POST['max_capacity'];
    $amenities = isset($_POST['amenities']) ? implode(',', $_POST['amenities']) : '';
    
    debugLog("Room data - Hotel ID: $hotel_id, Name: $name, Capacity: $max_capacity");
    
    // Enhanced validation
    $errors = [];
    
    if (empty($hotel_id) || $hotel_id <= 0) {
        $errors[] = "Please select a valid hotel.";
    }
    
    if (empty($name)) {
        $errors[] = "Room name is required.";
    }
    
    if (empty($description)) {
        $errors[] = "Room description is required.";
    }
    
    if (empty($max_capacity) || $max_capacity <= 0) {
        $errors[] = "Valid max capacity is required.";
    }
    
    // Check if hotel exists
    $hotel_check = $conn->prepare("SELECT id FROM hotels WHERE id = ?");
    $hotel_check->bind_param("i", $hotel_id);
    $hotel_check->execute();
    if ($hotel_check->get_result()->num_rows === 0) {
        $errors[] = "Selected hotel does not exist.";
        debugLog("Hotel ID $hotel_id does not exist");
    }
    
    if (!empty($errors)) {
        $error_message = implode("\\n", $errors);
        debugLog("Validation errors: " . implode(", ", $errors));
        echo "<script>alert('Validation Error:\\n" . $error_message . "'); window.location.href = '../room-listing';</script>";
        exit();
    }
    
    // Begin transaction
    $conn->autocommit(FALSE);
    debugLog("Starting database transaction");
    
    try {
        // Insert into database
        $sql = "INSERT INTO rooms (hotel_id, name, description, max_capacity, amenities) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("issis", $hotel_id, $name, $description, $max_capacity, $amenities);
        
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $room_id = $conn->insert_id;
        debugLog("Room inserted successfully with ID: " . $room_id);
        
        if ($room_id <= 0) {
            throw new Exception("Failed to get room ID after insert");
        }
        
        // Handle multiple image uploads
        $image_uploaded = false;
        $upload_errors = [];
        
        debugLog("Processing image uploads...");
        
        if (!empty($_FILES['images']['name'][0])) {
            $total_images = count($_FILES['images']['name']);
            debugLog("Total images to process: " . $total_images);
            
            for ($i = 0; $i < $total_images; $i++) {
                debugLog("Processing image $i: " . $_FILES['images']['name'][$i]);
                
                // Skip if no file name or upload error
                if (empty($_FILES['images']['name'][$i])) {
                    debugLog("Skipping empty file at index $i");
                    continue;
                }
                
                // Check for upload errors
                if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) {
                    $error_msg = "File " . ($i + 1) . ": Upload error code " . $_FILES['images']['error'][$i];
                    $upload_errors[] = $error_msg;
                    debugLog($error_msg);
                    continue;
                }
                
                $file = [
                    'name' => $_FILES['images']['name'][$i],
                    'type' => $_FILES['images']['type'][$i],
                    'tmp_name' => $_FILES['images']['tmp_name'][$i],
                    'error' => $_FILES['images']['error'][$i],
                    'size' => $_FILES['images']['size'][$i]
                ];
                
                debugLog("File data for index $i: " . print_r($file, true));
                
                $upload_result = uploadRoomImage($file);
                
                if ($upload_result['success']) {
                    $image_path = $upload_result['file_path'];
                    $is_primary = (!$image_uploaded) ? 1 : 0; // First successful upload is primary
                    
                    debugLog("Image upload successful: " . $image_path . " (Primary: $is_primary)");
                    
                    $img_sql = "INSERT INTO room_images (room_id, image_path, is_primary) VALUES (?, ?, ?)";
                    $img_stmt = $conn->prepare($img_sql);
                    
                    if ($img_stmt === false) {
                        $error_msg = "Database prepare error for image " . ($i + 1) . ": " . $conn->error;
                        $upload_errors[] = $error_msg;
                        debugLog($error_msg);
                        // Delete the uploaded file since we can't save it to database
                        $full_path = dirname(__DIR__) . "/" . $image_path;
                        if (file_exists($full_path)) {
                            unlink($full_path);
                            debugLog("Deleted orphaned file: " . $full_path);
                        }
                        continue;
                    }
                    
                    $img_stmt->bind_param("isi", $room_id, $image_path, $is_primary);
                    
                    if ($img_stmt->execute()) {
                        $image_uploaded = true;
                        debugLog("Image saved to database successfully");
                    } else {
                        $error_msg = "Database insert error for image " . ($i + 1) . ": " . $img_stmt->error;
                        $upload_errors[] = $error_msg;
                        debugLog($error_msg);
                        // Delete the uploaded file since we couldn't save it to database
                        $full_path = dirname(__DIR__) . "/" . $image_path;
                        if (file_exists($full_path)) {
                            unlink($full_path);
                            debugLog("Deleted orphaned file: " . $full_path);
                        }
                    }
                    
                    $img_stmt->close();
                } else {
                    $error_msg = "Image " . ($i + 1) . ": " . $upload_result['message'];
                    $upload_errors[] = $error_msg;
                    debugLog($error_msg);
                }
            }
        } else {
            debugLog("No images uploaded");
        }
        
        // Log any upload errors but don't fail the transaction
        if (!empty($upload_errors)) {
            debugLog("Image upload errors: " . implode("; ", $upload_errors));
        }
        
        // Commit transaction
        $conn->commit();
        $conn->autocommit(TRUE);
        debugLog("Transaction committed successfully");
        
        if (!$image_uploaded && !empty($_FILES['images']['name'][0])) {
            echo "<script>alert('Room added successfully, but some images failed to upload. Check error log for details.'); window.location.href = '../room-listing';</script>";
        } else {
            echo "<script>alert('Room added successfully.'); window.location.href = '../room-listing';</script>";
        }
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $conn->autocommit(TRUE);
        
        debugLog("Transaction rolled back. Error: " . $e->getMessage());
        echo "<script>alert('Error adding room: " . addslashes($e->getMessage()) . "'); window.location.href = '../room-listing';</script>";
    }
    
    debugLog("=== ADD ROOM PROCESSING END ===");
    exit();
}

// Show debug info if requested
if (isset($_GET['debug']) && $_GET['debug'] === 'upload') {
    echo "<h2>Upload Debug Information</h2>";
    echo "<pre>";
    echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
    echo "post_max_size: " . ini_get('post_max_size') . "\n";
    echo "max_file_uploads: " . ini_get('max_file_uploads') . "\n";
    echo "max_execution_time: " . ini_get('max_execution_time') . "\n";
    echo "memory_limit: " . ini_get('memory_limit') . "\n";
    echo "\nUpload directory: " . dirname(__DIR__) . "/uploads/room_images/\n";
    echo "Directory exists: " . (file_exists(dirname(__DIR__) . "/uploads/room_images/") ? "YES" : "NO") . "\n";
    echo "Directory writable: " . (is_writable(dirname(__DIR__) . "/uploads/room_images/") ? "YES" : "NO") . "\n";
    echo "\nCurrent working directory: " . getcwd() . "\n";
    echo "Script directory: " . __DIR__ . "\n";
    echo "</pre>";
    
    $logFile = dirname(__DIR__) . '/debug_upload.log';
    if (file_exists($logFile)) {
        echo "<h3>Recent Debug Log (last 50 lines):</h3>";
        echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 400px; overflow-y: auto;'>";
        $lines = file($logFile);
        $lastLines = array_slice($lines, -50);
        echo htmlspecialchars(implode('', $lastLines));
        echo "</pre>";
    }
    exit();
}

// Rest of the code remains the same for update, delete operations...
// [Include all the update, delete, and other operations from the fixed version]

debugLog("=== ROOM PROCESSING END ===");
?>