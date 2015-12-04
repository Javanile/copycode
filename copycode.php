<?php

/**
 *
 */
class Copycode
{
    /**
     *
     */
    public static function cli($argv)
    {
        ##
        echo
        "\n".
        "  Copycoder v0.0.1\n".
        "  by Francesco Bianco <bianco@javanile.org>\n".
        "\n";

        ##
        $file = "copycode.json";

        ##
        if (!file_exists($file)) {
            echo "  (?) stop: no '{$file}' found\n";
            exit();
        }

        ##
        $tag = isset($argv[1]) ? $argv[1] : 'default';
        
        ##
        echo "  (!) load: '{$file}'\n";

        ##
        $rules = json_decode(file_get_contents($file), true);

        ##
        if (!isset($rules[$tag])) {
            echo "  (?) stop: no 'default' tag on '{$file}'\n";
            echo "  (!) tags: ".implode(", ",array_keys($rules))."\n";
            exit();
        }

        ##
        foreach($rules[$tag] as $rule) {

            ##
            $src = realpath($rule['src']);

            ##
            if (!$src) {
                echo "  (?) skip: no real path for '".$rule['src']."'\n";
                continue;
            }

            ##
            $dir = realpath($rule['dir']);

            ##
            if (!$dir) {
                echo "  (?) skip: no real path for '".$rule['dir']."'\n";
                continue;
            }

            ##
            echo "  (!) dest: {$dir}\n";

            ##
            $exclude = array();

            ##
            if (isset($rule['exclude']) && is_array($rule['exclude'])) {
                foreach($rule['exclude'] as $path) {
                    $exclude[] = realpath($src.'/'.$path);
                }
            }

            ##
            ##echo "{$src} {$dest}\n";

            ##
            if (is_file($src)) {

            }

            ##
            else if (is_dir($src)) {
                 static::copyFolder($src, $dir, "", $exclude);
            }

            ##
            else {

            }
        }
    }

    /**
     * 
     */
    public static function copyFolder($src, $dir, $rel, $exlcude)
    {
        ## destionation folder to copy file
        $dirDest = realpath($dir.'/'.$rel);
        
        ## create current destination folder to copy file
        if (!is_dir($dirDest)) {
            mkdir($dirDest);
        }
        
        ##
        $files = scandir($src.'/'.$rel);

        ##
        foreach($files as $file) {

            ##
            if (in_array($file, array('.', '..'))) {
                continue;
            }

            ##
            $relFile = $rel ? $rel.'/'.$file : $file;
            
            ##
            $srcFile = realpath($src.'/'.$relFile);

            ##
            if (is_array($exlcude) && in_array($srcFile, $exlcude)) {
                continue;
            }

            ##
            if (is_dir($srcFile)) {

                ##
                static::copyFolder($src, $dir, $relFile, $exlcude);
            }

            else {

                ##
                $dirFile = $dir.'/'.$relFile;

                if (!file_exists($dirFile)) {

                    ##
                    echo "  (!) copy: '{$relFile}'\n";

                    ##
                    copy($srcFile, $dirFile);
                }

                else {

                    ##
                    $srcHash = md5_file($srcFile);

                    ##
                    $dirHash = md5_file($dirFile);

                    ##
                    if ($srcHash != $dirHash) {

                        ##
                        echo "  (!) copy: '{$relFile}'\n";

                        ##
                        copy($srcFile, $dirFile);
                    }
                }
            }            
        }
    }
}

##
Copycode::cli($argv);
