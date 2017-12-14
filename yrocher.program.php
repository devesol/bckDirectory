<?php

/*
 * Pour appeler ce prgramme en ligne de commande :
 * php yrocher.program.php chemin_absolu_du_repertoire_a_copier chemin_absolu_du_repertoire_destination liste_des_extensions_avec_separateur_virgule
 * ex : php yrocher.program.php "C:\\tmp_test\\envT\\" "C:\\tmp_test\\copyFile\\"  "pdf,js"
 * 
 */

$originDir = $argv[1];
$destDir = $argv[2];
$strExtension = $argv[3];
$arrayExtension = explode(",", $strExtension);

print "originDir" . $originDir . PHP_EOL;
print "destDir" . $destDir . PHP_EOL;
print "strExtension" . $strExtension . PHP_EOL;

//APPEL DE FONCTION // 

deleteDirectory($destDir);
mkdir($destDir);
browseDirectory($originDir);

//FONCTION// 

function isFileExtensionInArrayExtension($file) {
    $strExtension = pathinfo($file)['extension'];
    $valueToReturn = false;
    foreach ($GLOBALS['arrayExtension'] as $extensionInArray) {
        if ($strExtension === $extensionInArray) {
            $valueToReturn = true;
        }
    }
    return $valueToReturn;
}

function browseDirectory($dirToScan) {
    //print $dirToScan.PHP_EOL;
    foreach (scandir($dirToScan) as $file) {
        $filePath = $dirToScan . "\\" . $file;
        if ($file != '.' && $file != '..') {
            if (is_dir($filePath)) {
                makeDirectory($filePath);
                browseDirectory($filePath);
            } else {
                if (isFileExtensionInArrayExtension($filePath)) {
                    copyPasteFile($filePath);
                }
            }
        }
    }
}

function copyPasteFile($originDirToCopyAbsolutePath) {
    $fileName = basename($originDirToCopyAbsolutePath); // Nom du fichier avec extension
    $dirPath = $GLOBALS['destDir'] . getRelativePath($originDirToCopyAbsolutePath); // chemin du nouveau dossier
    $newPathFile = $dirPath . "\\" . $fileName;
    if (is_dir($originDirToCopyAbsolutePath)) {
        makeDirectory($originDirToCopyAbsolutePath);
    } else {
        copy($originDirToCopyAbsolutePath, $newPathFile);
    }
}

function getStrWithoutLastBackslash($originDirToCopyAbsolutePath) {
    $lastPosOfBackslash = strrpos($originDirToCopyAbsolutePath, "\\");
    $valueToReturn = substr($originDirToCopyAbsolutePath, 0, $lastPosOfBackslash);
    return $valueToReturn;
}

function getRelativePath($originDirToCopyAbsolutePath) {
    $longueurDuCheminDuRepertoireDorigineACopier = strlen($GLOBALS['originDir']);
    $longueurDuCheminDuRepertoireCourantACopier = strlen($originDirToCopyAbsolutePath);
    $valueToReturn = substr($originDirToCopyAbsolutePath, $longueurDuCheminDuRepertoireDorigineACopier, $longueurDuCheminDuRepertoireCourantACopier);
    $posOfLastBackslash = strrpos($originDirToCopyAbsolutePath, "\\");
    if (!is_dir($originDirToCopyAbsolutePath)) {
        $valueToReturn = getStrWithoutLastBackslash($valueToReturn);
    } else if ($posOfLastBackslash == ($longueurDuCheminDuRepertoireCourantACopier - 1)) {
        $valueToReturn = getStrWithoutLastBackslash($valueToReturn);
    }
    return $valueToReturn;
}

function makeDirectory($originDirToCopyAbsolutePath) {
    $dirToMake = $GLOBALS['destDir'] . getRelativePath($originDirToCopyAbsolutePath);
    if (!file_exists($dirToMake)) {
        mkdir($dirToMake, 0777, TRUE);
    }
}

function deleteDirectory($dir) {
    if (!file_exists($dir))
        return true;
    if (!is_dir($dir))
        return unlink($dir);
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..')
            continue;
        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item))
            return false;
    }
    return rmdir($dir);
}

/*
      function browseDirectory($urlDir) {
      $dir = opendir($urlDir);
      while ($file = readdir($dir)) {
      $strExtension = pathinfo($file);
      if ($strExtension['extension'] === 'txt' || $strExtension['extension'] === 'pdf' || $strExtension['extension'] === 'js' ) {
      copyPasteFile($urlDir.$file);
      }
      }
      closedir($dir);
      }
     */    