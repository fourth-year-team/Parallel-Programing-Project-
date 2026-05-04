<?php
// Script to install Sanctum
$command = 'composer update --no-interaction';
$dir = __DIR__;

$process = proc_open($command, [
    0 => ['pipe', 'r'],
    1 => ['pipe', 'w'],
    2 => ['pipe', 'w'],
], $pipes, $dir);

if (is_resource($process)) {
    // Write 'D' (Do not run) to stdin to handle any prompts
    fwrite($pipes[0], "D\n");
    fclose($pipes[0]);
    
    $output = stream_get_contents($pipes[1]);
    $error = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    
    $returnCode = proc_close($process);
    
    echo "Output:\n$output\n";
    echo "Error:\n$error\n";
    echo "Return code: $returnCode\n";
} else {
    echo "Failed to run process\n";
}
?>
