<?php 

function set_active($path, $active = 'active') {
    return call_user_func_array('Request::is', (array)$path) ? $active : '';
}

//add image to Public folder
function uploadImage($image, $path, $delete = false) {
    try {
        if ($image) {
            $uploadDir = public_path(sprintf($path));

            if ($delete) {
                File::cleanDirectory($uploadDir);
            }

            $imageName= time() . '.' . $image->getClientOriginalExtension();
            $image->move($uploadDir, $imageName);

            return $imageName;
        }
    } catch (Exception $e) {
        Log::error($e);
    }

    return null;
}

//Render select
function getOptions($options) {
    $results = [];
    foreach (config($options) as $option) {
        $results[$option] = trans($options . '.' . $option);
    }

    return $results;
}

//Save image to file
if (!function_exists('base64ToImage')) {
    function base64ToImage ($base64String, $path)
    {
        $data = explode(',', $base64String);
        $outputFile = str_random(40). '.png';
        $uploadDir = public_path(sprintf($path));
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 755, true);
        }
        file_put_contents($uploadDir . '/' . $outputFile, base64_decode($data[1]));

        return $outputFile; 
    }
}

function randColor() {
    return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
}

