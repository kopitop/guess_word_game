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

//Check status of a round
function getPlayingPanelView($round) {
    if ($round->isDrawer()) {
        $role = 'drawer';
    } else {
        $role = 'guesser';
    }

    if (!$round->word) {
        return config('room.' . $role . '.view.word');
    } elseif (!$round->image) {
        return config('room.' . $role . '.view.image');
    } elseif (!$round->answer) {
        return config('room.' . $role . '.view.answer');
    }

    return config('room.' . $role . '.view.result');
}
