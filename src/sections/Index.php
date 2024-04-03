<?php
declare(strict_types=1);

class Index extends SectionController
{
    public function getRenderVars(): array
    {
        $vars = $this->getRequestVars();

        return [
            "border" => $this->getBorderFiles(),
            "cards"  => $this->getCards($vars),
            "vars"   => $vars
        ];
    }

    protected function getRequestVars(): array
    {
        return array(
            "save"       => boolval($_REQUEST['save'] ?? 0),
            "offset"     => $_REQUEST['offset'] ?? 1,
            "limit"      => $_REQUEST['limit'] ?? 999,
            "number"     => $_REQUEST['number'] ?? null,
            "marginw"    => $_REQUEST['marginw'] ?? 0,
            "marginh"    => $_REQUEST['marginh'] ?? 0,
            "brightness" => $_REQUEST['brightness'] ?? 0,
            "contrast"   => $_REQUEST['contrast'] ?? 0,
            "border"     => $_REQUEST['border'] ?? null,
            "path"       => $_REQUEST['path'] ?? null
        );
    }

    private function getBorderFiles(): array
    {
        $path  = ROOT . "/src/assets/images/border";
        $files = scandir($path);
        $final = array("Sin borde");

        foreach ($files as $filename)
        {
            $ext = pathinfo($path . $filename, PATHINFO_EXTENSION);

            // Comprobamos que la extensión del archivo sea válida
            if ($ext != "png") continue;

            // Añadimos el archivo
            $final[] = $filename;
        }

        return $final;
    }

    protected function getCards(array $vars): array
    {
        $cards = array();
        $count = 0;

        if (empty($vars['path'])) {
            return $cards;
        }

        $validExt = array("jpg", "jpeg", "png", "webp");
        $path     = $vars['path'] . "\\";

        $marginw  = $vars['marginw'];
        $marginh  = $vars['marginh'];
        $limit    = $vars['limit'];
        $save     = $vars['save'];

        $files = scandir($path);

        foreach ($files as $filename)
        {
            $file = $path . $filename;
            $ext  = pathinfo($path . $filename, PATHINFO_EXTENSION);

            // Comprobamos que la extensión del archivo sea válida
            if (!in_array($ext, $validExt)) continue;

            // Incrementamos número
            ++$count;

            // Si existe número, descartamos el que no coincida
            if (!empty($vars['number']) && $vars['number'] != $count) continue;

            // Comprobamos que la carta esta dentro del offset
            if (empty($vars['number']) && $count < $vars['offset']) continue;

            // Márgenes de carta
            $mw = !empty($_REQUEST["mw-{$count}"]) ? intval($_REQUEST["mw-{$count}"]) : null;
            $mh = !empty($_REQUEST["mh-{$count}"]) ? intval($_REQUEST["mh-{$count}"]) : null;
            $mx = !empty($_REQUEST["mx-{$count}"]) ? intval($_REQUEST["mx-{$count}"]) : null;
            $my = !empty($_REQUEST["my-{$count}"]) ? intval($_REQUEST["my-{$count}"]) : null;

            // Rotacion
            $rot = !empty($_REQUEST["rot-{$count}"]) ? floatval(str_replace(",", ".", $_REQUEST["rot-{$count}"])) : null;

            // Carta
            $cards[] = array(
                "number"   => $count,
                "file"     => $file,
                "filename" => $filename,
                "ext"      => $ext,
                "mw"       => $mw,
                "mh"       => $mh,
                "mx"       => $mx,
                "my"       => $my,
                "rot"      => $rot ?? str_replace(".", ",", (string)$rot),
                "image"    => $vars['number'] ?
                              $this->getRawImage(
                                        $vars['border'],
                                        $file,
                                        intval($marginw),
                                        intval($marginh),
                                        intval($vars['brightness']),
                                        intval($vars['contrast']),
                                        array(
                                            "w" => $mw ? $marginw + $mw : null,
                                            "h" => $mh ? $marginh + $mh : null,
                                            "x" => $mx ? $mx * -1 : null,
                                            "y" => $my ? $my * -1 : null,
                                            "r" => $rot ? $rot : 0
                                        ),
                                        boolval($save)
                              ) : null
            );

            // Si existe el numero salimos
            if (!empty($vars['number'])) break;

            // Si ya tenemos el límite salimos
            if (count($cards) >= $limit) break;
        }

        return $cards;
    }

