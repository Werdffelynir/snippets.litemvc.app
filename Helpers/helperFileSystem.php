<?php
	

function fileDelete($path, $delDir = FALSE, $level = 0)
{
    $path = rtrim($path, DIRECTORY_SEPARATOR);

    if (!$currentDir = @opendir($path))
        return FALSE;

    while (FALSE !== ($filename = @readdir($currentDir))){
        if ($filename != "." and $filename != ".."){
            if (is_dir($path.DIRECTORY_SEPARATOR.$filename)){
                // Ignore empty folders
                if (substr($filename, 0, 1) != '.')
                    fileDelete($path.DIRECTORY_SEPARATOR.$filename, $delDir, $level + 1);
            }else{
                unlink($path.DIRECTORY_SEPARATOR.$filename);
            }
        }
    }
    
    @closedir($currentDir);

    if ($delDir == TRUE AND $level > 0)
        return @rmdir($path);
        
    return TRUE;
}

function fileInfo($file, $returnedValues = array('name', 'server_path', 'size', 'date'))
{
    if (!file_exists($file))
        return FALSE;

    $fileinfo= array();
    
    if (is_string($returnedValues))
    {
        $returnedValues = explode(',', $returnedValues);
    }

    foreach ($returnedValues as $key)
    {
        switch ($key)
        {
            case 'name':
                $fileinfo['name'] = substr(strrchr($file, DIRECTORY_SEPARATOR), 1);
                break;
            case 'server_path':
                $fileinfo['server_path'] = $file;
                break;
            case 'size':
                $fileinfo['size'] = filesize($file);
                break;
            case 'date':
                $fileinfo['date'] = filemtime($file);
                break;
            case 'readable':
                $fileinfo['readable'] = is_readable($file);
                break;
            case 'writable':
                // There are known problems using is_weritable on IIS.  It may not be reliable - consider fileperms()
                $fileinfo['writable'] = is_writable($file);
                break;
            case 'executable':
                $fileinfo['executable'] = is_executable($file);
                break;
            case 'fileperms':
                $fileinfo['fileperms'] = fileperms($file);
                break;
        }
    }

    return $fileinfo;
}

	
function getExtension($file)
{
    $extension = strtolower(substr(strrchr($file, '.'), 1));
    return $extension;
}

	
function dirMap($sourceDir, $directoryDepth = 0, $hidden = FALSE)
{
    if ($fp = @opendir($sourceDir))
    {
        $filedata	= array();
        $new_depth	= $directoryDepth - 1;
        $sourceDir	= rtrim($sourceDir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

        while (FALSE !== ($file = readdir($fp)))
        {
            // Remove '.', '..', and hidden files [optional]
            if ( ! trim($file, '.') OR ($hidden == FALSE && $file[0] == '.'))
            {
                continue;
            }

            if (($directoryDepth < 1 OR $new_depth > 0) && @is_dir($sourceDir.$file))
            {
                $filedata[$file] = dirMap($sourceDir.$file.DIRECTORY_SEPARATOR, $new_depth, $hidden);
            }
            else
            {
                $filedata[] = $file;
            }
        }

        closedir($fp);
        return $filedata;
    }

    return FALSE;
}




/* DIRS AND FILES FUNCTIONS
 **********************************************************************  */

/**
 * Конвертирует число или строку в байты
 *
 * @since 0.3.0
 *
 * @param   $bytes
 * @param   int $precision
 * @return  string
 */
function toBytes($bytes, $precision = 2)
{
    $kilobyte = 1024;
    $megabyte = $kilobyte * 1024;
    $gigabyte = $megabyte * 1024;
    $terabyte = $gigabyte * 1024;

    if (($bytes >= 0) && ($bytes < $kilobyte)) {
        return $bytes . ' B';

    } elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
        return round($bytes / $kilobyte, $precision) . ' KB';

    } elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
        return round($bytes / $megabyte, $precision) . ' MB';

    } elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
        return round($bytes / $gigabyte, $precision) . ' GB';

    } elseif ($bytes >= $terabyte) {
        return round($bytes / $terabyte, $precision) . ' TB';
    } else {
        return $bytes . ' B';
    }
}





/**
 * Сканирует указаную директорию $path на выявление указаных $type (файлов или директрорий) по умолчанию ито и другое
 *
 * @since 0.3.0
 *
 * @param string    $path   путь
 * @param bool      $type   false - все, 'd'||'dir' - каталоги, 'f'||'file' - файлы
 * @return array
 */
