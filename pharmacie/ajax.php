<?php

/**
 * all ajax call will be handle from here
 * */
class Updator
{


    public function __construct()
    {
        if (method_exists($this, $_POST['action'])) {
            $this->{$_POST['action']}($_POST);
        }
    }


    /**
     * it will replace updated controller in core folder
     * @sicne 1.0.0
     * */
    public function _update_controller_files()
    {


        $update_file_paths = ['app', 'css/back', 'css/front', 'config', 'database', 'routes', 'bootstrap', 'vendor', 'views'];
        $old_file_paths = ['../core/app', '../assets/back', '../assets/front', '../core/config', '../core/database', '../core/routes', '../core/bootstrap', '../core/vendor', '../core/resources/views'];

        foreach ($update_file_paths as $key => $update_file_path) {
            $this->ReplaceFileFolder($update_file_path, $old_file_paths[$key]);
        }

        $this->message([
            'type' => 'success',
            'msg' => "Updated Successfully"
        ]);
    }






    public function ReplaceFileFolder($update_file_path, $old_file_path) {
        $all_update_views = $this->get_file_list_by_directory($update_file_path);
        $all_old_views = $this->get_file_list_by_directory($old_file_path);
    
        foreach ($all_update_views as $new_file) {
            if (is_dir($update_file_path . '/' . $new_file)) {
                $old_file = array_search($new_file, $all_old_views);
                $folder_name = @$all_old_views[$old_file];
                if (!file_exists($old_file_path . '/' . $new_file)) {
                    if (!mkdir($concurrentDirectory = $old_file_path . '/' . $new_file) && !is_dir($concurrentDirectory)) {
                        throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
                    }
                    $folder_name = $new_file;
                } else {
                    // Delete the old folder before creating the new one
                    if (is_dir($old_file_path . '/' . $new_file)) {
                        $this->deleteDirectory($old_file_path . '/' . $new_file);
                    }
                    if (!mkdir($concurrentDirectory = $old_file_path . '/' . $new_file) && !is_dir($concurrentDirectory)) {
                        throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
                    }
                    $folder_name = $new_file;
                }
                $this->ReplaceFileFolder($update_file_path . '/' . $new_file, $old_file_path . '/' . $folder_name);
            } else {
                $file_index = array_search($new_file, $all_old_views);
                $update_file_path_new = $update_file_path;
                $script_old_file_path = $old_file_path;
    
                $folder_name = $all_old_views[$file_index] ?? $new_file;
                $update_able_file_size = $this->get_file_size($update_file_path_new . '/' . $new_file);
                $script_able_file_size = $this->get_file_size($script_old_file_path . '/' . $folder_name);
    
                if ($update_able_file_size != $script_able_file_size) {
                    $this->update_file($update_file_path . '/' . $new_file, $script_old_file_path . '/' . $folder_name);
                } elseif (!is_dir($script_old_file_path) && !file_exists($script_old_file_path . '/' . $new_file)) {
                    file_put_contents($script_old_file_path . '/' . $new_file, file_get_contents($update_file_path_new . '/' . $new_file));
                }
            }
        }
    }
    
    // Function to delete a directory and its contents
    private function deleteDirectory($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . '/' . $object)) {
                        $this->deleteDirectory($dir . '/' . $object);
                    } else {
                        unlink($dir . '/' . $object);
                    }
                }
            }
            rmdir($dir);
        }
    }


    /**
     * get file list by directory
     * @since 1.0.0
     * */
    public function get_file_list_by_directory($dir)
    {
        $get_file = array_diff(scandir($dir), array('.', '..', '.DS_Store'));
        return $get_file;
    }

    /**
     * update file
     * @since 1.0.0
     * */
    public function update_file($update_file, $old_file)
    {
        $update_data = file_get_contents($update_file);
        file_put_contents($old_file, $update_data);
    }

    /**
     * get file size
     * @since 1.0.0
     * */
    public function get_file_size($file_path)
    {
        return  file_exists($file_path) ? filesize($file_path) : 0;
    }
    /**
     * convert msg to JSON
     * @since 1.0.0
     * */
    public function message($msg)
    {
        echo json_encode($msg);
    }
}

new Updator();