    private function getRawImage(string $borderFile, string $file, int $marginw, int $marginh, int $brightness, int $contrast, array $mcard, bool $save = false): string
    {
        // Información del archivo
		$border = null;
        $info = pathinfo($file);
        $path = $info['dirname'];
        $ext  = $info['extension'];

        // Instanciamos la imagen
        switch (strtolower($ext))
        {
            case "jpg":
            case "jpeg":
                $img = @imagecreatefromjpeg($file);
                break;
            case "png":
                $img = @imagecreatefrompng($file);
                break;
            case "webp":
                $img = @imagecreatefromwebp($file);
                break;
            default:
                throw new Exception("Tipo de fichero desconocido {$file}");
        }

        // Creamos el icono
        /*
        $icon = imagecreatefrompng(ROOT . "/src/assets/images/icon2.png");
        imagecopyresampled($img, $icon,
            10,10,
            0,0,
            72,72,
            72, 72
        );
        imagedestroy($icon);
        */

        // Rotamos la carta
        $imgr = imagerotate($img, $mcard['r'], 0);

        // Aplicamos el brillo a la carta
        $this->brightnessImage($imgr, $brightness);

        // Aplicamos el contraste a la carta
        $this->contrastImage($imgr, $contrast);

        // Imagen base
        $base = $this->createBaseImage(imagesx($img), imagesy($img), $marginw, $marginh, $mcard);

        // Combinamos la carta original con los margenes
        $this->mergeImage($base, $imgr, $marginw, $marginh, $mcard);

        // Creamos el borde y combinamos
        if (file_exists(ROOT . "/src/assets/images/border/{$borderFile}"))
        {
            $border = imagecreatefrompng(ROOT . "/src/assets/images/border/{$borderFile}");
            $this->mergeImageResize($base, $border);
        }

        // Obtenemos la imagen
        ob_start();
        imagepng($base);
        $rawimage = ob_get_contents(); // read from buffer
        ob_end_clean(); // delete buffer

        // Eliminamos todas las imagenes
        if ($border) imagedestroy($border);
        imagedestroy($base);
        imagedestroy($imgr);
        imagedestroy($img);

        // Guardamos
        if ($save) {
            // Le quitamos la extensión
            $finalFilename = $info['filename'] . ".png";

            @mkdir($path . "/border");
            file_put_contents($path . "/border/b_{$finalFilename}", $rawimage);
        }

        return base64_encode($rawimage);
    }

    private function createBaseImage(int $w, int $h, int $marginw, int $marginh, array $mcard)
    {
        $image = imagecreatetruecolor(
            $w + (!empty($mcard['w']) ? $mcard['w'] : $marginw) * 2,
            $h + (!empty($mcard['h']) ? $mcard['h'] : $marginh) * 2
        );
        imagealphablending($image, true);
        imagesavealpha($image, true);

        return $image;
    }

    private function brightnessImage($image, int $value)
    {
        if ($value == 0) return;
        imagefilter($image, IMG_FILTER_BRIGHTNESS, $value);
    }

    private function contrastImage($image, int $value)
    {
        if ($value == 0) return;
        imagefilter($image, IMG_FILTER_CONTRAST, $value);
    }

    private function mergeImage($baseImage, $image, int $marginw, int $marginh, array $mcard, int $alpha = 100)
    {
        imagecopymerge($baseImage, $image,
            !empty($mcard['w']) ? $mcard['w'] : $marginw,
            !empty($mcard['h']) ? $mcard['h'] : $marginh,
            !empty($mcard['x']) ? $mcard['x'] : 0,
            !empty($mcard['y']) ? $mcard['y'] : 0,
            imagesx($image),
            imagesy($image),
            $alpha
        );
    }

    private function mergeImageResize($baseImage, $image)
    {
        imagecopyresampled($baseImage, $image,
            0,0,
            0,0,
            imagesX($baseImage),
            imagesY($baseImage),
            imagesX($image),
            imagesY($image)
        );
    }

}