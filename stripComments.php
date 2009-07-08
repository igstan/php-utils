<?php

/**
 * Removes comment declarations from a string of PHP source code.
 *
 * @author  Ionut G. Stan
 * @license http://www.opensource.org/licenses/bsd-license.php
 * @param   string $source PHP source code
 * @return  string
 */
function stripComments($source) {
    $strippedSource = '';
    $commentTokens  = array(T_COMMENT);

    if (defined('T_DOC_COMMENT')) $commentTokens[] = T_DOC_COMMENT; // PHP 5
    if (defined('T_ML_COMMENT'))  $commentTokens[] = T_ML_COMMENT;  // PHP 4

    $tokens = token_get_all($source);

    foreach ($tokens as $token) {
        if (is_array($token)) {
            if (in_array($token[0], $commentTokens)) {
                continue; // skip comment declarations
            }

            $token = $token[1];
        }

        $strippedSource .= $token;
    }

    return $strippedSource;
}
