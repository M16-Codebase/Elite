<?php
namespace LPS\Components;
/**
 * FS is class for work with File System
 *
 * @author Alexander Shulman
 * @link http://wiki.lpscms.ru
 */

class FS {

	/**
	 * Simple path creator
	 *
	 * @param string $path
	 * @param int $mode
	 */
	static function makeDirs($path, $mode = 0777, $set_umask = true) {
		if ($set_umask)
			$old_umask = umask(0);
		if (!file_exists($path)) {
			self::makeDirs(dirname($path), $mode, false);
			if (!mkdir($path, $mode)) {
				trigger_error('can\'t mkdir "' . $path . '"');
			}
		}
		if ($set_umask)
			umask($old_umask);
	}

	/**
	 * One level dir loader
	 *
	 * @param string $path loaded directory
	 * @param bool $fullpath relative names or absolute
	 * @param Closure $filter callback function for filter results
	 * @param string $return
	 * @return bool|null
	 * @see Config::getRealDocumentRoot()
	 */
	static function loadDir($path, $filter=null, $fullpath = false, $return = 'files') {
		if (!is_null($filter) and !is_callable($filter)) {
			$filter = null;
			trigger_error('Incorrect $filter value');
			return FALSE;
		}
		if (!in_array(substr($path, 0, 1), array('/', '\\')))
			$path = \Config::getRealDocumentRoot().'/'.$path ;
		if (!in_array(substr($path, -1), array('/', '\\')))
			$path = $path . '/';
		$dir = opendir($path);
		if ($dir === FALSE)
			return FALSE;
		$files = array();
		$dirs = array();
		while (false != ($fileName = readdir($dir))) {
			if ($fileName != '.' && $fileName != '..') {
				$absoluteFileName = realpath($path . $fileName);
				$fileName = $fullpath ? $absoluteFileName : $fileName;
				if (!is_dir($absoluteFileName) and $return != "dirs" and (is_null($filter) or call_user_func($filter, $absoluteFileName, 'file'))) {
					$files[] = $fileName;
				}elseif ($return != "files" and (is_null($filter) or call_user_func($filter, $absoluteFileName, 'dir'))) {
					$dirs[]  = $fileName;
				}
			}
		}
		closedir($dir);
		switch ($return) {
			case 'files':
				return $files;
				break;
			case 'dirs':
				return $dirs;
				break;
			default:
				return array('files' => $files, 'dirs' => $dirs);
				break;
		}
	}

    /**
     * Проверяем, цельный ли файл. Он возможно в процессе переноса.
     * @param string $file_path
     * @return bool
     */
    public static function isFileWhole($file_path){
        clearstatcache(TRUE, $file_path);
        $size1 = filesize($file_path);
        sleep(1);
        clearstatcache(TRUE, $file_path);
        $size2 = filesize($file_path);
        if ($size1 == $size2){
            return TRUE;
        }
        return FALSE;
    }
    
    public static function getFileLineCount($file){
        $handle = @fopen($file, "r");
        $lines=0;
        if ($handle) {
            while (fgets($handle, 4096) !== false) {
                 $lines++;
            }
            fclose($handle);
        }
        return $lines;
    }

    public static function getMime($file){
		if (function_exists("finfo_file")) {
			$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
			$mime = finfo_file($finfo, $file);
			finfo_close($finfo);
			return $mime;
		} else if (function_exists("mime_content_type")) {
			return mime_content_type($file);
		} else if (!stristr(ini_get("disable_functions"), "shell_exec")) {
			// http://stackoverflow.com/a/134930/1593459
			$file = escapeshellarg($file);
			$mime = shell_exec("file -bi " . $file);
			return $mime;
		} else {
			return false;
		}
	}
	public static function removeDirectory($path) {
		if (file_exists($path) && is_dir($path)) {
			$dirHandle = opendir($path);
			while (false!==($file = readdir($dirHandle))) {
				if($file!='.' && $file!='..'){
					$tmpPath = $path.'/'.$file;
					if(is_dir($tmpPath)){
						self::removeDirectory($tmpPath);
					} else {
						unlink($tmpPath);
					}
				}
			}
			closedir($dirHandle);
			// удаляем текущую папку
			rmdir($path);
		}
	}
    /**
	 * go into subdirectorys looking for more files
	 * @see self::pregFind
	 */
	const PREG_FIND_RECURSIVE = 1;
	/**
	 * return directorys that match the pattern also
	 * @see self::pregFind
	 */
	const PREG_FIND_DIRMATCH = 2;
	/**
	 * search for the pattern in the full path (dir+file)
	 * @see self::pregFind
	 */
	const PREG_FIND_FULLPATH = 4;
	/**
	 * return files that don't match the pattern
	 * @see self::pregFind
	 */
	const PREG_FIND_NEGATE = 8;
	/**
	 * return only directorys that match the pattern (no files)
	 * @see self::pregFind
	 */
	const PREG_FIND_DIRONLY = 16;
	/**
	 * Instead of just returning a plain array of matches,
	 * return an associative array with file stats
	 * @see self::pregFind
	 */
	const PREG_FIND_RETURNASSOC = 32;
	/**
	 * Reverse order of sort
	 * @see self::pregFind
	 */
	const PREG_FIND_SORTDESC = 64;
	/**
	 * Sort on the keyvalues or non-assoc array results
	 * @see self::pregFind
	 */
	const PREG_FIND_SORTKEYS = 128;
	/**
	 * Sort the results in alphabetical order on filename
	 * requires PREG_FIND_RETURNASSOC
	 * @see self::PREG_FIND_RETURNASSOC
	 * @see self::pregFind
	 */
	const PREG_FIND_SORTBASENAME = 256;
	/**
	 * Sort the results in last modified timestamp order
	 * requires PREG_FIND_RETURNASSOC
	 * @see self::PREG_FIND_RETURNASSOC
	 * @see self::pregFind
	 */
	const PREG_FIND_SORTMODIFIED = 512;
	/**
	 * Sort the results based on filesize
	 * requires PREG_FIND_RETURNASSOC
	 * @see self::PREG_FIND_RETURNASSOC
	 * @see self::pregFind
	 */
	const PREG_FIND_SORTFILESIZE = 1024;
	/**
	 * Sort based on the amount of disk space taken
	 * requires PREG_FIND_RETURNASSOC
	 * @see self::PREG_FIND_RETURNASSOC
	 * @see self::pregFind
	 */
	const PREG_FIND_SORTDISKUSAGE = 2048; # requires PREG_FIND_RETURNASSOC

