<?php
$work_dir = str_replace('\\', '\\\\', $working_directory);

// Function to recursively copy files and directories
function recurse_copy($src, $dst, $copy_all = true, $specific_files = [])
{
    $dir = opendir($src);
    @mkdir($dst);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file) && $copy_all) {
                recurse_copy($src . '/' . $file, $dst . '/' . $file);
            } else {
                if ($copy_all || in_array($file, $specific_files)) {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
    }
    closedir($dir);
}

// Load parameters.xml file
$params_xml = simplexml_load_file($working_directory . "parameters.xml");
$template_directory = "";
foreach ($params_xml->group as $group) {
    if ((string)$group['name'] == 'template_directory') {
        $template_directory = (string)$group->param['second'];
        break;
    }
}
$temp_dir = str_replace('\\', '\\\\', $template_directory);

// Copy the entire 'CAST' directory from templates to working directory
$CAST_template_dir = $temp_dir . "Anybody\\CAST";
$CAST_work_dir = $work_dir . "\\CAST";
recurse_copy($CAST_template_dir, $CAST_work_dir);

// Copy specific files for Subject SQ1
$CAST_subjects_sq1_dir = $temp_dir . "Anybody\\CAST\\Subjects\\SQ1";
$CAST_subjects_sq1_work_dir = $work_dir . "\\CAST\\Subjects\\SQ1";
@mkdir($CAST_subjects_sq1_work_dir, 0777, true);
$specific_files = ['SubjectSpecificData.any']; // The specific file(s) to copy

foreach ($specific_files as $specific_file) {
    if (file_exists($CAST_subjects_sq1_dir . "\\" . $specific_file)) {
        copy($CAST_subjects_sq1_dir . "\\" . $specific_file, $CAST_subjects_sq1_work_dir . "\\" . $specific_file);
    }
}



// Process dynamic trials from parameters.xml and handle Static 1 separately
$dynamic_trials = [];
foreach ($params_xml->group as $group) {
    if ((string)$group['name'] == 'measurement_guids') {
        foreach ($group->param as $param) {
            $trial_name = str_replace('.qtm', '', (string)$param['first']);
            if (preg_match('/Static 1|CAST FB \d+/', $trial_name)) {
                // Static 1 will be copied directly, CAST FB trials will be copied from Dynamic 1
                $dynamic_trials[$trial_name] = $trial_name;
            }
        }
    }
}

// Function to find and rename the first available static c3d file
function rename_first_static_c3d($work_dir) {
    $renamed = false;
    for ($i = 1; $i <= 4; $i++) {
        $old_file = $work_dir . "\\Static $i.c3d";
        if (file_exists($old_file)) {
            $new_file = $work_dir . "\\Static 1.c3d";
            if ($i !== 1) { // No need to rename if it's already Static 1
                rename($old_file, $new_file);
            }
            $renamed = true;
            break;
        }
    }
    return $renamed;
}

// Find and rename the first available static c3d file
$static_renamed = rename_first_static_c3d($work_dir);

// Copy all .c3d files to a specific folder in the working directory
$c3d_files_dir = $work_dir . "\\CAST\\c3d_files";
@mkdir($c3d_files_dir, 0777, true);
foreach (glob($work_dir . '\\*.c3d') as $c3d_file) {
    $file_name = basename($c3d_file);
    copy($c3d_file, $c3d_files_dir . '\\' . $file_name);
}

// If a static c3d file was renamed, ensure only the new Static 1.c3d exists in c3d_files directory
if ($static_renamed) {
    // Remove any other static c3d files that may have been copied
    for ($i = 2; $i <= 4; $i++) {
        $extra_file = $c3d_files_dir . "\\Static $i.c3d";
        if (file_exists($extra_file)) {
            unlink($extra_file);
        }
    }
}

foreach ($dynamic_trials as $trial_name) {
    $source_dir = $trial_name;
    // Determine if it's a CAST FB trial and not the first one
    if (strpos($trial_name, 'CAST FB') !== false && $trial_name != 'CAST FB 1') {
        $source_dir = 'CAST FB 1'; // Use CAST FB 1 as the source for all other CAST FB trials
    }

    $source_path = $CAST_subjects_sq1_dir . "\\" . $source_dir;
    $dest_path = $CAST_subjects_sq1_work_dir . "\\" . $trial_name;
    @mkdir($dest_path, 0777, true);
    
    // Use recurse_copy function defined earlier to copy the directory
    recurse_copy($source_path, $dest_path);
}



// Copy all .c3d files to a specific folder in the working directory
$c3d_files_dir = $work_dir . "\\CAST\\c3d_files";
@mkdir($c3d_files_dir, 0777, true);
foreach (glob($work_dir . '\\*.c3d') as $c3d_file) {
    $file_name = basename($c3d_file);
    copy($c3d_file, $c3d_files_dir . '\\' . $file_name);
}


// Define the directory containing the session.xml
$xml_directory = $work_dir; // Replace with the actual directory path if different

// Define the file paths for session.xml and SubjectSpecificData.any
$session_xml_file = $xml_directory . "\\session.xml";
$subject_specific_data_file = $xml_directory . "\\CAST\\Subjects\\SQ1\\SubjectSpecificData.any";

// Check if session.xml file exists
if (file_exists($session_xml_file)) {
    // Load the XML file with DOMDocument
    $dom = new DOMDocument();
    $dom->load($session_xml_file);

    // Access the Height and Weight values
    $height = $dom->getElementsByTagName('Height')->item(0)->nodeValue;
    $weight = $dom->getElementsByTagName('Weight')->item(0)->nodeValue;

    // Format the height and weight values
    $height_formatted = number_format((float)$height, 2, '.', '');
    $weight_formatted = number_format((float)$weight, 1, '.', '');

    // Check if the values are numeric and not zero
    if (is_numeric($height) && is_numeric($weight) && $height != 0 && $weight != 0) {
        // Check if SubjectSpecificData.any file exists
        if (file_exists($subject_specific_data_file)) {
            // Read SubjectSpecificData.any file
            $subject_specific_data_contents = file_get_contents($subject_specific_data_file);

            // Replace BodyMass and BodyHeight with the new values from session.xml
            // Ensure that only one semicolon is present at the end of each line
            $subject_specific_data_contents = preg_replace('/(BodyMass\s*=\s*)\d+(\.\d+)?\s*;*/', "BodyMass = {$weight_formatted};", $subject_specific_data_contents);
            $subject_specific_data_contents = preg_replace('/(BodyHeight\s*=\s*)\d+(\.\d+)?\s*;*/', "BodyHeight = {$height_formatted};", $subject_specific_data_contents);

            // Save the modified contents back to the SubjectSpecificData.any file
            file_put_contents($subject_specific_data_file, $subject_specific_data_contents);
        } else {
            die("Error: The SubjectSpecificData.any file does not exist in the specified directory.");
        }
    } else {
        die("Error: Invalid height or weight values in session.xml. Height is '{$height}', Weight is '{$weight}'.");
    }
} else {
    die("Error: The session.xml file does not exist in the specified directory.");
}




?>
