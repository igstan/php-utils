<?php

/**
 * The $_FILES superglobal presents a very weird structure when there are form
 * elements of type array (<input type="file" name="images[]">). If the user
 * uploads two images, the generated $_FILES array will be:
 *
 *   Array
 *   (
 *       [images] => Array
 *           (
 *               [name] => Array
 *                   (
 *                       [0] => foo.txt
 *                       [1] => bar.txt
 *                   )
 *               [tmp_name] => Array
 *                   (
 *                       [0] => 123.tmp
 *                       [1] => 456.tmp
 *                   )
 *               [type] => Array
 *                   (
 *                       [0] => image/png
 *                       [1] => image/jpeg
 *                   )
 *               [error] => Array
 *                   (
 *                       [0] => 0
 *                       [1] => 0
 *                   )
 *               [size] => Array
 *                   (
 *                       [0] => 123
 *                       [1] => 456
 *                   )
 *           )
 *   )
 *
 * ---------------------------------
 * Whereas we'd want something like:
 * ---------------------------------
 *
 *   Array
 *   (
 *       [images] => Array
 *           (
 *               [0] => Array
 *                   (
 *                       [name] => foo.txt
 *                       [tmp_name] => 123.tmp
 *                       [type] => image/png
 *                       [error] => 0
 *                       [size] => 123
 *                   )
 *               [1] => Array
 *                   (
 *                       [name] => bar.txt
 *                       [tmp_name] => 456.tmp
 *                       [type] => image/jpeg
 *                       [error] => 0
 *                       [size] => 456
 *                   )
 *           )
 *   )
 *
 * That's what this function does. It converts from the former structure
 * to the latter.
 *
 * @author  Ionut G. Stan
 * @license http://www.opensource.org/licenses/bsd-license.php
 * @return  array
 */
function normalizeFilesSuperglobal() {
    $files = array();

    foreach ($_FILES as $name => $file) {
        if (is_array($file['name'])) {
            foreach ($file['name'] as $i => $value) {
                $files[$name][] = array(
                    'name'     => $value,
                    'type'     => $file['type'][$i],
                    'tmp_name' => $file['tmp_name'][$i],
                    'error'    => $file['error'][$i],
                    'size'     => $file['size'][$i],
                );
            }
        } else {
            $files[$name] = $file;
        }
    }

    return $files;
}
