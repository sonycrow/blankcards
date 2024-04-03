<?php
declare(strict_types=1);

class Downloader extends SectionController
{
    public function getRenderVars(): array
    {
        $dir = "C:\\Personal\\Heroes of Normandie\\Battle Pack Battle for Caen\\tableros\\";

        $url = "https://www.devil-pig-games.com/wp-content/uploads/2022/05/";
        $img = array(
            'HoN-Product_BP-02_Terrains_Boards_EC-2-.jpg',
            'HoN-Product_BP-02_Terrains_Boards_EC-10-.jpg'
        );

        foreach ($img as $i)
        {
            file_put_contents($dir . $i, file_get_contents($url . $i));
        }

        exit();
    }

}