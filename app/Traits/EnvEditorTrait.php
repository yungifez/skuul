<?php

namespace App\Traits;

/**
 * Env Editor Trait.
 */
trait EnvEditorTrait
{
    /**
     * Write a new Env value to the env file the app is currently using.
     */
    public function setEnvironmentValue(array $values): bool
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);

        if (count($values) > 0) {
            foreach ($values as $envKey => $envValue) {
                $str .= "\n"; // In case the searched variable is in the last line without \n
                $keyPosition = strpos($str, "{$envKey}=");
                $endOfLinePosition = strpos($str, "\n", $keyPosition);
                $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);

                // If environment variable does not exist, add it
                if ($keyPosition === false || !$endOfLinePosition || !$oldLine) {
                    $str .= "{$envKey}=\"{$envValue}\"\n";
                } else {
                    //else replace it
                    $str = str_replace($oldLine, "{$envKey}=\"{$envValue}\"", $str);
                }
            }
        }

        $str = trim($str);
        if (!file_put_contents($envFile, $str)) {
            return false;
        }

        return true;
    }
}
