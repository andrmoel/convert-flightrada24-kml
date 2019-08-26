<?php

namespace App;

class KmlConverter
{
    public function run(): void
    {
        $files = $this->getSourceFiles();

        foreach ($files as $file) {
            $resultFile = __DIR__ . '/../results/foo.kml'; // TODO
            $this->convertSourceFile($file, $resultFile);
        }

        echo "JO";
    }

    private function getSourceFiles(): array
    {
        $result = [];

        $sourceDir = __DIR__ . '/../sources/';
        $handle = opendir($sourceDir);

        while ($fileName = readdir($handle)) {
            if (preg_match('/\.kml/i', $fileName)) {
                $result[] = $sourceDir . $fileName;
            }
        }

        return $result;
    }

    private function convertSourceFile(string $sourceFile, string $resultFile): void
    {
        $xml = $this->readXmlFile($sourceFile);

        $trailFolder = $xml->Document->Folder[1];
        $placemarks = $trailFolder->Placemark;
        $pathString = $this->getPathStringFromPlacemarks($placemarks);

        var_dump($pathString);
    }

    private function readXmlFile(string $file): \SimpleXMLElement
    {
        $xmlString = file_get_contents($file);

        return new \SimpleXMLElement($xmlString);
    }

    private function getPathStringFromPlacemarks(\SimpleXMLElement $placemarks): string
    {
        $result = '';

        $key = 0;
        foreach ($placemarks as $placemark) {
            $segment = $this->getPathSegmetStringFromPlacemark($placemark);

            if ($key > 0) {
                $result .= ' ';
                $segment = $this->removeFirstCoordinateFromSegment($segment);
            }

            $result .= $segment;
            $key++;
        }

        return $result;
    }

    private function getPathSegmetStringFromPlacemark(\SimpleXMLElement $placemark): string
    {
        $string = (string) $placemark->MultiGeometry->LineString->coordinates;

        return $string;
    }

    private function removeFirstCoordinateFromSegment(string $segment): string
    {
        return preg_replace('/^.*? /', '', $segment);
    }
}
