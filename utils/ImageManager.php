<?php
require_once "framework/Configuration.php";


class ImageManager {

    // valide upload -> taille + formats
    public static function validate(array $file) : array {
        $errors = [];

        if (!isset($file["error"]) || $file["error"] !== UPLOAD_ERR_OK) {
                $errors[] = "Erreur lors de l'upload.";
                return $errors;
            }

        $maxMb = (int)Configuration::get("max_upload_mb");
        $maxBytes = $maxMb * 1024 * 1024;
        if($file["size"] > $maxBytes){
            $errors[] = "Fichier trop grand";
        }

        $type = exif_imagetype($file["tmp_name"]);
            if ($type !== IMAGETYPE_JPEG &&
                $type !== IMAGETYPE_PNG &&
                $type !== IMAGETYPE_GIF &&
                $type !== IMAGETYPE_WEBP) {
                $errors[] = "Format non supporté (JPEG, PNG, GIF, WEBP).";
            }

            return $errors;
        }

    // crée image main / thumbnail + retourne le chemin de main    
    public static function save_item_image(array $file, int $itemId, string $originalNameNoExt, int $priority) : string {

        $mainMax = (int)Configuration::get("item_main_max");
        $thumbMax = (int)Configuration::get("item_thumb_max");

        $dir = "uploads/items/" .$itemId;
        if (!is_dir($dir)){
            mkdir($dir,0777,true);
        }
        $name = self::clean_name($originalNameNoExt);
        if ($name == "") $name = "image";
        $name = $name . "_" . $priority;

        $mainPath  = $dir . "/" . $name . ".jpg";
        $thumbPath = $dir . "/" . $name . "_thumbnail.jpg";

        self::resize_and_save_jpg($file["tmp_name"], $mainPath,  $mainMax);
        self::resize_and_save_jpg($file["tmp_name"], $thumbPath, $thumbMax);

        return $mainPath;
    }
    
    private static function clean_name(string $name): string {
        $name = str_replace(" ", "_", $name);
        // garde uniquement a-z A-Z 0-9 _ 
        $name = preg_replace("/[^a-zA-Z0-9_-]/", "_", $name);
        return $name;
    }

    private static function create_image_from_any(string $path) {
        $type = exif_imagetype($path);

        if ($type === IMAGETYPE_JPEG) return imagecreatefromjpeg($path);
        if ($type === IMAGETYPE_PNG)  return imagecreatefrompng($path);
        if ($type === IMAGETYPE_GIF)  return imagecreatefromgif($path);
        if ($type === IMAGETYPE_WEBP && function_exists("imagecreatefromwebp")) {
            return imagecreatefromwebp($path);
        }

        return null;
    }

    public static function resize_and_save_jpg(string $srcPath, string $destPath, int $maxSide): void {
       $src = self::create_image_from_any($srcPath); 
        if ($src === null) {
            throw new Exception("Impossible de lire l'image.");
        }

        $w = imagesx($src);
        $h = imagesy($src);

        $newW = $w;
        $newH = $h;

        if ($w > $maxSide || $h > $maxSide) {
            if ($w >= $h) {
                $newW = $maxSide;
                $newH = (int)round($h * ($maxSide / $w));
            } else {
                $newH = $maxSide;
                $newW = (int)round($w * ($maxSide / $h));
            }
        }

        $dst = imagecreatetruecolor($newW, $newH);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $w, $h);

        imagejpeg($dst, $destPath, 85);
    }

}

