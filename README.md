# Slippy

Convert Slippy ZXY tiles to geometries, and vice versa.

---

## Installation

To Do: Publish to Packagist.

Download `src/Slippy.php` file, and `require("Slippy.php")` in your PHP script.

---

## Data Schema

Slippy maps use three numbers to identify a map tile: the zoom level, and the 'x' and 'y' of the tile, counted from the West and North, respectively.

In this Slippy utility class, a tile is represented as a simple array with three integer values: [z, x, y].

Geographic coordinates are also represented as a simple array with two floating-point numbers: [longitude, latitude].

A "bounding box", or "bbox" for short, is a simple array of four floating-point numbers: [westLongitude, southLatitude, eastLongitude, northLatitude].

Further information on coordinates and bounding boxes can be found in the [GeoJSON Standard](https://tools.ietf.org/html/rfc7946).

---

## Example Usage

### Get Slippy Tile of Coordinates

```
$lngLat = [78.9, -123.4];
$desiredTileZoomLevel = 6;
$tile = Slippy::geoToTile($lngLat, $desiredTileZoomLevel);
```

### Get Northwest Coordinates of Slippy Tile

```
$tile = [7, 8, 9];
$nwCorner = Slippy::tileToGeo($tile);
```

### Get Bounding Box of Slippy Tile

```
$tile = [7, 8, 9];
$bbox = Slippy::tileToBbox($tile);
```

### Get Polygon Coordinates of Slippy Tile

```
$tile = [7, 8, 9];
$bounds = Slippy::tileToPoly($tile);
```

### Get the Four Smaller Quadrant Tiles within a Slippy Tile

```
$tile = [7, 8, 9];
$fourSubtiles = Slippy::subtiles ($tile);
```

### Get All the Tiles of a Specified Zoom within a Slippy Tile

```
$bigTile = [0,0,0];
$desiredZoomLevel = 12;
$manyTinyTiles = Slippy::subsubtiles ($bigTile, $desiredZoomLevel);
```

---

## Testing

To Do: Write tests.

```
./vendor/bin/phpunit tests
```