	/**
	 * Search for files matching $pattern in $start_dir.
	 * if args contains PREG_FIND_RECURSIVE then do a recursive search
	 * return value is an associative array, the key of which is the path/file
	 * and the value is the stat of the file.
	 *
	 * this is port from Paul Gregg <pgregg@pgregg.com> preg_find() function
	 * @see http://www.pgregg.com/projects/php/preg_find/preg_find.phps
	 *
	 * @param string $pattern
	 * @param string $start_dir
	 * @param int $args use cont from this class, to use more than one simply seperate them with a | character
	 * @return array
	 */
	static function pregFind($pattern, $start_dir='.', $args=NULL) {
		static $depth = -1;
		++$depth;
		$files_matched = array();
		$fh = opendir($start_dir);
		while (($file = readdir($fh)) !== false) {
			if (strcmp($file, '.')==0 || strcmp($file, '..')==0)
				continue;
			$filepath = $start_dir . '/' . $file;
			if (preg_match($pattern, ($args & self::PREG_FIND_FULLPATH) ? $filepath : $file)) {
				$doadd = is_file($filepath)
						|| (is_dir($filepath) && ($args & self::PREG_FIND_DIRMATCH))
						|| (is_dir($filepath) && ($args & self::PREG_FIND_DIRONLY));
				if ($args & self::PREG_FIND_DIRONLY && $doadd && !is_dir($filepath))
				  	$doadd = false;
				if ($args & self::PREG_FIND_NEGATE)
				  	$doadd = !$doadd;
				if ($doadd) {
					if ($args & self::PREG_FIND_RETURNASSOC) { // return more than just the filenames
						$fileres = array();
						if (function_exists('stat')) {
							$fileres['stat'] = stat($filepath);
							$fileres['du'] = $fileres['stat']['blocks'] * 512;
						}
						if (function_exists('fileowner'))
							$fileres['uid'] = fileowner($filepath);
						if (function_exists('filegroup'))
							$fileres['gid'] = filegroup($filepath);
						if (function_exists('filetype'))
							$fileres['filetype'] = filetype($filepath);
						if (function_exists('mime_content_type'))
							$fileres['mimetype'] = mime_content_type($filepath);
						if (function_exists('dirname'))
							$fileres['dirname'] = dirname($filepath);
						if (function_exists('basename'))
							$fileres['basename'] = basename($filepath);
						if (isset($fileres['uid']) && function_exists('posix_getpwuid'))
							$fileres['owner'] = posix_getpwuid ($fileres['uid']);
						$files_matched[$filepath] = $fileres;
				    }else{
						array_push($files_matched, $filepath);
				    }
				}
			}
			if (is_dir($filepath) && ($args & self::PREG_FIND_RECURSIVE)) {
				$files_matched = array_merge($files_matched,
					self::find($pattern, $filepath, $args)
				);
			}
		}
		closedir($fh);
		// Before returning check if we need to sort the results.
		if (($depth==0) && ($args & (self::PREG_FIND_SORTKEYS|self::PREG_FIND_SORTBASENAME|self::PREG_FIND_SORTMODIFIED|self::PREG_FIND_SORTFILESIZE|self::PREG_FIND_SORTDISKUSAGE)) ) {
			$order = ($args & self::PREG_FIND_SORTDESC) ? 1 : -1;
			$sortby = '';
			if ($args & self::PREG_FIND_RETURNASSOC) {
				if ($args & self::PREG_FIND_SORTMODIFIED)
					$sortby = "['stat']['mtime']";
				if ($args & self::PREG_FIND_SORTBASENAME)
					$sortby = "['basename']";
				if ($args & self::PREG_FIND_SORTFILESIZE)
					$sortby = "['stat']['size']";
				if ($args & self::PREG_FIND_SORTDISKUSAGE)
					$sortby = "['du']";
			}
			$filesort = create_function('$a, $b',
			'	$a1=$a'. $sortby .';
				$b1=$b'. $sortby .';
				if ($a1==$b1)
					return 0;
				else
					return ($a1 < $b1) ? '. $order .' : 0 - '. $order .';
			');
			uasort($files_matched, $filesort);
		}
		--$depth;
		return $files_matched;
	}
}