function fileOpen($path, $type=false) {

    $handle = opendir($path) or die("getFiles: Unable to open $path");
    $fileArr = array();
    while ($file = readdir($handle)) {
        if ($file != '.' && $file != '..' ) {

            if($type==false){
                $fileArr[] = $file;
            } elseif( ($type=='d' || $type=='dir') && is_dir($path.DIRECTORY_SEPARATOR.$file) ) {
                $fileArr[] = $file;
                break;
            } elseif( ($type=='f' || $type=='file') && is_file($path.DIRECTORY_SEPARATOR.$file) ) {
                $fileArr[] = $file;
                break;
            }
        }
    }
    closedir($handle);
    return $fileArr;
}



/**
 * Возвращает с указаной директрии массив полных путей всех каталогов и файлов
 *
 * @since 0.3.0
 *
 * @param   string      $directory  путь для сканирования
 * @param   bool        $recursive  усли true сканирование рекурсивное
 * @param   bool|string $type  false - все, 'd'||'dir' - каталоги, 'f'||'file' - файлы
 * @return  array
 */
function directoryToArray($directory, $recursive=false, $type=false) {

    $directory = rtrim($directory, DIRECTORY_SEPARATOR);

    $array_items = array();
    if ($handle = opendir($directory)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                if (is_dir($directory. DIRECTORY_SEPARATOR . $file)) {
                    if($type=='file' || $type=='f')
                        continue;
                    if($recursive) {
                        $array_items = array_merge($array_items, directoryToArray($directory. DIRECTORY_SEPARATOR . $file, $recursive));
                    }
                    $file = $directory . DIRECTORY_SEPARATOR . $file;
                    $array_items[] = preg_replace("/\/\//si", DIRECTORY_SEPARATOR, $file);
                } else {
                    if($type=='dir' || $type=='d')
                        continue;
                    $file = $directory . DIRECTORY_SEPARATOR . $file;
                    $array_items[] = preg_replace("/\/\//si", DIRECTORY_SEPARATOR, $file);
                }
            }
        }
        closedir($handle);
    }
    return $array_items;
}



/******************/
function dirFileInfo($sourceDir, $topLevelOnly = TRUE, $_recursion = FALSE)
{
    static $_dirFileData = array();
    
    $relativePath = $sourceDir;

    if ($fp = @opendir($sourceDir)){
        // reset the array and make sure $sourceDir has a trailing slash on the initial call
        if ($_recursion === FALSE) {
            $sourceDir = rtrim(realpath($sourceDir), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        }

        // foreach (scandir($sourceDir, 1) as $file) // In addition to being PHP5+, scandir() is simply not as fast
        while (FALSE !== ($file = readdir($fp)))
        {
            if (@is_dir($sourceDir.$file) AND strncmp($file, '.', 1) !== 0 AND $topLevelOnly === FALSE)
            {
                dirFileInfo($sourceDir.$file.DIRECTORY_SEPARATOR, $topLevelOnly, TRUE);
            }
            elseif (strncmp($file, '.', 1) !== 0)
            {
                $_dirFileData[$file] = dirFileInfo($sourceDir.$file);
                $_dirFileData[$file]['relativePath'] = $relativePath;
            }
        }

        return $_dirFileData;
    }
    else
    {
        return FALSE;
    }
}

/******************/

function fileNames($sourceDir, $includePath = FALSE, $_recursion = FALSE)
{
    static $_fileData = array();
    if ($fp = @opendir($sourceDir))
    {
        // reset the array and make sure $sourceDir has a trailing slash on the initial call
        if ($_recursion === FALSE)
        {
            $sourceDir = rtrim(realpath($sourceDir), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        }

        while (FALSE !== ($file = readdir($fp)))
        {
            if (@is_dir($sourceDir.$file) && strncmp($file, '.', 1) !== 0)
            {
                fileNames($sourceDir.$file.DIRECTORY_SEPARATOR, $includePath, TRUE);
            }
            elseif (strncmp($file, '.', 1) !== 0)
            {
                $_fileData[] = ($includePath == TRUE) ? $sourceDir.$file : $file;
            }
        }
        return $_fileData;
    }
    else
    {
        return FALSE;
    }
}

















